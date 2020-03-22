<?php

include 'db/clsconnection.php';

$cn = new Connection;

$cn->OpenConnection();

header("Cache-Control: max-age=3600");

$valid_ip = array(
    "49.144.231.195", "114.108.237.135", "112.198.230.82", "180.190.114.199", "124.106.26.74", 
    "49.144.72.2", "112.211.254.145"
);

$contacts = array();

if(in_array($_SERVER['REMOTE_ADDR'], $valid_ip)) {
    $cn->query("SELECT * FROM microsip_contacts ORDER BY extension");
    while($row = $cn->getrow()) {
        array_push($contacts, array(
            "number" => $row['extension'],
            "name" => $row['fname'] . ' ' . $row['lname'],
            "firstname" => $row['fname'], 
            "lastname" => $row['lname'], 
            "phone" => "", "mobile" => "", "email" => "", 
            "address" => "", "city" => "", "state" => "", "zip" => "", 
            "comment" => "", "presence" => 0, "info" => ""
        ));
    }
}

$contacts = array_values($contacts);

echo json_encode($contacts);