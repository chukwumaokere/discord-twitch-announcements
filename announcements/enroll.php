<?php
$inputJSON = file_get_contents("php://input");
$j = json_decode($inputJSON, true);
$r = print_r(json_decode($inputJSON, true),true);

$challenge = $_REQUEST['hub_challenge'];
$challenge_is = "";
if($challenge){
    header("HTTP/1.1 200");
    echo $challenge;
    $challenge_is = "Challenge is: $challenge\n";
}
/*
include("../../../db_twitch.php");
include("../../../db_discord.php");
global $db, $cs, $db_discord;

$r = print_r($_REQUEST, true);
$challenge = $_REQUEST['hub_challenge'];
header("HTTP/1.1 200");
echo $challenge;

$app_access_token = $_REQUEST['app_access_token'];
$access_token = $_REQUEST['access_token'];
$client_id = $_REQUEST['client_id'];
$user_id = $_REQUEST['user_id'];

//Check subscriptions
$url = "https://api.twitch.tv/helix/webhooks/subscriptions";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $app_access_token"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec($curl);
$output_j = json_decode($output,true);

$output_s=print_r($output,true);

curl_close($curl);

//Check stream data
$url = "https://api.twitch.tv/helix/streams?user_id=$user_id";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 0);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec($curl);
$output_j = json_decode($output,true);

$output_d = print_r($output_j,true);
/*
if (!empty($output_d['data'])){
    $d = $output_d['data'][0];
    $url = "http://localhost:13337";
    $curl 
    curl http://localhost:1337/ $postdata=$d
}
*/
//Log and save data
//file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . "$r\nChallenge is: $challenge\nSubs: $output_s\nStream data: $output_d\n" . "-------------------------------------\n", FILE_APPEND);

if (!empty($j['data'])){
    $url = "http://localhost:13337";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $data = $j['data'][0];
    $data_string = json_encode($data);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    curl_exec($curl);
    curl_close($curl);
}

file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . "$r\n". $challenge_is . "-------------------------------------\n", FILE_APPEND);
?>
