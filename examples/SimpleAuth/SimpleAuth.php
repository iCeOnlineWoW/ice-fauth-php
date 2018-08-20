<?php

// load common config
$config = require __DIR__."/SimpleAuthConfig.php";

// instantiate FAuth client
$fauth = new FAuthClient($config['service'], $config['secret'], $config['nodes']);
// retrieve auth URL
$authUrl = $fauth->getAuthURL($config['callback']);

// output a link to user, so he can click on it, be redirected to auth page, and the auth server will maintain the rest of the flow
// - after (un)successfull authentication, the user is redirected to callback URL supplied to getAuthURL above
// - for validation flow, see SimpleAuthCallback.php (which is the actual callback)
echo "<a href=\"".$authUrl."\">Click here to authenticate using FAuth</a>";
