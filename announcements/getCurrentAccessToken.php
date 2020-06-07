<?php

chdir(dirname(__FILE__));
include('../../../db_twitch.php');
global $at, $db;

$q = "SELECT access_token FROM rolling_access_token ORDER BY id DESC LIMIT 1";

if($res = $db->query($q)){
    while($row = $res->fetch_row()){
        $access_token = $row[0];
    }
}

//var_dump($access_token);

function getCurrentAccessToken(){
    global $at, $db;
    $q = "SELECT access_token FROM rolling_access_token ORDER BY id DESC LIMIT 1";

    if($res = $db->query($q)){
        while($row = $res->fetch_row()){
            $access_token = $row[0];
        }
    }

    return $access_token;
}

