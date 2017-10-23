<?php
        include 'dbcon.php';
        include 'helper.php';
        

        $res=$_GET['USSD_STRING']; //User response 
        $phonenumber=$_GET['MSISDN']; //Assumed mobile number
        $serviceCode=$_GET['serviceCode'];
        //$sessionId=$_GET['sessionId']; //For any audits/checks

        $dbcon = db(); 

        $level = explode("*", $res);
        if (isset($res)) {
        $number =$phonenumber;
        $citizen = pg_query($dbcon,"SELECT * FROM users WHERE sms_number='$number'");
        if (pg_num_rows($citizen) > 0) {
            $user_row=pg_fetch_assoc($citizen);
            $user = $user_row['id'];
            $mhusika = $user_row['first_name'].' '.$user_row['last_name'];
            $mitaro = "";            
   
        if ( $res == "" ) {
            $response=dWelcomeMenu($mhusika);
        }
        if(isset($level[0]) && $level[0]!="" && !isset($level[1])){
            switch ($level[0]) {
                case 1:
                   $response=dGetInfoMenu();
                break;
                case 2:
                    $response= dSendInfoMenu();
                break;
                case 3:
                    $response=switchLang();
                break;
                
                default:
                     $response="CON Chaguo sio sahihi\n";
                break;
            }
          
             
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="1" && !isset($level[2])){
            $response=getDrainStatus($user);
            
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="2" && !isset($level[2])){
            $response=sendInfo($level[1],$user);

        }
        else if(isset($level[2]) && $level[2]!="" && !isset($level[3])){
            //Save data to database
            /*$data=array(
                'phonenumber'=>$phonenumber,
                'fullname' =>$level[0],
                'electoral_ward' => $level[1],
                'national_id'=>$level[2]
                );*/
            
            $response="END Asante kwa kutumia huduma yetu";
            }
        
        }//If citizen is registered
        else { $response = "Namba yako, ".$number." haijasajiliwa na Twaa Mtaro. Wasiliana na kiongozi wako wa mtaa kwa msaada zaidi "; }

        header('Content-type: res/plain');
        echo $response;
        }//If USSD String is set

?>
