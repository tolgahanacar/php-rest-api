<?php
try{
    $db = new PDO("mysql:host=localhost;dbname=rest-api;charset=UTF8","tolga","123456");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    die($e->getMessage());
}