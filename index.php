<?php

        #We obtain the data which is contained in the post url on our server.
        $welcomemenu = "1. Pata Taarifa\n2. Tuma Taarifa\n3. Badili Lugha";
        $level01menu  = "1. Hali ya Mtaro wako \n2. Washirika";
        $level02menu  = "1. Umefanya usafi\n2. Eneo/Mtaro Mchafu\n3. Uchafu haujatolewa";

        //$level11answer  = "\n".$drainstatus."  \n";
        $level12answer  = "Washirika wako ni \n1. Nancy\n2. Edgar \n3. Beaty";

        $level21answer  = "Nimefanya Usafi \n";
        $level22answer  = "Mtaa/Mtaro ni mchafu \n";
        $level23answer  = "Uchafu, collection point \n";

        $res=$_GET['USSD_STRING']; //User response 
        $phonenumber=$_GET['MSISDN']; //Assumed mobile number
        $serviceCode=$_GET['serviceCode'];
        //$sessionId=$_GET['sessionId']; //For any audits/checks

        include 'dbcon.php';         

        $level = explode("*", $res);
        if (isset($res)) {
        $number =$phonenumber;
        $citizen = pg_query($dbcon,"SELECT * FROM users WHERE sms_number='$number'");

        if (pg_num_rows($citizen) > 0) {
            $user_row=pg_fetch_assoc($citizen);
            $user = $user_row['id'];
            $mhusika = $user_row['first_name'].' '.$user_row['last_name'];
            $mitaro = "";

            $sqlClaims = pg_query($dbcon,"SELECT * FROM sidewalk_claims WHERE user_id='$user'");

            if(pg_num_rows($sqlClaims) > 0) {
            $claimsInfo=pg_fetch_assoc($sqlClaims);

                $mitaro =$claimsInfo['gid'];
                $statusvalue =$claimsInfo['shoveled'];
                if ($statusvalue =true) {
                    $drainstatus ='Mtaro wako ni msafi';
                }
                elseif ($statusvalue = false)  {
                    $drainstatus ='Mtaro wako ni mchafu';
                }
                elseif ($statusvalue = null)  {
                    $drainstatus ='Hakuna taarifa yoyote inayohusu mtaro wako';
                } 

            } else {
              $drainstatus ="No Claims";
              
            }
            
   
        if ( $res == "" ) {
            $response=" CON ".$mhusika   ." Karibu Twaa Mtaro.\n Chagua Huduma Yako\n ".$welcomemenu;
        }
        if(isset($level[0]) && $level[0]!="" && !isset($level[1])){
            switch ($level[0]) {
                case 1:
                   $response="CON Chagua Huduma\n".$level01menu;
                break;
                case 2:
                    $response="CON Chagua Huduma\n".$level02menu;
                break;
                case 3:
                    $response="END Endelea tu kutumia Kiswahili\n";
                break;
                
                default:
                     $response="CON Chaguo sio sahihi\n";
                break;
            }
          
             
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="1" && !isset($level[2])){
            switch ($level[1]) {
                case 1:
                   $response="END ".$drainstatus;
                break;
                case 2:
                    $response="END ".$level12answer;
                break;
                
                default:
                     $response="END Chaguo sio sahihi 11 \n";
                break;
            }
           
        }
        else if(isset($level[1]) && $level[1]!="" && $level[0]=="2" && !isset($level[2])){
            switch ($level[1]) {
                //Citizen has cleaned their drain
                case 1:
                   
                   $updateStatus = pg_query($dbcon,"UPDATE sidewalk_claims SET shoveled = false WHERE user_id='$user'");
                   if ($updateStatus) {
                       $response="END Taarifa yako ya usafi imefika";
                   } else {
                       $response="END Taarifa yako ya usafi imefika";
                   }
                break;

                //Citizen is reporting a dirty drain/area
                case 2:
                    $response="END ".$level22answer;
                break;

                //Citizen reporting rubbish collection
                case 3:
                    $response="END ".$level23answer;
                break;
                
                default:
                     $response="END Chaguo sio sahihi\n";
                break;
            }
           
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
