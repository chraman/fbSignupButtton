<?php
	require 'php-sdk/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => '723871087684016',
		'secret' => 'd2c968dbddfa3089eb410739346a83cb'
	));

	setcookie('fbs_'.$facebook->getAppId(),'', time()-100, '/', 'https://www.swiftdeals.in/adsforads');
	session_destroy();
	header('Location: index.php');
?>
