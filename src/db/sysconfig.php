<?php

$config = new Connection;

$config->OpenConnection();

$config->query("SELECT * FROM system_variables");

while($res = $config->getrow()) {
    #if(!defined('EmailPwd')) DEFINE('EmailPwd', $res['config']);
    switch ($res['variable']) {
        case 'SYSTEM_EMAIL_ACCOUNT':
            DEFINE('EmailAcct', $res['config']);
            break;
        case 'SYSTEM_EMAIL_PASSWORD':
            DEFINE('EmailPwd', $res['config']);
            break;
    }
}

$config->CloseConnection();
