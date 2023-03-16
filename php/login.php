<?php

function checkUser($email,$password,$success,$failure){
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "userdetails";
    // $servername = "sql.freedb.tech:3306";
    // $username = "freedb_vicdom";
    // $password = "keB7?NPV**EfNW2";
    
    // $email = "vicky@hmai.com";
    // $password = "kjadajhre";
    
    $output = json_encode(array('type' => 'error', 'text' => 'DB Connection failed'));
    try {
        $conn = mysqli_connect($servername, $username, $password, $dbname);
    } catch (\Throwable $th) {
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' => 'DB Connection failed'] );
        print_r(mysqli_connect_error()) && die($output);
    }

    $sql = "SELECT email,password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if(!$user = $result->fetch_assoc()){
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' =>  "User not found" ] );
        $output = json_encode(array('type' => 'result', 'text' => "User not found"));
        die($output);
    }
    
    if($user['password'] == $password){
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' =>  "Login successfull" ] );
        $output = json_encode(array('type' => 'result', 'text' => "Login successfull"));
        die($output);
    } else {
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' =>  "Incorrect password" ] );
        $output = json_encode(array('type' => 'result', 'text' => "Incorrect password"));
        die($output);
    }

    mysqli_close($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require '../css/vendor/autoload.php';

    $client = new MongoDB\Client("mongodb://localhost:27017");

    $db = $client->userlog;

    $success = $db->success;
    $failure = $db->failure;

    
    $output = "";
    
    $email = $_POST["email"];
    $password = $_POST['password'];
    
    
    if ($email == "" || $password == "") {
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' => 'Input fields are empty!' ]);
        $output = json_encode(array('type' => 'error', 'text' => 'Input fields are empty!'));
        die($output);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' => "Invalid email format"] );
        $output = json_encode(array('type' => 'error', 'text' => "Invalid email format"));
        die($output);
    }
    
    if(!(strlen($password) >= 8 && strlen($password) <= 16)){
        $result = $failure->insertOne( [ 'email' => $email , 'password' => $password, 'date' => date('d:m:y') , 'time' => date('h:i:s') , 'error' => "Password should be >= 8 and <= 16"] );
        $output = json_encode(array('type' => 'error', 'text' => "Password should be >= 8 and <= 16"));
        die($output);
    }
    
    checkUser($email,$password,$success,$failure);
}

?>