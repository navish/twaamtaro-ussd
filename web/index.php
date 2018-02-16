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
    require __DIR__.'/functions.php';

    $res         = $_POST["text"]; //User response 
    $phonenumber = $_POST["phoneNumber"]; //Assumed mobile number
    $serviceCode = $_POST["serviceCode"];
    //$sessionId=$_GET['sessionId']; //For any audits/checks
    
    if (isset($res)) {

        if (strpos($phonenumber, '+') !== false) {
          $phonenumber = str_replace('+','',$phonenumber);
        }


        //Get Citizen Details
        $citizen = getUser($phonenumber);
        $username = $userDetails->user->first_name." ". $userDetails->user->last_name;
        $userId = $userDetails->user->id;

        $lang = userLangAssign($phonenumber);
    
    //If user is registered
    if ($citizen != NULL) {
        if ( $res == "" ) {
            $response = dWelcomeMenu($username, $lang); 
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
                    $response = askForHelp($res, $userId, $lang);
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
            $response = sendInfoDrains($user,$lang);
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="2" && isset($level[2])){
            $response = sendInfo($level[1],$level[2],$user,$lang);
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
