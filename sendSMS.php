<?php
// Be sure to include the file you've just downloaded
require_once('AfricasTalkingGateway.php');
// Specify your authentication credentials

$apikey ="39bd2748e8a747a3914b288f199687528cdf4675f6502a7977d0a06c515c9c60";
$username="Muragk01";
$from ="FIRSTFIN";

$dbhost = 'localhost';
$dbname = 'otp-test';
$dbusername = 'my-otp';
$dbpassword = 'Otp@2021';

// Specify the numbers that you want to send to in a comma-separated list
$_POST = json_decode(file_get_contents('php://input'), true);

// Create a new instance of our awesome gateway class
$gateway    = new AfricasTalkingGateway($username, $apikey);
/*************************************************************************************/
try 
{ 
  $otp = rand(1001,9999);  
//   // Thats it, hit send and we'll take care of the rest. 
  $_POST['message'] = "Your OTP is " . $otp;
  
  $results = $gateway->sendMessage($_POST['phone'], $_POST['message'],$from);
           
  foreach($results as $result) 
  {
	
    $arr = array ('phone'=>$result->number,'status'=>$result->messageId,'message'=>$_POST['message'],'otp'=>(string)$otp);
    
    echo json_encode($arr);
	
    $link = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);

    $statement = $link->prepare('INSERT INTO otps (message_id, phone, otp, status) VALUES (?, ?, ?, ?)');
	
    $statement->execute([$result->messageId,$_POST['phone'] ,$otp , $result->status]);
   }
  
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while sending: ".$e->getMessage();
}
// DONE!!! 