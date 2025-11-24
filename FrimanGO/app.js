/**
 * FrimanGO - JavaScript principal
 * Funcionalidades del frontend
 */

// Actualizar contador del carrito periódicamente
function updateCartCount() {
  fetch('/api/cart-count.php')
    .then(r => r.json())
    .then(data => {
      const countEl = document.getElementById('cart-count');
      if (countEl) {
        countEl.textContent = data.count || 0;
}
    })
    .catch(err => console.error('Error al actualizar carrito:', err));
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  
  // Actualizar cada 3 segundos
  setInterval(updateCartCount, 3000);
  
  // Toggle del menú lateral
  const menuToggle = document.querySelector('.menu-toggle');
  const sideMenu = document.getElementById('side-menu');
  const menuClose = document.querySelector('.menu-close');
  
  if (menuToggle && sideMenu) {
    menuToggle.addEventListener('click', () => {
      sideMenu.classList.remove('hidden');
      // Crear backdrop
      const backdrop = document.createElement('div');
      backdrop.className = 'menu-backdrop active';
      backdrop.addEventListener('click', () => {
        sideMenu.classList.add('hidden');
        backdrop.remove();
      });
      document.body.appendChild(backdrop);
    });
  }
  
  if (menuClose && sideMenu) {
    menuClose.addEventListener('click', () => {
      sideMenu.classList.add('hidden');
      const backdrop = document.querySelector('.menu-backdrop');
      if (backdrop) backdrop.remove();
    });
  }
  
  // Efecto zoom moderno y sutil solo en página principal (no en admin)
  // Verificar que no estamos en admin
  if (!window.location.pathname.includes('/admin')) {
    const productImages = document.querySelectorAll('.card-image img, .product-image img');
    productImages.forEach(img => {
      const container = img.closest('.card-image') || img.closest('.product-image');
      if (!container) return;
      
      // Agregar clase para estilos CSS
      container.classList.add('image-zoom-container');
      img.style.cursor = 'zoom-in';
      img.style.willChange = 'transform';
      
      // Efecto hover sutil en desktop
      if (window.innerWidth >= 768) {
        container.addEventListener('mouseenter', function() {
          img.style.transform = 'scale(1.08)';
          img.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        });
        
        container.addEventListener('mouseleave', function() {
          img.style.transform = 'scale(1)';
          img.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        });
      }
      
      // Click/touch para modal de zoom moderno
      img.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Crear overlay con animación suave
        const overlay = document.createElement('div');
        overlay.className = 'image-zoom-overlay';
        overlay.style.cssText = `
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.95);
          z-index: 10000;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: zoom-out;
          padding: 20px;
          opacity: 0;
          transition: opacity 0.3s ease;
        `;
        
        const zoomImg = document.createElement('img');
        zoomImg.src = this.src;
        zoomImg.alt = this.alt || 'Imagen del producto';
        zoomImg.style.cssText = `
          max-width: 90%;
          max-height: 90%;
          object-fit: contain;
          border-radius: 12px;
          box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
          transform: scale(0.8);
          transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        
        overlay.appendChild(zoomImg);
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        
        // Animar entrada
        requestAnimationFrame(() => {
          overlay.style.opacity = '1';
          zoomImg.style.transform = 'scale(1)';
        });
        
        // Cerrar al hacer click
        overlay.addEventListener('click', function() {
          overlay.style.opacity = '0';
          zoomImg.style.transform = 'scale(0.8)';
          setTimeout(() => {
            if (overlay.parentNode) {
              document.body.removeChild(overlay);
              document.body.style.overflow = '';
            }
          }, 300);
        });
        
        // Cerrar con ESC
        const closeOnEsc = function(e) {
          if (e.key === 'Escape') {
            overlay.style.opacity = '0';
            zoomImg.style.transform = 'scale(0.8)';
            setTimeout(() => {
              if (overlay.parentNode) {
                document.body.removeChild(overlay);
                document.body.style.overflow = '';
              }
              document.removeEventListener('keydown', closeOnEsc);
            }, 300);
          }
        };
        document.addEventListener('keydown', closeOnEsc);
      });
    });
  }
  
  // Funcionalidad de búsqueda
  const searchInput = document.getElementById('search-input');
  const searchBtn = document.querySelector('.search-btn');
  
  if (searchInput && searchBtn) {
    const performSearch = () => {
      const query = searchInput.value.trim();
      if (query) {
        window.location.href = `/category?search=${encodeURIComponent(query)}`;
      }
    };
    
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
    }
  });
}

  // Botón del carrito
  const cartButton = document.getElementById('cart-button');
  if (cartButton) {
    cartButton.addEventListener('click', () => {
      window.location.href = '/cart';
    });
  }
});

// Función helper para agregar al carrito (puede ser llamada desde cualquier página)
async function addToCart(productId, quantity = 1) {
  try {
    const response = await fetch('/api/cart-add.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId, quantity: quantity })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Mostrar notificación
      showNotification('Producto añadido al carrito', 'success');
      // Actualizar contador
      updateCartCount();
      return true;
  } else {
      showNotification('Error al agregar el producto', 'error');
      return false;
    }
  } catch (err) {
    console.error('Error:', err);
    showNotification('Error de conexión', 'error');
    return false;
  }
}

// Notificaciones simples
function showNotification(message, type = 'info') {
  // Crear elemento de notificación
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    animation: slideIn 0.3s ease;
  `;
  
  document.body.appendChild(notification);
  
  // Remover después de 3 segundos
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Agregar estilos de animación si no existen
if (!document.getElementById('notification-styles')) {
  const style = document.createElement('style');
  style.id = 'notification-styles';
  style.textContent = `
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
  }
    }
    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(100%);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(style);
}
