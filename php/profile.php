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

?>