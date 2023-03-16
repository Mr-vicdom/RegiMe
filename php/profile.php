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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    require '../css/vendor/predis/predis/autoload.php';
    
    Predis\Autoloader::register();

    $output = json_encode(array('type' => 'error', 'text' => 'Redis connection failed'));

    try {
        $redis = new Predis\Client(array(
            "scheme" => "tcp",
            "host" => "127.0.0.1",
            "port" => '6379',
            "password" => "")) or die($output);;
    } catch (\Throwable $th) {
        die($output);
    }

    // echo "Connected to Redis<br>";

    // echo "Server is running: ".$redis->ping()."<br>"; 
    
    $arList = $redis->keys("*"); 

    $output = [];
    
    foreach ($arList as $value) {
        $output[$value] = $redis->get($value);
    }

    // $output = json_encode($output);

    $output = json_encode(array("type" => "result", "text" => $output));

    die($output);

    // $redis->set("fname", "uiij");

    // echo $redis->del("fname");

}


function updateUser($fname,$lname,$contact,$dob,$email,$password,$redis){
    
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
    
    if(!$user = $result->fetch_assoc()){
        $output = json_encode(array('type' => 'result', 'text' => "Record Not Found"));
        die($output);
    }
    
    $stmt = $conn->prepare("UPDATE users SET fname = ?, lname = ?, contact = ?, dob = ?, email = ?,password = ? WHERE email = '".$email."'");
    $stmt->bind_param("ssssss", $fname, $lname, $contact, $dob, $email, $password);
    
    
    if ($stmt->execute()) {
        $output = json_encode(array('type' => 'result', 'text' => "Record updated successfully"));
        
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if(!$user = $result->fetch_assoc()){
            $output = json_encode(array('type' => 'result', 'text' => "Record Not Found"));
            die($output);
        }
        $d = "";
        foreach ($user as $key => $value) {
            $redis->set($key,$value);
            $d.=$key.$value;
        }
        
        // $output = json_encode(array('type' => 'result', 'text' => "Record updated successfully".$d));
        

        die($output);
    } else {
        $output = json_encode(array('type' => 'error', 'text' => "Record updation failed"));
        die($output);
    }

    mysqli_close($conn);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require '../css/vendor/predis/predis/autoload.php';
    
    Predis\Autoloader::register();
    
    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => '6379',
        "password" => ""));

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
    
    updateUser($fname,$lname,$contact,$dob,$email,$password,$redis);
}
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    
    require '../css/vendor/predis/predis/autoload.php';

    Predis\Autoloader::register();

    $redis = new Predis\Client(array(
        "scheme" => "tcp",
        "host" => "127.0.0.1",
        "port" => '6379',
        "password" => ""));
        
    $arList = $redis->keys("*"); 
    
    foreach ($arList as $value) {
        $redis->del($value);
    }
    $output = json_encode(array('type' => 'result', 'text' => "Record deleted successfully"));
    die($output);
}
    

?>