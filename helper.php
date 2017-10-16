<?php
		
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
              echo $drainstatus; 
              echo $mhusika; 

            } else {
              echo $mitaro ="No Claims";
              
            }
        	
      		


?>