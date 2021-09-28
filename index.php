<?php
require_once './settings/connect.php';
require_once './settings/functions.php';
$islem = isset($_GET["islem"]) ? addslashes(trim($_GET["islem"])) : null;
$jsonArray = array(); // array değişkenimiz bunu en alta json objesine çevireceğiz. 
$jsonArray['error'] = FALSE; // Başlangıçta hata yok olarak kabul edelim. 
$_code = 200; // HTTP Ok olarak durumu kabul edelim. 
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === "GET") {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query = $db->prepare("SELECT * FROM users WHERE id = :id");
        $query->bindParam(":id", $id, PDO::PARAM_INT);
        $query->execute();
        $query2 = $query->fetchAll(PDO::FETCH_OBJ);
        $jsonArray["User information"] = $query2;
        $_code = 200;
    } else {
        $query = $db->prepare("SELECT * FROM users");
        $query->execute();
        $query2 = $query->fetchAll(PDO::FETCH_OBJ);
        $jsonArray["User information"] = $query2;
        $_code = 200;
    }
} else if ($request_method === "POST") {
    $firstName = Security($_POST['first_name']);
    $lastName  = Security($_POST['last_name']);
    $age       = Security($_POST['age']);
    $city      = Security($_POST['city']);
    $ip        = Security($_POST['ip']);
    $add = $db->prepare("INSERT INTO users (first_name, last_name, age, city, ip) VALUES (:fname, :lname, :age, :city, :ip)");
    $add->bindParam(":fname", $firstName, PDO::PARAM_STR);
    $add->bindParam(":lname", $lastName, PDO::PARAM_STR);
    $add->bindParam(":age", $age, PDO::PARAM_INT);
    $add->bindParam(":city", $city, PDO::PARAM_STR);
    $add->bindParam(":ip", $ip, PDO::PARAM_STR);
    $add->execute();
    if ($db->lastInsertId()) {
        $jsonArray["send_data"] = "Data sending is successful";
        $jsonArray['first_name'] = $firstName;
        $jsonArray['last_name'] = $lastName;
        $jsonArray['age'] = $age;
        $jsonArray['city'] = $city;
        $jsonArray['ip'] = $ip;
    } else {
        $jsonArray['error'] = true;
        $_code = 404;
        $jsonArray['errorMessage'] = "Data sending failed!";
    }
} else if ($request_method === "DELETE") {
    $id = Security($_GET['id']);
    $delete = $db->prepare("DELETE FROM users WHERE id = :id");
    $delete->bindParam(":id", $id, PDO::PARAM_INT);
    $delete->execute();
    if ($delete) {
        $jsonArray['message'] = "Deletion successful";
        $jsonArray['affectedId'] = $id;
    } else {
        $jsonArray['error'] = true;
        $jsonArray['deleteid'] = $id;
        $_code = 404;
        $jsonArray['errorMessage'] = "Deletion failed.";
    }
} else {
    $jsonArray['error'] = true;
    $_code = 404;
    $jsonArray['errorMessage'] = "Invalid request or undefined method";
}


SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
