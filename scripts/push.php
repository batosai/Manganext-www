<?php

require_once __DIR__ . '/bootstrap.php';

$apns = new Easyapns_APNS();

$deviceTable = Model_ApnsDeviceTable::getInstance();
$bookTable = Model_BookTable::getInstance();

$devices = $deviceTable->createQuery('ad')->select('ad.*')
                                    	  ->where('ad.status = "active"')
                                    	  ->execute();

$books = $bookTable->createQuery('b')->select('b.*')
                                     ->where('TO_DAYS(published_at) = TO_DAYS(NOW())')
                                     ->andWhere('name != ""')
                                     ->orderBy('published_at DESC')
                                     ->execute();

if ($books->count())
{
    $apns->newMessage($devices->toKeyValueArray('pid', 'pid'));
	$apns->addMessageAlert('Venez découvrir les nouvelles sorties');
	$apns->addMessageSound('chime');
	$apns->addMessageBadge($books->count());
	$apns->queueMessage();
}
?>