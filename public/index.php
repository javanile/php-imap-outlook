<?php

$clientId = getenv('MICROSOFT_CLIENT_ID');
$tenantId = getenv('MICROSOFT_TENANT_ID') ?: 'common';
$clientSecret = getenv('MICROSOFT_CLIENT_SECRET');
$redirectUri = getenv('REDIRECT_URI');

$scopes = [
    'https://outlook.office.com/IMAP.AccessAsUser.All',
    'https://outlook.office.com/POP.AccessAsUser.All',
    'https://outlook.office.com/SMTP.Send'
];

$authUri = 'https://login.microsoftonline.com/' . $tenantId
         . '/oauth2/authorize?client_id=' . $clientId
         . '&scope=' . urlencode(implode(' ', $scopes))
         . '&redirect_uri=' . urlencode($redirectUri)
         . '&response_type=code'
         . '&prompt=consent';

$arrayToReturn = array();
$output = "";

if (isset($_GET['code'])) {
    $auth = $_GET['code'];
    $resourceId = "https://api.office.com/discovery/";
    $data = "client_id=".$clientId."&redirect_uri=".urlencode($redirectUri)."&client_secret=".urlencode($clientSecret)."&code=".$auth."&grant_type=authorization_code&resource=".$resourceId;
    try
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://login.microsoftonline.com/common/oauth2/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
        ));
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
    }
    catch (Exception $exception)
    {
        var_dump($exception);
    }

    var_dump($output);

    $out2 = json_decode($output, true);
    $get_access_token = $out2['access_token'];
    $get_refresh_token = $out2['refresh_token'];
    $arraytoreturn = Array(
        'access_token' => $out2['access_token'],
        'refresh_token' => $out2['refresh_token'],
        'expires_in' => $out2['expires_in']
    );
    echo "Get access toke and refresh token in office 365 using PHP<br>";
    echo "access token :: ".$get_access_token."<br>";
    echo "refresh token :: ".$get_refresh_token."<br>";
}

?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<title>PHP IMAP Outlook</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://www.javanile.org/crisp/css/crisp.css">
</head>
<body>
<h1>PHP IMAP Outlook</h1>
<p>
    <?php
    if (true)
    if (!isset($_GET['code'])) {}
    ?>

    <a href="<?=$authUri?>">Login</a>

</p>
</body>
</html>


