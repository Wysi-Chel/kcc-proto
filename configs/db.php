<?php
// configs/db.php

$host = 'localhost';
$db   = 'lougeh_db';
$user = 'root';
$pass = '';

$dsn = "mysql:host=localhost;port=3306;dbname=lougeh_db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
