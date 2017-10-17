<?php
    //------------------------------------------------------------------------------
    //-------------------------------BEGIN MENUS-----------------------------------//
    //------------------------------------------------------------------------------
    function dWelcomeMenu($user) {
        $welcomemenu =" CON ".$user." Karibu Twaa Mtaro. \nChagua Huduma\n1. Pata Taarifa\n2. Tuma Taarifa\n3. Badili Lugha";
        return $welcomemenu;
    }

    //Display Get Information Menu
    function dGetInfoMenu() {
        $menu ="CON Chagua Huduma\n1. Hali ya Mtaro wako \n2. Washirika";
        return $menu;
    } 

    // Display Send Information Menu
    function dSendInfoMenu() {
        $menu ="CON Chagua Huduma\n1. Nimefanya usafi\n2. Uchafu haujatolewa";
        return $menu;
    } 

    //---------------------------END MENUS-----------------------------------------// 


    //------------------------------------------------------------------------------
    //--------------------------BEGIN FUNCTIONS------------------------------------//
    //------------------------------------------------------------------------------

    function sendInfo($info,$userId) {
        switch ($info) {
            //Citizen has cleaned their drain
            case 1:
            var_dump($dbcon);
                $sendClean = pg_query($dbcon,"UPDATE drain_claims SET shoveled = true WHERE user_id=$userId");
                if ($sendClean) {
                    echo "Umefanikiwa kutuma taarifa";
                } else {
                    echo "Haujafanikiwa kutuma taarifa";
                }
            break;
            //Citizen reporting rubbish collection
            case 2:
                    echo "Huduma hii haipo kwa sasa.";
            break;
            
            default:
                echo "Haujafanya chaguo sahihi";
            break;
        } // End Switch 
    } //End sendInfo()

    function getDrainStatus($userId)
    {
        $sqlClaims = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");

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
            return $drainstatus;
    }
   

    function switchLang()
    {
        echo "END Huduma hii haipatikani kwa sasa\n";
    }
    //------------------------------------------------------------------------------
    //          END FUNCTIONS
    //------------------------------------------------------------------------------

    


?>