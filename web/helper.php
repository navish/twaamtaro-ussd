<?php
   
    //------------------------------------------------------------------------------
    //-------------------------------BEGIN MENUS-----------------------------------//
    //------------------------------------------------------------------------------
    function dWelcomeMenu($user) {
        $menulist = "
        1.Pata Taarifa
        2.Tuma Taarifa
        3.Omba Msaada        
        4.Badili Lugha";

        $welcomemenu =" CON ".$user." Karibu Twaa Mtaro. \nChagua Huduma ".$menulist;
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
            $citizen = "Hakuna mwananchi mwenye id hii";
        } 
        return $citizen;
    }

    function sendInfo($info,$userId) {
        $dbcon = db(); 
        switch ($info) {
            //Citizen has cleaned their drain
            case 1:
                $sqlClaim = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");
                
                if(pg_num_rows($sqlClaim) > 0) {
                    $sendClean = pg_query($dbcon,"UPDATE drain_claims SET shoveled = true WHERE user_id=$userId");
                    if ($sendClean) {
                        return "END Umefanikiwa kutuma taarifa";
                    } else {
                        return "END Haujafanikiwa kutuma taarifa";
                    }
                }
                else
                    {
                        return "END Taarifa yako haijatumwa. Hauna mtaro wowote.";
                    }
            break;

            //Citizen reporting rubbish collection
            case 2:
                return "END Huduma hii haipo kwa sasa.";
            break;
            
            default:
                return "END Haujafanya chaguo sahihi";
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

                if ($statusvalue = true) {
                    $drainstatus ='Mtaro wako ni msafi';
                }
                elseif ($statusvalue = false)  {
                    $drainstatus = 'Mtaro wako ni mchafu';
                }
                elseif ($statusvalue === null)  {
                    $drainstatus ='Hakuna taarifa yoyote inayohusu mtaro wako';
                } 
            }   
            else
            {
                $drainstatus ='Haujatwaa mtaro wowote, 
                            wasiliana na kiongozi wako wa mtaa kwa msaada zaidi.';
            }
        return "END ".$drainstatus;
    } //End Get Drain Status

    function getCollaborators($userId)
    {   
        $dbcon = db();
        //Check if the user has any claimed drains
        $sqlClaim = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");
        
        if(pg_num_rows($sqlClaim) > 0) {
            $claimInfo=pg_fetch_assoc($sqlClaim);
            $drain =$claimInfo['gid'];

            $sqlCollaborator = pg_query($dbcon,"SELECT * FROM drain_claims WHERE gid=$drain AND user_id != $userId");
            //Check if there is any collaborator
              if(pg_num_rows($sqlCollaborator) > 0) {
                $collaborators = 'Washirika wako ni: ';

                //Get collaborators' names from users table
                while ($collaboratorInfo=pg_fetch_assoc($sqlCollaborator)) {
                    $user =$collaboratorInfo['user_id'];
                    $collaborators .= getUser($user).'  ';
                    
                }
              } //End getting collaborators details
              else {
                    $collaborators = 'Hauna washirika wowote kwenye mtaro wako';
                }
            } 
            else
            {
                $collaborators ='Hauna washirika wowote sababu haujatwaa mtaro wowote,
                            wasiliana na kiongozi wako wa mtaa kwa msaada zaidi.';
            } 
        return "END ".$collaborators;
    } //End getting collaborators

    function getHelpCategories() //Gets Help Categories from the DB
    {
        $dbcon = db();
        $categoriesMenu = '';
        $sqlCategories = pg_query($dbcon,"SELECT * FROM need_help_categories");
        $categoriesMenu.='<ol>';
            while ($categories=pg_fetch_assoc($sqlCategories)) {
                    $category =$categories['category_name'];
                    $categoriesMenu.='<li>'.$category.'</li> ';
                     
            }
        $categoriesMenu.='</ol>';
        return "CON Chagua aina ya msaada ". $categoriesMenu;
    }
    
    function askForHelp($res, $user)
    {      
        $dbcon = db();    
        $helpText = "";
        $helpDetails = explode("*", $res);

        if(count($helpDetails)==1) {
            //Enter Drain Id
            $helpText .= "CON OMBA MSAADA
                        Ingiza namba ya mtaro";
            return $helpText;
        }
        else if(count($helpDetails)==2) {
            //Enter HelpCategory
            $helpText .= getHelpCategories();
            return $helpText;
        }
        else if(count($helpDetails) == 3){
            $helpText .="CON Ongeza maelezo (Sio lazima)";
            return $helpText;
        }
        else if(count($helpDetails) == 4) {

            $drainId = $helpDetails[1];
            $helpCategory = $helpDetails[2];
            $helpNeeded = $helpDetails[3]; 

            //Send Help Details to the DB
            $sqlHelp = pg_query($dbcon, 
                "INSERT INTO need_helps( help_needed, gid, user_id, need_help_category_id, 
                created_at, updated_at) 
                VALUES ( '$helpNeeded', $drainId , $user, $helpCategory, now(), now())");

            if ($sqlHelp) {
                return "END Umefanikiwa kuomba msaada kwa ajili ya mtaro namba ".$drainId;
            } else {
                return "END Kuna tatizo limetokea, maombi yako ya msaada hayajatumwa";
            }
        }

    }
    function switchLang()
    {
        echo "END Huduma hii haipatikani kwa sasa\n";
    }
    //------------------------------------------------------------------------------
    //          END FUNCTIONS
    //------------------------------------------------------------------------------
    
?>