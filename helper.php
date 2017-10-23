<?php
   
    //------------------------------------------------------------------------------
    //-------------------------------BEGIN MENUS-----------------------------------//
    //------------------------------------------------------------------------------
    function dWelcomeMenu($user) {
        $welcomemenu =" CON ".$user." Karibu Twaa Mtaro. \nChagua Huduma \n1. Pata Taarifa\n2. Tuma Taarifa\n3. Badili Lugha";
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
    function getUser($id)
    {
        $dbcon = db();
        $sqlCitizen = pg_query($dbcon,"SELECT * FROM users WHERE id=$id");
        if (pg_num_rows($sqlCitizen) > 0) {
            $user_row=pg_fetch_assoc($sqlCitizen);
            $citizen = $user_row['first_name'].' '.$user_row['last_name'];            
        }
        else {
            $citizen = "There is no user with that id";
        } 
        return $citizen;
    }
    function sendInfo($info,$userId) {
        $dbcon = db(); 
        switch ($info) {
            //Citizen has cleaned their drain
            case 1:
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
        $dbcon = db();
        $sqlClaims = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");

            if(pg_num_rows($sqlClaims) > 0) {
            $claimsInfo=pg_fetch_assoc($sqlClaims);

                $mitaro =$claimsInfo['gid'];
                $statusvalue =$claimsInfo['shoveled'];

                if ($statusvalue === t) {
                    $drainstatus ='Mtaro wako ni msafi';
                }
                elseif ($statusvalue === f)  {
                    $drainstatus ='Mtaro wako ni mchafu';
                }
                elseif ($statusvalue === null)  {
                    $drainstatus ='Hakuna taarifa yoyote inayohusu mtaro wako';
                } 
                return $drainstatus;
            } 
            
    }
    function getCollaborators($userId)
    {   
        $dbcon = db();
        $collaborators = 'Washirika wako ni: <br>';
        //Search DB for any claimed drains
        $sqlClaim = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");

        //Check if the user has any claimed drains
        if(pg_num_rows($sqlClaim) > 0) {
            $claimInfo=pg_fetch_assoc($sqlClaim);
            $drain =$claimInfo['gid'];
            
            $sqlCollaborator = pg_query($dbcon,"SELECT * FROM drain_claims WHERE gid=$drain AND user_id != $userId");

            //Check if there is any collaborator
              if(pg_num_rows($sqlCollaborator) > 0) {
                
                //Get collaborators' names from users table
                while ($collaboratorInfo=pg_fetch_assoc($sqlCollaborator)) {
                    $user =$collaboratorInfo['user_id'];
                    $collaborators .= getUser($user).'<br>';
                    
                }
              } //End getting collaborators details
              else {
                    $collaborators = 'Hauna washirika wowote kwenye mtaro wako';
                }
            //End checking for collaborators
            }    
             
        return $collaborators;
    }

    function switchLang()
    {
        echo "END Huduma hii haipatikani kwa sasa\n";
    }
    //------------------------------------------------------------------------------
    //          END FUNCTIONS
    //------------------------------------------------------------------------------

    


?>