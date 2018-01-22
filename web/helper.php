<?php
   
    //------------------------------------------------------------------------------
    //-------------------------------BEGIN MENUS-----------------------------------//
    //------------------------------------------------------------------------------
   
    function dWelcomeMenu($user, $lang) 
    {
        
        if ($lang == "sw" || !isset($lang)) {
            $menulist = "
            1. Pata Taarifa
            2. Tuma Taarifa
            3. Omba Msaada        
            4. English";

            $welcomemenu =" CON ".$user." Karibu Twaa Mtaro. \nChagua Huduma ".$menulist;
        } elseif($lang == "en") {
            $menulist = "
            1. Get Information
            2. Send Information
            3. Ask for Help     
            4. Swahili";

            $welcomemenu =" CON ".$user." Welcome to Twaa Mtaro. \nChoose a service ".$menulist;
        } else {
            $menulist = "
            1. Pata Taarifa
            2. Tuma Taarifa
            3. Omba Msaada        
            4. English";

            $welcomemenu =" CON ".$user." Karibu Twaa Mtaro. \nChagua Huduma ".$menulist;
        }
        return $welcomemenu;
    }

    //Display a list of all districts
    function dDistrictsMenu($lang) 
    {
        $districtsList = "
        1. Kinondoni
        2. Ilala
        3. Temeke        
        4. Ubungo
        5. Kigamboni";
        
        if ($lang == "sw") {
            $districtsmenu =" CON OMBA MSAADA
        Chagua Wilaya ".$districtsList;
        } elseif ($lang == "en") {
            $districtsmenu =" CON Ask for help
        Choose district ".$districtsList;
        }
        return $districtsmenu;
    }

    //Display a list of all streets
    function dStreetsMenu($lang) 
    {
        $dbcon = db();
        $streetsList  = '';

        $sqlStreets = pg_query($dbcon,"SELECT * FROM streets");
        if (pg_num_rows($sqlStreets) > 0) {

            $allStreets = count($streets);
            $streetNo = 1;

            while ($streetRow=pg_fetch_assoc($sqlStreets)) {
                    $street = $streetRow['street_name'];
                    $streetsList .= "\n".$streetNo.". ".$street;
            
            $streetNo++;         
            } 
           
            if ($lang == "sw") {
             $streetsMenu = "CON Chagua mtaa ".$streetsList; 
            } elseif ($lang == "en") {
             $streetsMenu = "CON Choose street ".$streetsList; 
            }
     
        }
        else {
            if ($lang == "sw") {
             $streetsMenu = "END Hakuna mitaa kwenye kata hii.";
            } elseif ($lang == "en") {
             $streetsMenu = "There are no drains in this ward."; 
            }
        } 
        return $streetsMenu;
    }

    //Display a list of all wards
    function dWardsMenu($lang) {
        $wardsList = "
        1. Hananasifu
        2. Kigogo
        3. Magomeni
        4. Makumbusho
        5. Msasani
        5. Mwananyamala
        6. Mzimuni
        7. Ndugumbi
        8. Tandale ";

        
        if ($lang == "sw") {
             $wardsmenu =" CON Chagua Kata ".$wardsList;
            } elseif ($lang == "en") {
             $wardsmenu =" CON Choose Ward ".$wardsList; 
            }
        return $wardsmenu;
    }

    //Display Get Information Menu
    function dGetInfoMenu($lang) {
        if ($lang == "sw") {
            $menu ="CON Chagua Huduma\n1. Hali ya Mtaro wako \n2. Washirika";
        } elseif ($lang == "en") {
            $menu ="CON Choose a service\n1. Drain Status \n2. Collaborators";
        } 
        
        return $menu;
    } 

    // Display Send Information Menu
    function dSendInfoMenu($lang) {
        if ($lang == "sw") {
            $menu ="CON Chagua Huduma\n1. Nimefanya usafi";
        } elseif ($lang == "en") {
            $menu ="CON Choose a service\n1. I cleaned my drain";
        } 
        
        return $menu;
    } 

    //---------------------------END MENUS-----------------------------------------// 
    //-----------------------------------------------------------------------------//

    //--------------------------BEGIN FUNCTIONS------------------------------------//
    //-----------------------------------------------------------------------------//

    //--------------------------LANGUAGE FUNCTIONS---------------------------------//
    //-----------------------------------------------------------------------------//

    //Check if user exists in the language table
    //Create the record if not
    //Params user number
    function userLangAssign($phoneNumber)
    {
        $dbcon = db();

        $sqlUsers = pg_query($dbcon,"SELECT * FROM ussd_users WHERE phonenumber='$phoneNumber' LIMIT 1");
        if (pg_num_rows($sqlUsers) > 0) {
           $user = pg_fetch_assoc($sqlUsers);
           $lang = $user['language'];
        }
        else{
          $sqlInsertUsers = pg_query($dbcon,"INSERT INTO ussd_users (phonenumber, language) VALUES ('$phoneNumber', 'sw')");
          $lang = 'sw';

        }
        return $lang;
    }

    //Updates the user language
    //Params user number
    function updateUserLang($phoneNumber)
    {
        $dbcon = db();
        $lang = changeLang($phoneNumber);

        $sqlUsers = pg_query($dbcon,"UPDATE ussd_users SET language = '$lang' WHERE phonenumber='$phoneNumber'");
        
        return $lang;
    }

    // Changes user language
    function changeLang($phoneNumber){
        $lang = userLangAssign($phoneNumber);

        if ($lang == "sw"){
            $lang = "en";
        }elseif ($lang == "en"){
            $lang = "sw";
        }

        return $lang;
    }

    //------------------------ END LANGUAGE FUNCTIONS -----------------------------//
    //-----------------------------------------------------------------------------//

    function getUser($id, $lang)
    {
        $dbcon = db();
        $sqlCitizen = pg_query($dbcon,"SELECT * FROM users WHERE id=$id");
        if (pg_num_rows($sqlCitizen) > 0) {
            $user_row=pg_fetch_assoc($sqlCitizen);
            $citizen = $user_ow['first_name'].' '.$user_row['last_name'];            
        }
        else {
            if ($lang == "sw") {
                $citizen = "Hakuna mwananchi mwenye id hii";
            } elseif ($lang == "en") {
               $citizen = "There is no citizen with such ID";
            }            
        } 
        return $citizen;
    }

    function sendInfoDrains($userId, $lang) {
        $dbcon = db();
        $drains = "";
        $sqlClaim = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");
              
        if(pg_num_rows($sqlClaim) > 0) {
            while($drainRow = pg_fetch_assoc($sqlClaim)){
            $drains .= $drainRow['gid']."\n";
            }
        } 
        else
        {
            if ($lang =="sw") {
                $drains = "You have not adopted any drains";
            } elseif($lang =="en") {
                $drains = "Hamna mitaro yoyote ulitwaa";
            }
        }
        return $drains;
    } //End sendInfoDrains()

    function sendInfo($info, $drain, $userId, $lang) {
        $dbcon = db(); 
        switch ($info) {
            //Citizen has cleaned their drain
            case 1:
                $sqlClaim = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId AND gid = $drain");
                
                if(pg_num_rows($sqlClaim) > 0) {
                    $sendClean = pg_query($dbcon,"UPDATE drain_claims SET shoveled = true WHERE user_id=$userId");
                    if ($sendClean) 
                    {
                        if ($lang=="sw") {
                            return "END Umefanikiwa kutuma taarifa.";
                        } elseif($lang=="en") {
                            return "END You have sent the information.";
                        }
                        
                    } 
                    else 
                    {
                        if ($lang=="sw") {
                            return "END Haujafanikiwa kutuma taarifa.";
                        } 
                        elseif($lang=="en") {
                            return "END Information sending failed.";
                        }
                        
                    }
                }
                else
                    {
                        if ($lang=="sw") {
                            return "END Taarifa yako haijatumwa. Hauna mtaro wowote.";
                        } elseif ($lang=="en") {
                            return "END Information was not sent. You don't have any drain.";
                        }   
                    }
            break;
            
            default:
                if ($lang=="sw") {
                    return "END Haujafanya chaguo sahihi";
                } elseif ($lang=="en") {
                    return "END You picked a wrong choice";
                }
                
            break;
        } // End Switch 
    } //End sendInfo()

    function giveStatus($drain, $statusvalue, $lang){
        if($statusvalue = true) {
            if ($lang=="sw") {
                $theStatus ="Mtaro ".$drain.", ni msafi";
            } elseif ($lang=="en") {
                $theStatus ="Drain no. ".$drain.", is clean";
            }
        }
        elseif ($statusvalue = false)  {
            if ($lang=="sw") {
                $theStatus = "Mtaro ".$drain.", ni mchafu";
            } elseif ($lang=="en") {
                $theStatus ="Drain no. ".$drain.", is dirty";
            }
        }
        elseif ($statusvalue === null)  {
            if ($lang=="sw") {
                $theStatus ="Hakuna taarifa yoyote inayohusu mtaro wako";
            } elseif ($lang=="en") {
                $theStatus ="There is no any information about your drain";
            }
        }//End Null status 
        return $theStatus;

    }

    function getDrainStatus($userId, $lang)
    {
        $dbcon = db();
        $sqlClaims = pg_query($dbcon,"SELECT * FROM drain_claims WHERE user_id=$userId");
        $mtaro = "";
        $drainstatus = "";
    
            if(pg_num_rows($sqlClaims) > 0) {
                while ($claimsInfo=pg_fetch_assoc($sqlClaims)) {
                    $mtaro = $claimsInfo['gid'];
                    $status = $claimsInfo['shoveled'];
                    $drainstatus .= giveStatus($mtaro,$status,$lang)."\n";
                }  
            }   
            else
            {
                if ($lang=="sw") {
                    $drainstatus ='Haujatwaa mtaro wowote, 
                            wasiliana na kiongozi wako wa mtaa kwa msaada zaidi.';
                } elseif ($lang=="en") {
                    $drainstatus ='You have not adopted any drain, contact your street leader for further assistance.';
                }
            }
        return "END ".$drainstatus;
    } //End Get Drain Status

    function getCollaborators($userId, $lang)
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
                if ($lang == "sw") {
                    $collaborators = 'Washirika wako ni: ';
                } elseif($lang == "en") {
                    $collaborators = 'Your collaborator(s) are: ';
                }
                
                //Get collaborators' names from users table
                while ($collaboratorInfo=pg_fetch_assoc($sqlCollaborator)) {
                    $user =$collaboratorInfo['user_id'];
                    $collaborators .= getUser($user).'  ';
                }
              } //End getting collaborators details
              else {
                    if ($lang == "sw") {
                         $collaborators = "Hauna washirika wowote kwenye mtaro wako";
                    }
                    elseif($lang == "en") {
                         $collaborators = "You don't have any collaborators on your drain";
                    }
                }
            } 
            else
            {
                if ($lang == "sw") {
                    $collaborators ="Hauna washirika wowote sababu haujatwaa mtaro wowote,
                            wasiliana na kiongozi wako wa mtaa kwa msaada zaidi.";
                } elseif($lang == "en") {
                    $collaborators ="You don't have any collaborators because you have not adopted any drain. Please contact your street leader for further assistance.";
                }
            } 
        return "END ".$collaborators;
    } //End getting collaborators


    //Get Drains from a specific street
    function getStreetDrains($streetId, $lang)
    {
        $dbcon = db();
        $drains = "";
        $sqlDrains = pg_query($dbcon,"SELECT * FROM drains_streets WHERE street_id=$streetId");
        if (pg_num_rows($sqlDrains) > 0) {
            
            while ($drainRows = pg_fetch_assoc($sqlDrains)) {
                $drain = $drainRows['drain_id'];
                
                $sqlDrainDetails = pg_query($dbcon,"SELECT * FROM mitaro_dar WHERE gid=$drain");
               
                $drainName = pg_fetch_assoc($sqlDrainDetails);
                $drains .= "\n".$drainName['gid'].", ".$drainName['address'];
            }
        }
        else {
            if ($lang == "sw") {
                $drains = "\n Hakuna mitaro yoyote katika mtaa huu";
            } else {
                $drains = "\n There are no drains in this street";
            }
        }
        return $drains;
    } //End getting drains from streets



    function getHelpCategories($lang) //Gets Help Categories from the DB
    {
        $dbcon = db();
        $categoriesMenu = "";
        $allCategories = count($categories);
        $sqlCategories = pg_query($dbcon,"SELECT * FROM need_help_categories");
        $categoryNo = 1;
            while ($categories=pg_fetch_assoc($sqlCategories)) {
                    $categoryId =$categories['id'];

                    if ($lang == "sw") {
                        $category =$categories['category_name'];
                        $categoriesMenu.= "\n".$categoryNo.". ".$category;
                    } 
                    elseif($lang == "en") 
                    {
                        if ($categoryId == 1) {
                                $category = "Tools are needed";
                                $categoriesMenu.= "\n".$categoryNo.". ".$category;
                            } 
                        elseif ($categoryId == 2){
                                $category = "The drain needs to be renovated";
                                $categoriesMenu.= "\n".$categoryNo.". ".$category;
                        }
                        elseif ($categoryId == 3){
                                $category = "The contractor did not collect trash";
                                $categoriesMenu.= "\n".$categoryNo.". ".$category;
                        }
                        elseif ($categoryId == 4){
                                $category = "Others";
                                $categoriesMenu.= "\n".$categoryNo.". ".$category;
                        }
                        
                    }
                    
            $categoryNo++;         
        }
        if ($lang == "sw") {
             return "CON Chagua aina ya msaada ". $categoriesMenu;
        } elseif ($lang == "en") {
             return "CON Choose help category ". $categoriesMenu;
        }
    }
    
    function askForHelp($res, $user, $lang)
    {      
        $dbcon = db();    
        $helpText = "";
        $helpDetails = explode("*", $res);

        if(count($helpDetails) == 1) {
            //Enter District
            $helpText .= dDistrictsMenu($lang);
            return $helpText;
        }
        else if(count($helpDetails) == 2) {
            //Enter Ward
            if ($helpDetails[1] == 1) { //Temporary Constraint
                $helpText .= dWardsMenu($lang);
            } else {

                if ($lang == "sw") {
                    $helpText .= "END Huduma hii haijafika kwenye wilaya/manispaa hii";
                } elseif($lang == "en") {
                    $helpText .= "END This service is not available for this district/municipal";
                }
            }
            return $helpText;
        }
        else if(count($helpDetails) == 3) {
            //Enter Street
            if ($helpDetails[2] == 1) { //Temporary Constraint
                $helpText .= dStreetsMenu($lang);
            } else {
                if ($lang == "sw") {
                    $helpText .= "END Huduma hii haijafika kwenye kata hii";
                } elseif($lang == "en") {
                    $helpText .= "END This service is not available in this ward";
                }
            }
            return $helpText;
        }
        
        else if(count($helpDetails) == 4) {
            //Enter Drain Id
            //$helpText .= "CON OMBA MSAADA  Ingiza namba ya mtaro";
            
            if ($lang == "sw") {
                $helpText .= "CON OMBA MSAADA Chagua mtaro ".getStreetDrains($helpDetails[3], $lang) ;
            } elseif($lang == "en") {
                $helpText .= "CON ASK FOR HELP. Pick a drain ".getStreetDrains($helpDetails[3], $lang) ;
            }
            
            return $helpText;
        }
        else if(count($helpDetails) == 5) {
            //Enter HelpCategory
            $helpText .= getHelpCategories($lang);
            return $helpText;
        }
        else if(count($helpDetails) == 6){
             if ($lang == "sw") {
                $helpText .= "CON Ongeza maelezo";
            } elseif($lang == "en") {
                $helpText .= "CON Add description";
            }
            return $helpText;
        }
        else if(count($helpDetails) == 7) {

            $districtId = $helpDetails[1];
            $wardId = $helpDetails[2];
            $streetId = $helpDetails[3];
            $drainId = $helpDetails[4];
            $helpCategory = $helpDetails[5];
            $helpNeeded = $helpDetails[6]; 

            //Send Help Details to the DB
            $sqlHelp = pg_query($dbcon, 
                "INSERT INTO need_helps( help_needed, gid, user_id, need_help_category_id, 
                created_at, updated_at) 
                VALUES ( '$helpNeeded', $drainId , $user, $helpCategory, now(), now())");
            $sqlHelpDrain = pg_query($dbcon, 
                "UPDATE mitaro_dar SET need_help=true WHERE gid=$drainId");

            if ($sqlHelp && $sqlHelpDrain) {
                if ($lang == "sw") {
                    return "END Umefanikiwa kuomba msaada kwa ajili ya mtaro namba ".$drainId;
                } elseif ($lang == "en") {
                    return "END You have succeeded to ask for help for drain no. ".$drainId;
                }
            } else {
                if ($lang == "sw") {
                    return "END Kuna tatizo limetokea, maombi yako ya msaada hayajatumwa.";
                } elseif ($lang == "en") {
                    return "END Something went wrong, your request was not processed.";
                }
            }
        }
    }

    //------------------------------------------------------------------------------
    //          END FUNCTIONS
    //------------------------------------------------------------------------------
    
?>