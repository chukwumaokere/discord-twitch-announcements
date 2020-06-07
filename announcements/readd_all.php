<?php

require_once('getCurrentSubs.php');
require_once('getCurrentAccessToken.php');

$subs = getCurrentSubs();
$at = getCurrentAccessToken();

var_dump($subs);
var_dump($at);

foreach ($subs as $sub){
    echo "\n" ."Adding Subscription for: " . $sub . "\n";
    $url = "https://api.twitch.tv/helix/webhooks/hub";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "Authorization: Bearer $at", "Client-ID: cr8inwbvopqkqkt9mcb31y3yqtkspp"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $data = array("hub.mode" => "subscribe", "hub.callback" => "https://chukwumaokere.com/twitch/announcements/enroll.php", "hub.lease_seconds" => 864000, "hub.topic" => "https://api.twitch.tv/helix/streams?user_id=$sub");
    $data_string = json_encode($data);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    var_dump($data_string);

    $output = curl_exec($curl);
    $output_p = json_decode($output);

    if(@$output_p->status != 400){
        $cb_debug="Created subscription: " . $sub . " expires in 10 days (using: readd_all)";
        echo $cb_debug . "\n\n";
        file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
    }else{
        echo "RAN INTO AN ERROR: \n";
        var_dump($output);
    }

    curl_close($curl);
}
