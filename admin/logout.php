<?php
// admin/logout.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
logoutAdmin();
flash('info','Sessão encerrada.');
redirect(APP_URL . '/admin/index.php');
