<?php
header('Content-Type: application/json');
$cep = preg_replace('/\D/', '', $_GET['cep'] ?? '');
if (strlen($cep) !== 8) { echo json_encode(['erro' => true]); exit; }
$url  = "https://viacep.com.br/ws/{$cep}/json/";
$resp = @file_get_contents($url);
echo $resp ?: json_encode(['erro' => true]);