<?php
include("../../../db_twitch.php");
include("../../../db_discord.php");
global $db, $cs, $db_discord;
$client_id="cr8inwbvopqkqkt9mcb31y3yqtkspp";

$params = $_GET;

$code = $params["code"];
if($code){
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

    curl_close($curl);

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

    curl_close($curl);

    //Get User Info
    if($access_token && $access_token !== NULL){
        $url = "https://id.twitch.tv/oauth2/userinfo";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_HEADER, FALSE);
        curl_setopt($crl, CURLOPT_HTTPHEADER, array("Authorization: Bearer $access_token")); 
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($crl);

        $output_j = json_decode($output,true);

        $preferred_name = $output_j['preferred_username']; 
        $user_id = $output_j['sub'];

        curl_close($crl);

        if ($user_id){
            $url = "https://api.twitch.tv/helix/webhooks/hub";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HEADER, FALSE);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', "Authorization: Bearer $access_token"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = array("hub.callback" => "https://chukwumaokere.com/twitch/announcements/enroll.php", "hub.mode" => "subscribe", "hub.topic" => "https://api.twitch.tv/helix/streams?user_id=$user_id", "hub.lease_seconds" => 864000);
            $data_string = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

            $output = curl_exec($curl);

            $output_j = json_decode($output,true);

            /*Troubleshooting 
            var_dump($user_id);
            var_dump($preferred_name);
            var_dump($access_token);
            var_dump($app_access_token);
            */

            $cb_debug="https://chukwumaokere.com/twitch/announcements/enroll.php?client_id=$client_id&app_access_token=$app_access_token&access_token=$access_token&user_id=$user_id";
            file_put_contents("./test.txt", date('Y-m-d H:i:s') . ":\n-------------------------------------\n" . "cb_debug: $cb_debug" . "\n" . "-------------------------------------\n", FILE_APPEND);

            curl_close($curl);
            /*
            $success = file_get_contents("./success.php");
            echo $success;
            */
            $auth_code = generateRandomString(12, $preferred_name);
            if(empty($output_j)){
                $store = saveToDatabase($preferred_name, $access_token, $app_access_token, $client_id, $user_id, $auth_code);
                if($store){
                    header("Location: ./success.php?code=$auth_code");
                }else{
                    echo "Query entry failed... please try again. <br> Please @Pinót in discord with this message";
                }
            }else{
                $err_code = $output_j['status'];
                $errr = $output_j['error'];
                $mess = $output_j['message'];
                echo "Error $err_code: $errr <br> Message: $mess <br> Please @Pinót in discord with this message";
            }
        }
    }
}

function saveToDatabase($preferred_name, $access_token, $app_access_token, $client_id, $user_id, $auth_code){
    global $db, $db_discord;
    $date_entered = date('Y-m-d H:i:s');

    $q1 = "INSERT INTO announcement_store (user_id, preferred_name, access_token, app_access_token, client_id, auth_code, date_entered) VALUES ('$user_id', '$preferred_name', '$access_token', '$app_access_token', '$client_id', '$auth_code', '$date_entered')";
    $add1 = $db->query($q1);
    
    $q2 = "INSERT INTO twitch_announcements (t_user_id, t_user_name, auth_code) VALUES ('$user_id', '$preferred_name', '$auth_code')";
    $add2 = $db_discord->query($q2);
    if ($add1 && $add2){
        return true;    
    }else{
        return false;
    }
}

function generateRandomString($length, $string) {
    $include_chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charLength = strlen($include_chars);
    $randomString = "$string"."_";
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $include_chars [rand(0, $charLength - 1)];
    }
    return $randomString;
}


?>
