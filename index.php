<?php
	require 'php-sdk/facebook.php';
	$facebook = new Facebook(array(
		'appId'  => '764344836948245',
		'secret' => 'c226c68cd859fa01b892d36076bac6ca'
	));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Facebook SignUp</title>
</head>
<body>
<?
	$user = $facebook->getUser();
	if ($user): 
		$user_graph = $facebook->api('/me');
		$user_graph_page = $facebook->api('me?fields=accounts');
		echo '<h1>Hello ',$user_graph['first_name'],'</h1>';
		echo '<p>Your birthday is: ',$user_graph['birthday'],'</p>';
		echo '<p>Your User ID is: ', $user, '</p>';
		echo '<p>Your College is: ', $user_graph['education'],'</p>';
		echo '<p><a href="logout.php">logout</a></p>';
	    echo '<p>Your email id is: ', $user_graph['email'],'</p>';
	    echo '<p>Your gender is: ', $user_graph['gender'],'</p>';
		$loginUrl = $facebook->getLoginUrl(array(
			'diplay'=>'popup',
			'scope'=>'email',
			'redirect_uri' => 'https://fbsignupbutton.herokuapp.com/'
		));
		echo '<button><a href="', $loginUrl, '" target="_top">login</a></button>';
	endif; 
	//echo '<p><a href="insta.php">insta</a></p>';
?>
</body>
</html>