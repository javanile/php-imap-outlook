<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$clientId = getenv('MICROSOFT_CLIENT_ID');
$tenantId = getenv('MICROSOFT_TENANT_ID') ?: 'common';
$clientSecret = getenv('MICROSOFT_CLIENT_SECRET');
$redirectUri = getenv('REDIRECT_URI');

$scopes = [
    'https://outlook.office.com/IMAP.AccessAsUser.All',
    'https://outlook.office.com/POP.AccessAsUser.All',
    'https://outlook.office.com/SMTP.Send',
    'https://graph.microsoft.com/User.Read',
    'https://graph.microsoft.com/Mail.Read',
];

$authUri = 'https://login.microsoftonline.com/' . $tenantId
         . '/oauth2/authorize?client_id=' . $clientId
         . '&scope=' . urlencode(implode(' ', $scopes))
         . '&redirect_uri=' . urlencode($redirectUri)
         . '&response_type=code'
         . '&prompt=consent';

$tokenUri = 'https://login.microsoftonline.com/common/oauth2/token';
$resourceId = 'https://graph.microsoft.com/';
$profileUri = 'https://graph.microsoft.com/v1.0/me';

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
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
    $_SESSION['auth'] = json_decode(curl_exec($curl), true);
    $_SESSION['user'] = null;
    header('Location: '.$redirectUri);
    exit();
}

if (isset($_SESSION['auth']['access_token']) && empty($_SESSION['user'])) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $profileUri);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $_SESSION['auth']['access_token']]);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    $_SESSION['user'] = json_decode(curl_exec($curl), true);
    header('Location: '.$redirectUri);
    exit();
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

    <a href="<?=$authUri?>">Authorize</a>

</p>

<?php
if (isset($accessToken['access_token'])) {
    $inbox = '{outlook.office365.com:993/imap/ssl}';
    #$imap = imap2_open($inbox, $);


}
?>

<pre>$_SESSION['access_token'] = <?php var_dump($_SESSION['access_token']); ?></pre>

<pre>$_SESSION['user'] = <?php var_dump($_SESSION['user']); ?></pre>

</body>
</html>
