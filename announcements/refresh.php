<?php
/**
* All subscriptions have an expiration time, which cannot exceed 10 days. 
* To renew a subscription, make a new subscription request with the same parameters as the original request.
* https://dev.twitch.tv/docs/api/webhooks-guide/#subscriptions
* Set up to check daily on cron if time diff = 1 day, then renew and remove old sub
**/
include('../../../db_twitch.php');
global $at, $db;

$access_token = $at; 

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
        echo "Subscription ($topic) still valid, " . $date_diff->days . " days remaining before expiration\n";
    }
}
?>
