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
            $_code = 406;
        } else if (!is_numeric($id)) {
            $jsonArray['error'] = true;
            $jsonArray['errorMessage'] = "The request must be 'numeric'";
            $_code = 406;
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
                $_code = 404;
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
    $userName  = Security($_POST['username']);
    $firstName = Security($_POST['first_name']);
    $lastName  = Security($_POST['last_name']);
    $email     = Security($_POST['email']);
    if (empty($userName) || empty($firstName) || empty($lastName) || empty($email)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "Invalid or null value!";
        $_code = 406;
    } else if (is_numeric($firstName) || is_numeric($lastName)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "first or last name cannot contain numeric values.";
        $_code = 406;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "invalid email address";
        $_code = 406;
    } else {
        $add = $db->prepare("INSERT INTO users (username, first_name, last_name, email) VALUES (:uname, :fname, :lname, :email)");
        $add->bindParam(":uname", $userName, PDO::PARAM_STR);
        $add->bindParam(":fname", $firstName, PDO::PARAM_STR);
        $add->bindParam(":lname", $lastName, PDO::PARAM_STR);
        $add->bindParam(":email", $email, PDO::PARAM_STR);
        $add->execute();
        if ($db->lastInsertId()) {
            $jsonArray["send_data"] = "Data sending is successful";
            $jsonArray['username'] = $userName;
            $jsonArray['first_name'] = $firstName;
            $jsonArray['last_name'] = $lastName;
            $jsonArray['email'] = $email;
        } else {
            $jsonArray['error'] = true;
            $_code = 403;
            $jsonArray['errorMessage'] = "Data sending failed!";
        }
    }
} else if ($request_method === "DELETE") { //DELETE Method
    $id = Security($_GET['id']);
    if (empty($id)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "Invalid or null value!";
        $_code = 406;
    } else if (!is_numeric($id)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "The request must contain a numeric value!";
        $_code = 406;
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
                $_code = 403;
                $jsonArray['errorMessage'] = "Deletion failed.";
            }
        } else {
            $jsonArray['error'] = true;
            $jsonArray['errorMessage'] = "No value found for your request!";
            $_code = 404;
        }
    }
} else if ($request_method === "PUT") {
    $put_req = json_decode(file_get_contents("php://input"));
    $id = $put_req->id;
    $userName = $put_req->username;
    $firstName = $put_req->first_name;
    $lastName  = $put_req->last_name;
    $email = $put_req->email;
    if (empty($id) || empty($userName) || empty($firstName) || empty($lastName) || empty($email)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "Invalid or null value!";
        $_code = 406;
    } else if (is_numeric($firstName) || is_numeric($lastName)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "first or last name cannot contain numeric values.";
        $_code = 406;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $jsonArray['error'] = true;
        $jsonArray['errorMessage'] = "invalid email address";
        $_code = 406;
    } else {
        $control = $db->prepare("SELECT * FROM users WHERE id = :id");
        $control->bindParam(":id", $id, PDO::PARAM_INT);
        $control->execute();
        $controlCount = $control->rowCount();
        if ($controlCount > 0) {
            $update = $db->prepare("UPDATE users SET username = :uname, first_name = :fname, last_name = :lname, email = :email WHERE id = :id");
            $update->bindParam(":uname", $userName, PDO::PARAM_STR);
            $update->bindParam(":fname", $firstName, PDO::PARAM_STR);
            $update->bindParam(":lname", $lastName, PDO::PARAM_STR);
            $update->bindParam(":email", $email, PDO::PARAM_STR);
            $update->bindParam("id", $id, PDO::PARAM_INT);
            $update->execute();
            if ($update) {
                $jsonArray['message'] = "Update successfull.";
                $jsonArray['affectedId'] = $id;
            } else {
                $jsonArray['error'] = true;
                $_code = 403;
                $jsonArray['errorMessage'] = "Data sending failed!";
            }
        }
    }
} else {
    $jsonArray['error'] = true;
    $_code = 405;
    $jsonArray['errorMessage'] = "Method Not Allowed";
}


SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
