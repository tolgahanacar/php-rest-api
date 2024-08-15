<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=rest-api;charset=UTF8", "user", "userpass", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
} catch (PDOException $e) {
    exit($e->getMessage());
}
?>
