<?php

function createUser($fname,$lname,$contact,$dob,$email,$password){
    
    $servername = "localhost";
    $username = "root";
    $dbpassword = "";
    $dbname = "userdetails";
    // $servername = "sql.freedb.tech:3306";
    // $username = "freedb_vicdom";
    // $password = "keB7?NPV**EfNW2";
    
    // $fname = "Vic";
    // $lname = "ky";
    // $email = "vicky@hmai.com";
    // $dob = "2023-03-05";
    // $password = "kjadajhre";

    $output = json_encode(array('type' => 'error', 'text' => 'DB Connection failed'));
    try {
        $conn = mysqli_connect($servername, $username, $dbpassword, $dbname);
    } catch (\Throwable $th) {
        die($output);
    }

    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($user = $result->fetch_assoc()){
        $output = json_encode(array('type' => 'result', 'text' => "Record already created"));
        die($output);
    }
    
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, contact, dob, email,password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $fname, $lname, $contact, $dob, $email, $password);
    

    if ($stmt->execute()) {
        $output = json_encode(array('type' => 'result', 'text' => "New record created successfully"));
        die($output);
    } else {
        $output = json_encode(array('type' => 'error', 'text' => "Record creation failed"));
        die($output);
    }

    mysqli_close($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $output = "";
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $contact = $_POST["contact"];
    $dob = $_POST["dob"];
    $email = $_POST["email"];
    $password = $_POST['password'];

    
    
    if ($fname == "" || $lname == "" || $contact == "" || $dob == "" || $email == "" || $password == "") {
        $output = json_encode(array('type' => 'error', 'text' => 'Input fields are empty!'));
        die($output);
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $output = json_encode(array('type' => 'error', 'text' => "Invalid email format"));
        die($output);
    }
    
    if(strlen($contact) != 10){
        $output = json_encode(array('type' => 'error', 'text' => "Invalid contact number"));
        die($output);
    }
    
    if(!(strlen($password) >= 8 && strlen($password) <= 16)){
        $output = json_encode(array('type' => 'error', 'text' => "Password should be >= 8 and <= 16"));
        die($output);
    }
    
    createUser($fname,$lname,$contact,$dob,$email,$password);
}

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    require '../css/vendor/predis/predis/autoload.php';
    
    Predis\Autoloader::register();
    
    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => '6379',
        "password" => ""));
        
        $arList = $redis->keys("*"); 
        
        if(count($arList) == 6){
            $output = json_encode(array('type' => 'result', 'text' => "redirect"));
            die($output);
        }
        $output = json_encode(array('type' => 'error', 'text' => "no redirect"));
        die($output);
    }

?>