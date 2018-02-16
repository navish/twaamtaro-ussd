<?php
    
    include_once("url-service.php");
    use GuzzleHttp\Client;
    
    function guzzleClient(){
        $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => "http://localhost:3000/api/v1/",
        // You can set any number of default request options.
        'timeout'  => 2.0,
        ]);
        return $client;
    }
    

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

        $streetsList  = '';           
            if ($lang == "sw") {
             $streetsMenu = "CON Chagua mtaa ".$streetsList; 
            } elseif ($lang == "en") {
             $streetsMenu = "CON Choose street ".$streetsList; 
            }
        return $streetsMenu;
    }

    //Display a list of all wards
    function dWardsMenu($lang) {
        $wardsList = " ";

        
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
    function userLangAssign($phoneNumber)
    {
        $lang = 'sw';
        return $lang;
    }
    //Updates the user language
    //Params user number
    function updateUserLang($phoneNumber)
    {
        $lang = changeLang($phoneNumber);        
        return $lang;
    }

    //------------------------ END LANGUAGE FUNCTIONS -----------------------------//
    

    //------------------------ BEGIN MAIN FUNCTIONS -----------------------------//
    //-----------------------------------------------------------------------------//
    function getUser($phoneNumber){
        $client = guzzleClient();
        $theuser = $client->request('POST', 'users/ussd_user/'.$phoneNumber);
        $user = $theuser->getBody();
        $userDetails = json_decode($user);
        return $userDetails;
    }

    //----------------------------- END MAIN FUNCTIONS ---------------------------//
    
    
    //------------------------------------------------------------------------------
    //          END FUNCTIONS
    //------------------------------------------------------------------------------
    
?>