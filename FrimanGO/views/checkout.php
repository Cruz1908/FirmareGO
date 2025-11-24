<?php
/**
 * Página de checkout con integración Stripe
 */
$pageTitle = 'Pagament';
$cart = Cart::get();
$total = Cart::getTotal();

if (empty($cart)) {
    header('Location: /cart');
    exit;
}

$error = $_GET['error'] ?? null;
?>

<div class="container" style="padding: 40px 20px;">
  <h1 class="page-title"><?= Lang::get('checkout.title') ?></h1>
  
  <?php if ($error): ?>
    <div class="alert alert-error" style="background: #fee; border: 1px solid #fcc; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #c33;">
      <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <div class="checkout-layout">
    <div class="checkout-form-section">
      <form method="POST" action="/api/checkout.php" class="checkout-form" id="checkout-form">
        <h2><?= Lang::get('checkout.contact') ?></h2>
      <div class="form-grid">
          <div class="form-group">
            <label class="label"><?= Lang::get('checkout.full_name') ?></label>
            <input type="text" name="name" class="input" required value="<?= htmlspecialchars(Auth::isLoggedIn() ? Auth::getCurrentUser()['name'] : '') ?>" />
          </div>
          <div class="form-group">
            <label class="label"><?= Lang::get('checkout.email') ?></label>
            <input type="email" name="email" class="input" required value="<?= htmlspecialchars(Auth::isLoggedIn() ? Auth::getCurrentUser()['email'] : '') ?>" />
        </div>
          <div class="form-group">
            <label class="label"><?= Lang::get('checkout.phone') ?></label>
            <input type="tel" name="phone" class="input" required />
        </div>
        </div>
        
        <h2><?= Lang::get('checkout.delivery') ?></h2>
        <div class="form-grid">
          <div class="form-group" style="grid-column: 1 / -1;">
            <label class="label"><?= Lang::get('checkout.address') ?></label>
            <input type="text" name="address" class="input" required />
        </div>
          <div class="form-group">
            <label class="label"><?= Lang::get('checkout.city') ?></label>
            <input type="text" name="city" class="input" required />
        </div>
          <div class="form-group">
            <label class="label"><?= Lang::get('checkout.postal') ?></label>
            <input type="text" name="postal_code" class="input" required />
        </div>
        </div>
        
        <h2><?= Lang::get('checkout.payment') ?></h2>
        <div class="payment-methods">
          <label class="payment-option">
            <input type="radio" name="payment_method" value="stripe" checked id="payment-stripe" />
            <span><?= Lang::get('checkout.card') ?></span>
          </label>
          <label class="payment-option">
            <input type="radio" name="payment_method" value="cash" id="payment-cash" />
            <span><?= Lang::get('checkout.cash') ?></span>
          </label>
        </div>
        
        <!-- Stripe Elements -->
        <div id="stripe-card-element" style="margin-top: 16px;">
          <label class="label"><?= Lang::get('checkout.card_data') ?></label>
          <div id="card-element" style="padding: 12px; border: 1px solid var(--color-border); border-radius: 8px; margin-top: 8px; background: #fff;"></div>
          <div id="card-errors" role="alert" style="color: var(--color-danger); margin-top: 8px; font-size: 14px;"></div>
        </div>
        
        <input type="hidden" name="payment_intent_id" id="payment-intent-id" />
        
        <button type="submit" class="primary checkout-submit" id="submit-button"><?= Lang::get('checkout.complete') ?></button>
        <div id="loading" style="display: none; text-align: center; padding: 16px; color: var(--color-text-muted);">
          <?= Lang::get('checkout.processing') ?>
        </div>
      </form>
      </div>

    <div class="checkout-summary">
      <div class="summary-card">
        <h3><?= Lang::get('checkout.order_summary') ?></h3>
        <div class="summary-items">
          <?php foreach ($cart as $item): ?>
          <div class="summary-item">
            <span><?= htmlspecialchars(Lang::getProductName($item)) ?> × <?= $item['quantity'] ?></span>
            <span><?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?> €</span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="summary-total">
          <span><?= Lang::get('cart.total') ?></span>
          <strong><?= number_format($total, 2, ',', '.') ?> €</strong>
        </div>
      </div>
      </div>
  </div>
</div>

