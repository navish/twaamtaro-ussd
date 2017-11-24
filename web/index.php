<?php

require('../vendor/autoload.php');


$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->post('/', function() use($app) {
    require __DIR__.'/helper.php';
    require __DIR__.'/dbcon.php';

    $res         = $_POST["text"]; //User response 
    $phonenumber = $_POST["phoneNumber"]; //Assumed mobile number
    $serviceCode = $_POST["serviceCode"];

    //$sessionId=$_GET['sessionId']; //For any audits/checks
    $dbcon = db(); 
    $level = explode("*", $res);
    if (isset($res)) {
      if (strpos($phonenumber, '+') !== false) {
          $phonenumber = str_replace('+','',$phonenumber);
      }

      $citizen = pg_query($dbcon, "SELECT * FROM users WHERE sms_number='$phonenumber'");


    if (pg_num_rows($citizen) > 0) {
        $user_row = pg_fetch_assoc($citizen);
        $user = $user_row['id'];
        $mhusika = $user_row['first_name'].' '.$user_row['last_name'];
        $role = $user_row['role'];        

    if ( $res == "" ) {
        $response = dWelcomeMenu($mhusika); 
    }
    if(isset($level[0]) && $level[0]!= "" && !isset($level[1])){
        error_log("first level");
        switch ($level[0]) {
            case 1:
               $response = dGetInfoMenu();
            break;
            case 2:
                $response = dSendInfoMenu();
            break;
            case 3:
                $response = askForHelp($user);
                
            break;
            case 4:
                $response = switchLang();
            break;
            
            default:
                 $response = "CON Chaguo sio sahihi\n";
            break;
        }
         
    }
    else if(isset($level[1]) && $level[1]!="" && $level[0]=="1"  && !isset($level[2])){
        error_log("secon level if");
        switch ($level[1]) {
            case 1:
                $sqlClaims = pg_query($dbcon, "SELECT * FROM drain_claims WHERE user_id='$user'");
                
                if(pg_num_rows($sqlClaims) > 0) {
                  
                  $claimsInfo = pg_fetch_assoc($sqlClaims);
                  $response = getDrainStatus($claimsInfo);
                  
                } else{
                  $response = "Haujatwaa mtaro wowote,
                               Wasiliana na kiongozi wako wa mtaa
                               kwa maelezo zaidi";
                }
                $response = 'END '.$response;
            break;
            case 2:
                $response = getCollaborators($user);
            break;    
            default:
                 $response = "CON Chaguo sio sahihi\n";
            break;
        }   
        
    }
    else if(isset($level[1]) && $level[1]!="" && $level[0]=="2" && !isset($level[2])){
        error_log("third level if");
        $response = sendInfo($level[1],$user);
    }
    
    else if(isset($level[2]) && $level[2]!="" && !isset($level[3])){
        //Save data to database
         error_log("Fourth level");
         echo("There is nothing here");
        
        $response = "END Asante kwa kutumia huduma yetu";
    }
    
    }//If citizen is registered

    else { 

      $response = "END Namba yako, ".$phonenumber." haijasajiliwa na Twaa Mtaro. Wasiliana na kiongozi wako wa mtaa kwa msaada zaidi "; 
  }
    header('Content-type: res/plain');
    return $response;
    }//If USSD String is set

header('Content-type: text/plain');
return $response;
// DONE!!!

});

$app->run();
