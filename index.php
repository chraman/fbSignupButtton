<?php
	require 'php-sdk/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => '1558311771076814',
		'secret' => '92454ddfb405669c03ecd5a5c49547f7'
	));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Facebook SignUp</title>
</head>
<body>
<?php

	$user = $facebook->getUser();
	if ($user): 
		$user_graph = $facebook->api('/me');
		echo '<h1>Hello ',$user_graph['first_name'],'</h1>';	
	    echo '<p>Your email id is: ', $user_graph['email'],'</p>';
	    echo '<p><a href="logout.php">logout</a></p>';
	    echo '<img src="http://swiftintern.com/library/facebook/process_facebook.php?name='.$user_graph['name'].'&email='.$user_graph['email'].'&link='.$user_graph['link'].'"/>';
	else: 
		$loginUrl = $facebook->getLoginUrl(array(
			'diplay'=>'popup',
			'scope'=>'email',
			'redirect_uri' => 'https://apps.facebook.com/sign_up_test_me'
		));
		echo '<button><a href="', $loginUrl, '" target="_top">login</a></button>';
	endif; 

	
	
	
?>
</body>
</html>