<?php
$curl = curl_init( 'https://public-api.wordpress.com/oauth2/token' );
curl_setopt( $curl, CURLOPT_POST, true );
curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
    'client_id' => 'I91bfwX1qRQqcB3oqxDh1I37JTuJLN84Pwrrq3Gi',
    'redirect_uri' => 'https://sarala.it/agile/',
    'client_secret' => 'ikFVszi6NRBkwRknkgcsvmyUMOgPels8DsHpMPJR',
    'code' => $_GET['code'], // The code from the previous request
    'grant_type' => 'authorization_code'
) );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
$auth = curl_exec( $curl );
$secret = json_decode($auth);
$access_key = $secret->access_token;
?>