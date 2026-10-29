<?php
// Приём заявок с сайта на почту. Положите файл рядом с index.html на хостинг с PHP.
$to = 'ВАШ_EMAIL@example.com'; // <-- сюда будут приходить заявки

header('Content-Type: application/json; charset=utf-8');

function fail($code) { http_response_code($code); echo json_encode(['ok' => false]); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !empty($_POST['_gotcha'])) fail(400);

function clean($v) { return trim(strip_tags((string)$v)); }
$name  = clean($_POST['name']  ?? '');
$phone = clean($_POST['phone'] ?? '');
$task  = clean($_POST['task']  ?? '');
if ($name === '' || $phone === '') fail(422);

$host    = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
$subject = '=?UTF-8?B?' . base64_encode('Новая заявка с сайта') . '?=';
$body    = "Имя: $name\nТелефон: $phone\n\nЗаявка:\n$task\n";
$headers = "From: noreply@$host\r\nContent-Type: text/plain; charset=utf-8\r\n";

if (!mail($to, $subject, $body, $headers)) fail(500);
echo json_encode(['ok' => true]);
