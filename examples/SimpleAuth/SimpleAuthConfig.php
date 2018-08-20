<?php

// composer autoload - note this is not valid when downloading this library through composer
require __DIR__."/../../vendor/autoload.php";
// require our FAuthClient
require __DIR__."/../../src/FAuthClient.php";

// prepare callback URL - this just builds callback URL from our current location, and replacing the script name
// with our known callback script name
$callbackUrl = 'http://'.$_SERVER['HTTP_HOST'];

$uri = $_SERVER['REQUEST_URI'];
$pos = strrpos($uri, '/');
$callbackUrl .= substr($uri, 0, $pos + 1);
$callbackUrl .= "SimpleAuthCallback.php";

return [
    'service' => 'blank',
    'secret' => 'SET_TO_YOUR_SECRET',
    'nodes' => [ 'SET_TO_YOUR_FAUTH_NODE', 'SET_TO_YOUR_ANOTHER_FAUTH_NODE' ],
    'callback' => $callbackUrl
];
