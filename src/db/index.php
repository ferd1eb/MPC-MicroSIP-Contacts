<?php

    ini_set('session.gc_maxlifetime', 3600);
    session_set_cookie_params(3600);

    session_start();

    if (!isset($_SESSION['sts_login_id'])) {        
        header('Location: ../signin');
    } else {
        if ($_SESSION['sts_login_id'] != "") {
            header('Location: ../home');
        } else {
            header('Location: ../signin');
        }
    }

//    header('Location: home');
?>