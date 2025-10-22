<?php
/**
 * config.php
 * Ustawienia połączenia z bazą danych MySQL za pomocą PDO.
 */

$host = 'localhost';
$dbname = 'planjazd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd połączenia z bazą danych.']);
    exit;
}
?>