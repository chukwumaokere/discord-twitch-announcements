<?php

chdir(dirname(__FILE__));
include('../../../db_twitch.php');
global $at, $db;

$q = "SELECT access_token, expires_in, created_date FROM rolling_access_token ORDER BY id DESC LIMIT 1";
$needs_renewal = false;
$nr = "No";
if($res = $db->query($q)){
    while($row = $res->fetch_row()){
        $expires_in = $row[1];
        $created_date = $row[2];
    }
    $current_date = date('Y-m-d H:i:s');
    $current_date = '2020-02-20 00:00:00';
    $expiration_date = date(('Y-m-d H:i:s'), strtotime($created_date) + $expires_in - 432000); //subtract 3 days from expiration date, so we can renew 5 days in advance
    if($current_date > $expiration_date){
        $needs_renewal = true;
        $nr = "Yes";
    }
    $cb_debug = "$current_date: Token Expiration Date: $expiration_date, Needs renewal: $nr\n";
    echo ($cb_debug);
}else{
    //some debugging
    $cb_debug = "FAILED TO GET ACCESS TOKEN INFO FROM DB";
}


