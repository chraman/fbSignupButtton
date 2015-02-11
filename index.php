<?php

	
	defined('DB_SERVER') ? null : define('DB_SERVER', 'newswiftintern.db.11823432.hostedresource.com');
	defined('DB_USER') ? null : define('DB_USER', 'newswiftintern');
	defined('DB_PASS') ? null : define('DB_PASS', 'phpOTL123@');
	defined('DB_NAME') ? null : define('DB_NAME', 'newswiftintern');
	require_once('functions.php');
	require_once('class.database.php');
	require_once('class.database_object.php');
	require_once('class.user.php');
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
		$auth 		= 'student';
	$member_id 	= '';

	//compare user id in our database
	
		$user_exist = User::find_by_email($user_graph['email']);
	if($user_exist) {
		echo "welcome  back";
	}else{ 
		//user is new
		echo 'Hi '.$user_name.', Thanks for Registering!<br>Redirecting You Please wait...';
		//register
		$newuser = new User();
		$newuser->name = $user_graph['name'];
		$newuser->email = $user_graph['email'];
		$newuser->access_token = rand(9, 99999999);
		$newuser->type = 'student';
		$newuser->validity = '1';
		$newuser->last_ip = get_client_ip();
		$newuser->created = $time;

		if ($newuser->save()) {
			$student = new Student();
			$student->user_id = $newuser->id;
			$social 					= new Social();
			$social->user_id 			= $newuser->id;
			$social->social_platform	= 'facebook';
			$social->link 				= $user_graph['link'];
			$social->created 			= $time;
			$social->save();
			if($student->save()){
			}
		}else {
			echo "<br>Unable to save";
		}	
	}
	    echo '<p><a href="logout.php">logout</a></p>';
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