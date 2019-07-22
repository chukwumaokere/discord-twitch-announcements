<!DOCTYPE html>
<title> Authenticate Application </title>
<head> 
<head>

<body>
    <input type="hidden" id="client_id" name="client_id" value="cr8inwbvopqkqkt9mcb31y3yqtkspp"/>
<?php
    header('Location: https://id.twitch.tv/oauth2/authorize?client_id=cr8inwbvopqkqkt9mcb31y3yqtkspp&redirect_uri=https://chukwumaokere.com/twitch/announcements/store.php&response_type=code&scope=&claims={"id_token":{"preferred_username":null}}');
?>
</body>
</html>
