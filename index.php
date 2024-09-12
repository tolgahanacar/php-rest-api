<?php

require_once './controller/connect.php';
require_once './controller/functions.php';

$jsonArray = ['error' => false];
$_code = 200;
$request_method = $_SERVER['REQUEST_METHOD'];

function respond_with_error($message, $code) {
    global $jsonArray, $_code;
    $jsonArray['error'] = true;
    $jsonArray['errorMessage'] = $message;
    $_code = $code;
}

function getUserById($id) {
    global $db;
    $query = $db->prepare("SELECT * FROM users WHERE id = :id");
    $query->bindParam(":id", $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_OBJ);
}

if ($request_method === "GET") {

    $id = isset($_GET['id']) ? Security($_GET['id']) : null;

    if ($id !== null) {
        if (empty($id) || !is_numeric($id)) {
            respond_with_error("Invalid or null value!", 406);
        } else {
            $result = getUserById($id);
            if ($result) {
                $jsonArray["User information"] = $result;
            } else {
                respond_with_error("No value found for your request!", 404);
            }
        }
    } else {
        $query = $db->prepare("SELECT * FROM users");
        $query->execute();
        $jsonArray["User information"] = $query->fetchAll(PDO::FETCH_OBJ);
    }

} elseif ($request_method === "POST") {

    $userName  = Security($_POST['username']);
    $firstName = Security($_POST['first_name']);
    $lastName  = Security($_POST['last_name']);
    $email     = Security($_POST['email']);

    if (empty($userName) || empty($firstName) || empty($lastName) || empty($email)) {
        respond_with_error("Invalid or null value!", 406);
    } elseif (is_numeric($firstName) || is_numeric($lastName)) {
        respond_with_error("First or last name cannot contain numeric values.", 406);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond_with_error("Invalid email address.", 406);
    } else {
        $add = $db->prepare("INSERT INTO users (username, first_name, last_name, email) VALUES (:uname, :fname, :lname, :email)");
        $add->bindParam(":uname", $userName, PDO::PARAM_STR);
        $add->bindParam(":fname", $firstName, PDO::PARAM_STR);
        $add->bindParam(":lname", $lastName, PDO::PARAM_STR);
        $add->bindParam(":email", $email, PDO::PARAM_STR);
        $add->execute();

        if ($db->lastInsertId()) {
            $jsonArray = array_merge($jsonArray, [
                "send_data" => "Data sending is successful",
                'username' => $userName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email
            ]);
        } else {
            respond_with_error("Data sending failed!", 403);
        }
    }

} elseif ($request_method === "DELETE") {

    $id = Security($_GET['id']);

    if (empty($id) || !is_numeric($id)) {
        respond_with_error("Invalid or null value!", 406);
    } else {
        $user = getUserById($id);
        if ($user) {
            $delete = $db->prepare("DELETE FROM users WHERE id = :id");
            $delete->bindParam(":id", $id, PDO::PARAM_INT);
            $delete->execute();
            $jsonArray = array_merge($jsonArray, [
                'message' => "Deletion successful",
                'affectedId' => $id
            ]);
        } else {
            respond_with_error("No value found for your request!", 404);
        }
    }

} elseif ($request_method === "PUT") {

    $put_req = json_decode(file_get_contents("php://input"));

    if (json_last_error() !== JSON_ERROR_NONE) {
        respond_with_error("Invalid JSON format", 400);
    }

    $id = $put_req->id;
    $userName = $put_req->username;
    $firstName = $put_req->first_name;
    $lastName = $put_req->last_name;
    $email = $put_req->email;

    if (empty($id) || empty($userName) || empty($firstName) || empty($lastName) || empty($email)) {
        respond_with_error("Invalid or null value!", 406);
    } elseif (is_numeric($firstName) || is_numeric($lastName)) {
        respond_with_error("First or last name cannot contain numeric values.", 406);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond_with_error("Invalid email address.", 406);
    } else {
        $user = getUserById($id);
        if ($user) {
            $update = $db->prepare("UPDATE users SET username = :uname, first_name = :fname, last_name = :lname, email = :email WHERE id = :id");
            $update->bindParam(":uname", $userName, PDO::PARAM_STR);
            $update->bindParam(":fname", $firstName, PDO::PARAM_STR);
            $update->bindParam(":lname", $lastName, PDO::PARAM_STR);
            $update->bindParam(":email", $email, PDO::PARAM_STR);
            $update->bindParam(":id", $id, PDO::PARAM_INT);
            $update->execute();

            if ($update) {
                $jsonArray = array_merge($jsonArray, [
                    'message' => "Update successful",
                    'affectedId' => $id
                ]);
            } else {
                respond_with_error("Data sending failed!", 403);
            }
        } else {
            respond_with_error("No value found for your request!", 404);
        }
    }

} else {
    respond_with_error("Method Not Allowed", 405);
}

SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
