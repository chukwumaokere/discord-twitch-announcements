<?php
chdir(dirname(__FILE__));
include("../../../db_twitch.php");
include("../../../db_discord.php");
global $db, $cs, $db_discord;
$client_id="cr8inwbvopqkqkt9mcb31y3yqtkspp";

$code = '036dxpzr3wftflr2gh3phpk9x53x1o';

$q = "SELECT access_token, expires_in, created_date FROM rolling_access_token ORDER BY id DESC LIMIT 1";
$needs_renewal = false;
$nr = "No";
if($res = $db->query($q)){
    while($row = $res->fetch_row()){
        $expires_in = $row[1];
        $created_date = $row[2];
    }
    $current_date = date('Y-m-d H:i:s');
    $expiration_date = date(('Y-m-d H:i:s'), strtotime($created_date) + $expires_in - 432000); //subtract 5 days from expiration date, so we can renew 5 days in advance
    if($current_date > $expiration_date){
        $needs_renewal = true;
        $nr = "Yes";
    }
    $cb_debug = "Token Expiration Date: $expiration_date, Needs renewal: $nr";
}else{
    //some debugging
    $cb_debug = "FAILED TO GET ACCESS TOKEN INFO FROM DB";
}
file_put_contents('./test.txt', date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);


if($code && $nr == "Yes"){
/*
    //Get User Token
    $url = "https://id.twitch.tv/oauth2/token?client_id=$client_id&client_secret=$cs&code=$code&grant_type=authorization_code&redirect_uri=https://chukwumaokere.com/twitch/announcements/store.php";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);

    $output_j = json_decode($output,true);
    $access_token = $output_j['access_token'];
    $expires_in = $output_j['expires_in'];
    $refresh_token = $output_j['refresh_token'];
    $token_type = $output_j['token_type'];

    var_dump($output_j);

    curl_close($curl);
*/
    //Get App token 
    $url = "https://id.twitch.tv/oauth2/token?client_id=$client_id&client_secret=$cs&grant_type=client_credentials";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);

    $output_j = json_decode($output,true);

    $app_access_token = $output_j['access_token'];

    var_dump($output_j);
    $at = $output_j['access_token'];
    $ei = $output_j['expires_in'];
    $tt = $output_j['token_type'];
    $cd = date('Y-m-d H:i:s');

    $q = "INSERT INTO rolling_access_token (access_token, expires_in, token_type, created_date) VALUES ('$at', '$ei', '$tt', '$cd')";
    $insert = $db->query($q);

    if($insert == true){
        file_put_contents('./test.txt', date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . "GENERATED AND INSERTED TOKEN $at INTO DB" . "\n" . "-------------------------------------\n", FILE_APPEND);
    }else{
        file_put_contents('./test.txt', date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . 'FAILED TO GENERATE AND INSERT NEW TOKEN' . "\n" . "-------------------------------------\n", FILE_APPEND); 
    }

    curl_close($curl);
}else{
    //No Renewal Needed
    $cb_debug = "NO ACCESS TOKEN RENEWAL NEEDED AT THIS TIME";
    file_put_contents('./test.txt', date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
}
