<?php
declare(strict_types=1);

// CORS configuration (Must be before any output)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once './controller/functions.php';

// Load environment variables
loadEnv(__DIR__ . '/.env');

require_once './controller/connect.php';

$jsonArray = ['error' => false];
$_code = 200;
$request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

function respond_with_error(string $message, int $code): void {
    global $jsonArray, $_code;
    $jsonArray['error'] = true;
    $jsonArray['errorMessage'] = $message;
    $_code = $code;
}

function getUserById(int $id): object|false {
    global $db;
    $query = $db->prepare("SELECT * FROM users WHERE id = :id");
    $query->bindValue(":id", $id, PDO::PARAM_INT);
    $query->execute();
    return $query->fetch(PDO::FETCH_OBJ);
}

if ($request_method === "GET") {

    $id = isset($_GET['id']) ? Security((string)$_GET['id']) : null;

    if ($id !== null) {
        if ($id === '' || !is_numeric($id)) {
            respond_with_error("Invalid or null value!", 406);
        } else {
            $result = getUserById((int)$id);
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

    $userName  = Security((string)($_POST['username'] ?? ''));
    $firstName = Security((string)($_POST['first_name'] ?? ''));
    $lastName  = Security((string)($_POST['last_name'] ?? ''));
    $email     = Security((string)($_POST['email'] ?? ''));

    if ($userName === '' || $firstName === '' || $lastName === '' || $email === '') {
        respond_with_error("Invalid or null value!", 406);
    } elseif (is_numeric($firstName) || is_numeric($lastName)) {
        respond_with_error("First or last name cannot contain numeric values.", 406);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond_with_error("Invalid email address.", 406);
    } else {
        $add = $db->prepare("INSERT INTO users (username, first_name, last_name, email) VALUES (:uname, :fname, :lname, :email)");
        $add->bindValue(":uname", $userName, PDO::PARAM_STR);
        $add->bindValue(":fname", $firstName, PDO::PARAM_STR);
        $add->bindValue(":lname", $lastName, PDO::PARAM_STR);
        $add->bindValue(":email", $email, PDO::PARAM_STR);
        $add->execute();

        if ($db->lastInsertId()) {
            $_code = 201; // HTTP 201 Created is more appropriate for success POST
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

    $id = isset($_GET['id']) ? Security((string)$_GET['id']) : '';

    if ($id === '' || !is_numeric($id)) {
        respond_with_error("Invalid or null value!", 406);
    } else {
        $user = getUserById((int)$id);
        if ($user) {
            $delete = $db->prepare("DELETE FROM users WHERE id = :id");
            $delete->bindValue(":id", (int)$id, PDO::PARAM_INT);
            $delete->execute();
            $jsonArray = array_merge($jsonArray, [
                'message' => "Deletion successful",
                'affectedId' => (int)$id
            ]);
        } else {
            respond_with_error("No value found for your request!", 404);
        }
    }

} elseif ($request_method === "PUT") {

    $put_req = json_decode(file_get_contents("php://input"));

    if (json_last_error() !== JSON_ERROR_NONE) {
        respond_with_error("Invalid JSON format", 400);
    } elseif (!$put_req || !is_object($put_req)) {
        respond_with_error("Invalid or null value!", 406);
    } else {
        $id = isset($put_req->id) ? (string)$put_req->id : '';
        $userName = isset($put_req->username) ? Security((string)$put_req->username) : '';
        $firstName = isset($put_req->first_name) ? Security((string)$put_req->first_name) : '';
        $lastName = isset($put_req->last_name) ? Security((string)$put_req->last_name) : '';
        $email = isset($put_req->email) ? Security((string)$put_req->email) : '';

        if ($id === '' || !is_numeric($id) || $userName === '' || $firstName === '' || $lastName === '' || $email === '') {
            respond_with_error("Invalid or null value!", 406);
        } elseif (is_numeric($firstName) || is_numeric($lastName)) {
            respond_with_error("First or last name cannot contain numeric values.", 406);
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond_with_error("Invalid email address.", 406);
        } else {
            $user = getUserById((int)$id);
            if ($user) {
                $update = $db->prepare("UPDATE users SET username = :uname, first_name = :fname, last_name = :lname, email = :email WHERE id = :id");
                $update->bindValue(":uname", $userName, PDO::PARAM_STR);
                $update->bindValue(":fname", $firstName, PDO::PARAM_STR);
                $update->bindValue(":lname", $lastName, PDO::PARAM_STR);
                $update->bindValue(":email", $email, PDO::PARAM_STR);
                $update->bindValue(":id", (int)$id, PDO::PARAM_INT);
                $update->execute();

                $jsonArray = array_merge($jsonArray, [
                    'message' => "Update successful",
                    'affectedId' => (int)$id
                ]);
            } else {
                respond_with_error("No value found for your request!", 404);
            }
        }
    }

} else {
    respond_with_error("Method Not Allowed", 405);
}

SetHeader($_code);
$jsonArray[$_code] = HttpStatus($_code);
echo json_encode($jsonArray);
