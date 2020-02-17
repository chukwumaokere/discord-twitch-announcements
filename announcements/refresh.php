<?php
/**
* All subscriptions have an expiration time, which cannot exceed 10 days. 
* To renew a subscription, make a new subscription request with the same parameters as the original request.
* https://dev.twitch.tv/docs/api/webhooks-guide/#subscriptions
* Set up to check daily on cron if time diff = 1 day, then renew and remove old sub
**/
chdir(dirname(__FILE__));
include('../../../db_twitch.php');
global $at, $db;

//DEPRECATED:
//$access_token = $at; 
$use = '';
$q = "SELECT access_token FROM rolling_access_token ORDER BY id DESC LIMIT 1";

if($res = $db->query($q)){
    while($row = $res->fetch_row()){
        $access_token = $row[0];
    }
}else{
    $cb_debug = "FAILED TO GRAB ACCESS TOKEN FROM DB";
    file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
}


$url = "https://api.twitch.tv/helix/webhooks/subscriptions";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 0);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec($curl);
$output_j = json_decode($output,true);

curl_close($curl);

if(sizeof($output_j['data']) == 0){ // || sizeof($output_j['data']) < SELECT user_id FROM announcement_store
    $cb_debug="output is empty using database instead";
    $use = 'db';
    file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
}else{
    $cb_debug="using data from webcall";
    $use = 'web';
    file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
}
if($use == 'web'){
    foreach($output_j['data'] as $subscription){
        $expiration_date = new DateTime($subscription['expires_at']);
        $exp_date = $expiration_date->format('Y-m-d H:i:s');
        $today = new DateTime();
        $today_f = $today->format('Y-m-d H:i:s');
        $date_diff = $expiration_date->diff($today);
        if($date_diff->days <= 1){
            $url = "https://api.twitch.tv/helix/webhooks/hub";
            // If theres 1 day left, delete the old subscription (since we're only allowed max of 3 and we dont want duplicates)
            $topic = $subscription['topic'];
            $callback = $subscription['callback'];

            //Remove the subscription
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "Authorization: Bearer $access_token"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = array("hub.callback" => "$callback", "hub.mode" => "unsubscribe", "hub.topic" => "$topic");
            $data_string = json_encode($data);
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            
            $output = curl_exec($curl);
            
            $cb_debug="Removed subscription: " . $topic . " expires at " . $subscription['expires_at'] . " (in " . $date_diff->days . " days)";
            file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
            
            curl_close($curl);

            //Renew that same subscription 
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "Authorization: Bearer $access_token"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = array("hub.callback" => "$callback", "hub.mode" => "subscribe", "hub.topic" => "$topic", "hub.lease_seconds" => 864000);
            $data_string = json_encode($data);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

            $output = curl_exec($curl);

            $cb_debug="Renewed subscription: " . $topic . " expires in 10 days";
            file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);

            curl_close($curl);
            
        }else{
            $topic = $subscription['topic'];
            // Else: do nothing. Keep current subscription
            $log = "Subscription ($topic) still valid, " . $date_diff->days . " days remaining before expiration\n";
            file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $log . "\n" . "-------------------------------------\n", FILE_APPEND);
        }
    }
}
if($use == 'db'){
    $sql = "SELECT user_id FROM announcement_store";
    if($res = $db->query($sql)){
        while($row = $res->fetch_row()){
            $user_id = $row[0];
            echo "\n" ."Adding Subscription for: " . $user_id . "\n";
            $today = new DateTime();
            $today_f = $today->format('Y-m-d H:i:s');
            $url = "https://api.twitch.tv/helix/webhooks/hub";
            $hubtopic = "https://api.twitch.tv/helix/streams?user_id=".$user_id;
            $data = array("hub.callback"=>"https://chukwumaokere.com/twitch/announcements/enroll.php", "hub.mode" => "subscribe", "hub.topic" => $hubtopic, "hub.lease_seconds" => 864000);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "Authorization: Bearer $access_token"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data_string = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $output = curl_exec($curl);

            $cb_debug="Created subscription: " . $hubtopic . " expires in 10 days";
            file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);

            curl_close($curl);
        }
    }else{
        $cb_debug = "FAILED TO GRAB USER IDS FROM DB";
        file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . $cb_debug . "\n" . "-------------------------------------\n", FILE_APPEND);
    }
}
?>
