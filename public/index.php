<?php

#require_once __DIR__.'/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

#$dotenv = Dotenv\Dotenv::createImmutable(getcwd());
#$dotenv->load();

#var_dump($_ENV);

#header('Content-Type: text/html; charset=utf-8');
$clientId = $_ENV['MICROSOFT_CLIENT_ID'];
$tenantId = $_ENV['MICROSOFT_TENANT_ID'] ?? 'common';
$client_secret = $_ENV['MICROSOFT_CLIENT_SECRET'];
$redirect_uri  = "http://localhost:8080/office365api/index.php";

$scope = explode(',', $_ENV['MICROSOFT_SCOPE']);

$encodedScope = urlencode(implode(' ', $scope));

$response   = "";
$response   = "https://login.microsoftonline.com/".$tenantId."/oauth2/authorize?client_id=".$client_id."&scope=".$encodedScope."&response_type=code&redirect_uri=".urlencode($redirect_uri);  //&prompt=consent


echo "<h2>office 365 using PHP login</h2>";
echo "<br>";
if(!isset($_GET['code']))
{
    echo "LOGIN  :: ";
    echo "<span style='vertical-align: middle;'><a href='".$response."'>LOGIN</a></span>";
}



$arraytoreturn = array();
$output = "";
//  Redeem the authorization code for tokens office 365 using PHP
if(isset($_GET['code']))
{
    $auth = $_GET['code'];
    $resource_id = "https://api.office.com/discovery/";
    $data = "client_id=".$client_id."&redirect_uri=".urlencode($redirect_uri)."&client_secret=".urlencode($client_secret)."&code=".$auth."&grant_type=authorization_code&resource=".$resource_id;
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