<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const stripe = Stripe('<?= STRIPE_PUBLIC_KEY ?>');
  const form = document.getElementById('checkout-form');
  const submitButton = document.getElementById('submit-button');
  const loadingDiv = document.getElementById('loading');
  const cardElementDiv = document.getElementById('card-element');
  const cardErrorsDiv = document.getElementById('card-errors');
  const stripeCardDiv = document.getElementById('stripe-card-element');
  const paymentStripe = document.getElementById('payment-stripe');
  const paymentCash = document.getElementById('payment-cash');
  let cardElement = null;
  let paymentIntentClientSecret = null;

  // Toggle visibilidad de Stripe Elements
  function toggleStripeElements() {
    if (paymentStripe.checked) {
      stripeCardDiv.style.display = 'block';
      if (!cardElement) {
        initStripe();
      }
    } else {
      stripeCardDiv.style.display = 'none';
    }
  }

  paymentStripe.addEventListener('change', toggleStripeElements);
  paymentCash.addEventListener('change', toggleStripeElements);

  // Inicializar Stripe Elements
  async function initStripe() {
    try {
      // Crear Payment Intent
      const response = await fetch('/api/payment/create-intent.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      });
      
      const data = await response.json();
      
      if (!data.success) {
        throw new Error(data.error || 'Error al crear pago');
      }
      
      paymentIntentClientSecret = data.client_secret;
      document.getElementById('payment-intent-id').value = data.payment_intent_id;
      
      // Crear elementos de tarjeta
      const elements = stripe.elements();
      cardElement = elements.create('card', {
        style: {
          base: {
            fontSize: '16px',
            color: '#424770',
            '::placeholder': {
              color: '#aab7c4',
            },
          },
        },
      });
      
      cardElement.mount(cardElementDiv);
      
      cardElement.on('change', ({error}) => {
        if (error) {
          cardErrorsDiv.textContent = error.message;
        } else {
          cardErrorsDiv.textContent = '';
        }
      });
      
    } catch (error) {
      console.error('Error:', error);
      cardErrorsDiv.textContent = error.message;
    }
  }

  // Manejar envío del formulario
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (paymentStripe.checked) {
      // Pago con tarjeta
      if (!cardElement || !paymentIntentClientSecret) {
        alert('Error: Stripe no está inicializado correctamente');
        return;
      }
      
      submitButton.disabled = true;
      loadingDiv.style.display = 'block';
      
      try {
        const {error, paymentIntent} = await stripe.confirmCardPayment(paymentIntentClientSecret, {
          payment_method: {
            card: cardElement,
            billing_details: {
              name: form.name.value,
              email: form.email.value,
              phone: form.phone.value,
            }
          }
        });
        
        if (error) {
          cardErrorsDiv.textContent = error.message;
          submitButton.disabled = false;
          loadingDiv.style.display = 'none';
          return;
        }
        
        if (paymentIntent.status === 'succeeded') {
          // Pago exitoso, enviar formulario
          form.submit();
        }
      } catch (error) {
        console.error('Error:', error);
        cardErrorsDiv.textContent = 'Error al procesar el pago';
        submitButton.disabled = false;
        loadingDiv.style.display = 'none';
      }
    } else {
      // Pago en efectivo
      submitButton.disabled = true;
      loadingDiv.style.display = 'block';
      form.submit();
    }
  });
  
  // Inicializar Stripe si está seleccionado por defecto
  if (paymentStripe.checked) {
    initStripe();
    }
  });
 </script>

<style>
.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 32px;
  align-items: start;
}

.checkout-form {
  background: #fff;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 32px;
}

.checkout-form h2 {
  font-size: 20px;
  font-weight: 700;
  margin: 32px 0 20px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--color-border);
}

.checkout-form h2:first-child {
  margin-top: 0;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  margin-bottom: 16px;
}

.label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: var(--color-dark);
  font-size: 14px;
}

.input {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 16px;
}

.input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(255, 210, 0, 0.1);
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 32px;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  cursor: pointer;
}

.payment-option:hover {
  background: #f9fafb;
}

.payment-option input[type="radio"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.checkout-submit {
  width: 100%;
  background: var(--color-primary);
  color: var(--color-dark);
  border: 0;
  border-radius: 8px;
  padding: 16px;
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
  margin-top: 20px;
}

.checkout-submit:hover {
  background: var(--color-primary-dark);
}

.checkout-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.summary-card {
  background: #fff;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 24px;
  position: sticky;
  top: 100px;
}

.summary-card h3 {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 20px;
}

.summary-items {
  margin-bottom: 20px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
  font-size: 14px;
}

.summary-item:last-child {
  border-bottom: none;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  padding-top: 16px;
  border-top: 2px solid var(--color-border);
  font-size: 20px;
  font-weight: 700;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 32px;
}

@media (max-width: 900px) {
  .checkout-layout {
    grid-template-columns: 1fr;
  }
  
  .summary-card {
    position: static;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
