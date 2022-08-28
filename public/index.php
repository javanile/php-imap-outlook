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
$profileUri = 'https://graph.microsoft.com/v1.0/me';
$resourceId = 'https://graph.microsoft.com/';

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

    <center>
    <button onclick="window.location='<?=$authUri?>'">
        <img height="24" width="24" style="vertical-align:middle" src="/logo.svg" />
        Sign in with Microsoft
    </button>
    </center>
</p>

<?php
if (isset($_SESSION['auth']['access_token'])) {
    $inbox = '{outlook.office365.com:993/imap/ssl}';
    $username = $_SESSION['user']['mail'];
    $accessToken = $_SESSION['auth']['access_token'];
    $imap = imap2_open($inbox, $username, $accessToken, OP_XOAUTH2);
    $info = imap2_mailboxmsginfo($imap);
    var_dump($info);
}
?>

<?php if (isset($_SESSION['auth'])) { ?>
    <pre>$_SESSION['auth'] = <?php var_dump($_SESSION['auth']); ?></pre>
<?php } ?>

<?php if (isset($_SESSION['user'])) { ?>
    <pre>$_SESSION['user'] = <?php var_dump($_SESSION['user']); ?></pre>
<?php } ?>

<a href="https://github.com/javanile/php-imap-outlook" target="_blank"><img src="https://github.blog/wp-content/uploads/2008/12/forkme_right_green_007200.png?resize=149%2C149" style="position:absolute;top:0;right:0;border:0;" width="149" height="149" class="attachment-full size-full" alt="Fork me on GitHub" data-recalc-dims="1" /></a>
</body>
</html>
