<?php 
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
logoutCliente();
flash('info', 'Você saiu da sua conta.');
redirect(APP_URL);