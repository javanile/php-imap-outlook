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

$tokenUri = 'https://login.microsoftonline.com/common/oauth2/token';
$resourceId = 'https://api.office.com/discovery/';
$accessToken = null;

if (isset($_GET['code'])) {
    $postFields = 'client_id=' . $clientId
                . '&redirect_uri=' . urlencode($redirectUri)
                . '&client_secret=' . urlencode($clientSecret)
                . '&code=' . $_GET['code']
                . '&resource=' . urlencode($resourceId)
                . '&grant_type=authorization_code';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $tokenUri);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
    $response = curl_exec($curl);
    $accessToken = json_decode($response, true);
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

<pre>$accessToken = <?php var_dump($accessToken); ?></pre>


<?php if (isset($accessToken['access_token'])) { ?>



<?php } ?>

</body>
</html>
