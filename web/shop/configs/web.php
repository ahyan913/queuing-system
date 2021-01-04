<?php

$domain = "http://vps8592-stage-form.youdomain.hk/";

$waitingPage = "http://vps8592-stage-waiting.youdomain.hk";

$apiAuthKey = 'staffsaleshk.com';

$apiAuthToken = 'IeDny3UaSb0yr6ADIq8XyT8IR4CAwr9f';

$waitingPageStatusUrl = $waitingPage.'/status.php';

$waitingPageDataUrl = $waitingPage.'/data.php';

//alert at last 1 minutes
$alertLevel = 1;

$expireMessage = 'You are expire now ';

$alertMessage = 'Your session is going to be expired within '.$alertLevel.' minutes';