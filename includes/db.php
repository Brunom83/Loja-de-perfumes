<?php
// db.php

$host = 'sql113.infinityfree.com';
$dbname = 'if0_39079212_perfumes_verdes';
$username = 'if0_39079212';
$password = 'DB2whlba1I';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com a base de dados: " . $e->getMessage());
}
