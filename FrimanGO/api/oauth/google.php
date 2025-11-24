<?php
require_once __DIR__ . '/../../config/config.php';

header('Location: ' . OAuth::getGoogleAuthUrl());
exit;


