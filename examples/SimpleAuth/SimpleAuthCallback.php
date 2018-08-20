<?php

// load common config
$config = require __DIR__."/SimpleAuthConfig.php";

// instantiate FAuth client
$fauth = new FAuthClient($config['service'], $config['secret'], $config['nodes']);
// the FAuth node performed callback with an additional token parameter - retrieve it
// and perform validation - the server will reply with user info
$user = $fauth->validateToken($_GET['token']);

// something went wrong
if (!$user)
    die("Auth failed!");

echo "Welcome user '".$user['user']['username']."' with email ".$user['user']['email'];
