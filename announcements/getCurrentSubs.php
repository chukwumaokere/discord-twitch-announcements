<?php

chdir(dirname(__FILE__));
include('../../../db_twitch.php');
global $at, $db;

$q = "SELECT DISTINCT(user_id) FROM announcement_store";

$res = $db->query($q);

while($row = $res->fetch_array()){
    $subscribers[] = $row['user_id'];
}
//var_dump($subscribers);


function getCurrentSubs(){
    global $at, $db;
    $subs = [];
    $q = "SELECT DISTINCT(user_id) FROM announcement_store";
    $res = $db->query($q);

    while($row = $res->fetch_array()){
        $subs[] = $row['user_id'];
    }
    return $subs;
}
