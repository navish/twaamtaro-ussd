<?php
   
    //------------------------------------------------------------------------------
    //-------------------------------BEGIN MENUS-----------------------------------//
    //------------------------------------------------------------------------------
    function dWelcomeMenu($user) 
    {
        $menulist = "
        1.Pata Taarifa
        2.Tuma Taarifa
        3.Omba Msaada        
        4.Badili Lugha";

        $welcomemenu =" CON ".$user." Karibu Twaa Mtaro. \nChagua Huduma ".$menulist;
        return $welcomemenu;
    }

    //Display a list of all districts
    function dDistrictsMenu() 
    {
        $districtsList = "
        1. Kinondoni
        2. Ilala
        3. Temeke        
        4. Ubungo
        5. Kigamboni";

        $districtsmenu =" CON OMBA MSAADA
        Chagua Wilaya ".$districtsList;
        return $districtsmenu;
    }

    //Display a list of all list streets
    function dStreetsMenu() 
    {
        $dbcon = db();
        $streetsList  = '';

        $sqlStreets = pg_query($dbcon,"SELECT * FROM streets");
        if (pg_num_rows($sqlStreets) > 0) {

            $allStreets = count($streets);
            $treetNo = 1;

            while ($streetRow=pg_fetch_assoc($sqlStreets)) {
                    $street = $streetRow['street_name'];
                    $streetsList .= ' '.$streetNo.'. '.$street;
            
            $streetNo++;         
            }            
        }
        else {
            $streetsMenu = "END Hakuna mitaa kwenye kata hii";
        } 
        return $streetsList;
    }

    //Display a list of all wards
    function dWardsMenu() {
        $wardsList = "
        1. Hananasifu
        2. Mbuyuni";

        $wardsmenu =" CON Chagua Wilaya ".$wardsList;
        return $wardsmenu;
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
    //-----------------------------------------------------------------------------//
    //--------------------------BEGIN FUNCTIONS------------------------------------//
    //-----------------------------------------------------------------------------//

    function getUser($id)
    {
        $dbcon = db();
        $sqlCitizen = pg_query($dbcon,"SELECT * FROM users WHERE id=$id");
        if (pg_num_rows($sqlCitizen) > 0) {
            $user_row=pg_fetch_assoc($sqlCitizen);
            $citizen = $user_ow['first_name'].' '.$user_row['last_name'];            
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


    //Get Drains from a specific street
    function getStreetDranis($streetId)
    {
        $dbcon = db();
        $sqlDrains = pg_query($dbcon,"SELECT * FROM mitaro_dar WHERE street_id=$streetId");
        if (pg_num_rows($sqlDrains) > 0) {
            $drain_row=pg_fetch_assoc($sqlDrains);
            $drains = $drain_row['address'].' '.$drain_row['gid'];            
        }
        else {
            $drains = " Hakuna mitaro yoyote katika mtaa huu";
        }
        return "END ".$drains;
    } //End getting drains from streets

    function getHelpCategories() //Gets Help Categories from the DB
    {
        $dbcon = db();
        $categoriesMenu = '';
        $allCategories = count($categories);
        $sqlCategories = pg_query($dbcon,"SELECT * FROM need_help_categories");
        $categoryNo = 1;
            while ($categories=pg_fetch_assoc($sqlCategories)) {
                    $category =$categories['category_name'];
                    $categoriesMenu.= ' '.$categoryNo.'. '.$category;
            
            $categoryNo++;         
            }
        return "CON Chagua aina ya msaada ". $categoriesMenu;
    }
    
    function askForHelp($res, $user)
    {      
        $dbcon = db();    
        $helpText = "";
        $helpDetails = explode("*", $res);

        if(count($helpDetails) == 1) {
            //Districts List
            $helpText .= dDistrictsMenu();
            return $helpText;
        }
        else if(count($helpDetails) == 2) {
            //Enter Streets List
            $helpText .= dStreetsMenu();
            return $helpText;
        }
        else if(count($helpDetails) == 3) {
            //Enter Drain Id
            $helpText .= "CON OMBA MSAADA  Ingiza namba ya mtaro";
            return $helpText;
        }
        else if(count($helpDetails) == 4) {
            //Enter HelpCategory
            $helpText .= getHelpCategories();
            return $helpText;
        }
        else if(count($helpDetails) == 5){
            $helpText .="CON Ongeza maelezo ";
            return $helpText;
        }
        else if(count($helpDetails) == 6) {

            $districtId = $helpDetails[1];
            $streetId = $helpDetails[2];
            $drainId = $helpDetails[3];
            $helpCategory = $helpDetails[4];
            $helpNeeded = $helpDetails[5]; 

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