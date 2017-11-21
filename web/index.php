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
     
    // Reads the variables sent via POST from our gateway
    $sessionId   = $_POST["sessionId"];
    $serviceCode = $_POST["serviceCode"];
    $phoneNumber = $_POST["phoneNumber"];
    $text        = $_POST["text"];
    if ( $text == "" ) {
         // This is the first request. Note how we start the response with CON
         $response  = "CON What would you want to check \n";
         $response .= "1. My Account \n";
         $response .= "2. My phone number";
    }
    else if ( $text == "1" ) {
      // Business logic for first level response
      $response = "CON Choose account information you want to view \n";
      $response .= "1. Account number \n";
      $response .= "2. Account balance";
      
     }
     else if($text == "2") {
      // Business logic for first level response
      // This is a terminal request. Note how we start the response with END
      $response = "END Your phone number is $phoneNumber";
     }
     else if($text == "1*1") {
      // This is a second level response where the user selected 1 in the first instance
      $accountNumber  = "ACC1001";
      // This is a terminal request. Note how we start the response with END
      $response = "END Your account number is $accountNumber";
     }
        
     else if ( $text == "1*2" ) {
      
         // This is a second level response where the user selected 1 in the first instance
         $balance  = "TZS 10,000";
         // This is a terminal request. Note how we start the response with END
         $response = "END Your balance is $balance";
    }
    // Print the response onto the page so that our gateway can read it
    header('Content-type: text/plain');
    $response_http = new Response();
    $response_http->headers->set('Content-Type', 'text/plain');
    $response_http->sendHeaders();

    $app['monolog']->addDebug('logging output.');

    return "CON What would you want to check \n";
// DONE!!!

});

$app->run();
