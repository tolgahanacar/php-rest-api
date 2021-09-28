<?php
require_once './controller/connect.php';
require_once './controller/functions.php';
$jsonArray = array();
$jsonArray['error'] = FALSE;
$_code = 200;
$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method === "GET") { //GET Method
    if (isset($_GET['id'])) {
        $id = Security($_GET['id']);
        if (empty($id)) {
            $jsonArray['error'] = true;
            $jsonArray['errorMessage'] = "Invalid or null value!";
            $_code = 403;
        } else if (!is_numeric($id)) {
            $jsonArray['error'] = true;
            $jsonArray['errorMessage'] = "The request must be 'numeric'";
            $_code = 403;
        } else {
            $control = $db->prepare("SELECT * FROM users WHERE id = :id");
            $control->bindParam(":id", $id, PDO::PARAM_INT);
            $control->execute();
            $controlCount = $control->rowCount();
            if ($controlCount > 0) {
                $query = $db->prepare("SELECT * FROM users WHERE id = :id");
                $query->bindParam(":id", $id, PDO::PARAM_INT);
                $query->execute();
                $query2 = $query->fetchAll(PDO::FETCH_OBJ);
                $jsonArray["User information"] = $query2;
            } else {
                $jsonArray['error'] = true;
                $jsonArray['errorMessage'] = "No value found for your request!";
                $_code = 403;
            }


            $_code = 200;
        }
    } else {
        $query = $db->prepare("SELECT * FROM users");
        $query->execute();
        $query2 = $query->fetchAll(PDO::FETCH_OBJ);
        $jsonArray["User information"] = $query2;
        $_code = 200;
    }
} else if ($request_method === "POST") { //POST Method
    $firstName = Security($_POST['first_name']);
    $lastName  = Security($_POST['last_name']);
    $age       = Security($_POST['age']);
    $city      = Security($_POST['city']);
    $ip        = Security($_POST['ip']);
    if (empty($firstName) || empty($lastName) || empty($age) || empty($city) || empty($ip)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "Invalid or null value!";
        $_code = 403;
    } else if (is_numeric($firstName) || is_numeric($lastName)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "first or last name cannot contain numeric values.";
        $_code = 403;
    } else {
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
    }
} else if ($request_method === "DELETE") { //DELETE Method
    $id = Security($_GET['id']);
    if (empty($id)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "Invalid or null value!";
        $_code = 403;
    } else if (!is_numeric($id)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "The request must contain a numeric value!";
        $_code = 403;
    } else {
        $control = $db->prepare("SELECT * FROM users WHERE id = :id");
        $control->bindParam(":id", $id, PDO::PARAM_INT);
        $control->execute();
        $controlCount = $control->rowCount();
        if ($controlCount > 0) {
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
            $jsonArray['errorMessage'] = "No value found for your request!";
            $_code = 403;
        }
    }
} else if ($request_method === "PUT") {
    $put_req = json_decode(file_get_contents("php://input"));
    $id = $put_req->id;
    $firstName = $put_req->first_name;
    $lastName  = $put_req->last_name;
    $age = $put_req->age;
    $city = $put_req->city;
    $ip   = $put_req->ip;
    if (empty($id) || empty($firstName) || empty($lastName) || empty($age) || empty($city) || empty($ip)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "Invalid or null value!";
        $_code = 403;
    } else if (is_numeric($firstName) || is_numeric($lastName)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "first or last name cannot contain numeric values.";
        $_code = 403;
    } else {
        $control = $db->prepare("SELECT * FROM users WHERE id = :id");
        $control->bindParam(":id", $id, PDO::PARAM_INT);
        $control->execute();
        $controlCount = $control->rowCount();
        if ($controlCount > 0) {
            $update = $db->prepare("UPDATE users SET first_name = :fname, last_name = :lname, age = :age, city = :city, ip = :ip WHERE id = :id");
            $update->bindParam(":fname", $firstName, PDO::PARAM_STR);
            $update->bindParam(":lname", $lastName, PDO::PARAM_STR);
            $update->bindParam(":age", $age, PDO::PARAM_INT);
            $update->bindParam(":city", $city, PDO::PARAM_STR);
            $update->bindParam(":ip", $ip, PDO::PARAM_STR);
            $update->bindParam("id", $id, PDO::PARAM_INT);
            $update->execute();
            if ($update) {
                $jsonArray['message'] = "Update successfull.";
                $jsonArray['affectedId'] = $id;
            } else {
                $jsonArray['error'] = true;
                $_code = 404;
                $jsonArray['errorMessage'] = "Data sending failed!";
            }
        }
    }
} else {
    $jsonArray['error'] = true;
    $_code = 404;
    $jsonArray['errorMessage'] = "Invalid request or undefined method";
}


SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
