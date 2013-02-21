<?php

require_once __DIR__ . '/bootstrap.php';

$apns = new Easyapns_APNS();
$apns->processQueue();

?>