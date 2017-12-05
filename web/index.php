<?php

require('../vendor/autoload.php');


$app = new Silex\Application();
$app['debug'] = true;
$app['lang'] = "en";

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
    if (isset($res)) {
      if (strpos($phonenumber, '+') !== false) {
          $phonenumber = str_replace('+','',$phonenumber);
    }

    $citizen = pg_query($dbcon, "SELECT * FROM users WHERE sms_number='$phonenumber'");
    $lang = userLangAssign($phonenumber);

    if (pg_num_rows($citizen) > 0) {
        error_log("found citizen");
        $user_row = pg_fetch_assoc($citizen);
        $user = $user_row['id'];
        $street = $user_row['street_id'];
        $userName = $user_row['first_name'].' '.$user_row['last_name'];
        //$role = $user_row['role']; 

        if ( $res == "" ) {
            $response = dWelcomeMenu($userName, $lang); 
        }

        $level = explode("*", $res);

        if(isset($level[0]) && $level[0]!= "" && !isset($level[1])){
            error_log("first level");
            switch ($level[0]) {
                case 1:
                   $response = dGetInfoMenu($lang);
                break;
                case 2:
                    $response = dSendInfoMenu($lang);
                break;
                case 3:
                    $response = askForHelp($res, $user, $lang);
                break;
                case 4:
                    $lang = updateUserLang($phonenumber);
                    if ($lang == "sw") {
                         $response = "END Umechagua Kiswahili\n";
                     } elseif($lang == "en") {
                         $response = "END You have chosen English\n";
                     }
                break;
                
                default:
                     
                     if ($lang == "sw") {
                         $response = "CON Chaguo sio sahihi\n";
                     } elseif($lang == "en") {
                         $response = "CON Wrong Choice\n";
                     }
                     
                break;
            }
             
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="1"  && !isset($level[3])){
            switch ($level[1]) {
                case 1:                
                      $response = getDrainStatus($user,$lang);
                break;
                case 2:
                    $response = getCollaborators($user,$lang);
                break;    
                default:
                     if ($lang == "sw") {
                         $response = "CON Chaguo sio sahihi\n";
                     } elseif($lang == "en") {
                         $response = "CON Wrong Choice\n";
                     }
                break;
            }   
            
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="2" && !isset($level[2])){
            $response = sendInfo($level[2],$user,$lang);
        }
        
        else if(isset($level[0]) && $level[0]!="" && $level[0]=="3"){
            $response = askForHelp($res, $user,$lang);
            }//End Need Help
            
           
        
    }//End If citizen is registered

    else { 
        if ($lang == "sw") {
           $response = "END Namba yako, ".$phonenumber." haijasajiliwa na Twaa Mtaro. Wasiliana na kiongozi wako wa mtaa kwa msaada zaidi. "; 
        } elseif($lang == "en") {
           $response = "END Your phone number, ".$phonenumber." is not registered by Twaa Mtaro. Please contact your street leader for assistance."; 
        }
  }
    header('Content-type: res/plain');
    return $response;
}//If USSD String is set

header('Content-type: text/plain');
return "END ".$response;
// DONE!!!

});

$app->run();
