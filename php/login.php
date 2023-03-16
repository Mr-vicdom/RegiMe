<?php

function checkUser($email,$password){
    
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
        die($output);
    }

    $sql = "SELECT email,password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if(!$user = $result->fetch_assoc()){
        $output = json_encode(array('type' => 'result', 'text' => "User not found"));
        die($output);
    }

    mysqli_close($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $output = "";

    $email = $_POST["email"];
    $password = $_POST['password'];
    
    if ($email == "" || $password == "") {
        $output = json_encode(array('type' => 'error', 'text' => 'Input fields are empty!'));
        die($output);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $output = json_encode(array('type' => 'error', 'text' => "Invalid email format"));
        die($output);
    }
    
    if(!(strlen($password) >= 8 && strlen($password) <= 16)){
        $output = json_encode(array('type' => 'error', 'text' => "Password should be >= 8 and <= 16"));
        die($output);
    }

    checkUser($email,$password);
}

?>