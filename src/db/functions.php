<?php
//    include "clsconnection.php";

    function GeneratePasswordResetCode () {
        $con = new Connection;
        
        $validchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        $validchararr = str_split($validchars);
        $resetcode = "";    
        
        $goodcode = false;
        $con->OpenConnection();
        while (!$goodcode) {
            $resetcode="";
            for ($i=1;$i<=30;$i++) {
                $resetcode = $resetcode . $validchararr[rand(1, strlen($validchars )- 1)];
            }
            
            $con->query("SELECT * FROM users WHERE resetcode='".$resetcode."'");
            $con->CloseRecordset();
                
            $hasrow = $con->hasrow();
            $goodcode = $hasrow != true;
        }
        $con->CloseConnection();
        
        return $resetcode;
    }
?>