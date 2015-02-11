<?php
/**
 * User
 * 
 * @package Main
 * @subpackage Basic
 * @author Faizan Ayubi
 */
class User extends DatabaseObject
{
	protected static $table_name = "users";
	public $id;
	public $name;
	public $email;
	public $password;
	public $phone;
	public $access_token;
	public $login_number;
	public $type;
	public $validity;
	public $last_ip;
	public $last_login;
	public $created;
	public $updated;

	protected static $db_fields = array('id', 'name', 'email', 'password', 'phone', 'access_token', 'login_number', 
		'type', 'validity', 'last_ip', 'last_login', 'created', 'updated');

	public static function login_counter($id) {
		global $database;
		$sql  = "UPDATE ".self::$table_name." SET ";
		$sql .= "login_number = login_number+1 ";
		$sql .= "WHERE id = {$id}";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public static function login_time($id) {
		global $database, $time;
		$sql  = "UPDATE ".self::$table_name." SET ";
		$sql .= "last_login = '{$time}' ";
		$sql .= "WHERE id = {$id}";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	public static function last_ip($ip, $id) {
		global $database;
		$sql  = "UPDATE ".self::$table_name." SET ";
		$sql .= "last_ip = '{$ip}' ";
		$sql .= "WHERE id = {$id}";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	public static function change_access($access_token, $id) {
		global $database;
		$sql  = "UPDATE ".self::$table_name." SET ";
		$sql .= "access_token = '{$access_token}' ";
		$sql .= "WHERE id = {$id}";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	public static function authenticate($email="", $password="") {
		global $database;
		$email = $database->escape_value($email);
		$password = $database->escape_value($password);

		$sql  = "SELECT * FROM ".self::$table_name." ";
		$sql .= "WHERE email = '{$email}' ";
		$sql .= "AND password = '{$password}' ";
		$sql .= "LIMIT 1";
		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false ;
	}
	
	public function forget_password() {
		$to = $this->email;
		$subject = "Password Reset Link SwiftIntern";
		$from = "SwiftIntern <info@swiftintern.com>";

		$message 	=<<<EMAILBODY
Hi {$this->name},<br>
<br>
You have requested for change of password on our site, just click the link to reset your password. If not ignore this email, you can contact me for any problem, it will be good to listen from you.
<br>
To change password : <a href="http://swiftintern.com/changepass/{$this->id}/{$this->access_token}">Link</a><br>
<br>
Thanks and Regards,<br>
Saud Akhtar<br>
+91-9891048495<br>
CMO at SwiftIntern.Com<br>

EMAILBODY;

		$headers = "From: {$from}\n";
		$headers .= "Reply-To: {$from}\n";
		$headers .= "X-Mailer: PHP/".phpversion()."\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	
		$result = mail($to, $subject, $message, $headers);
		return $result;
	}
	
	public function confirm_email() {
		global $enddate;
		$to = $this->email;
		$subject = "Email confirmation on SwiftIntern";
		$from = "SwiftIntern <info@swiftintern.com>";

		$message 	=<<<EMAILBODY
Hi {$this->name},<br>
<br>
Thank you for Registering on SwiftIntern.com, just click the link to Confirm your Account.<br>
For any help or to ask any question you can call me or mesaage me.<br>
<br>
To verify account  : <a href="http://swiftintern.com/verify/{$this->id}/{$this->access_token}">Link</a><br>
<br>
<br>
Thanks and Regards,<br>
Saud Akhtar<br>
+91-9891048495<br>
CMO at SwiftIntern.Com<br>

EMAILBODY;

		$headers = "From: {$from}\n";
		$headers .= "Reply-To: {$from}\n";
		$headers .= "X-Mailer: PHP/".phpversion()."\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
	
		$result = mail($to, $subject, $message, $headers);
		if($result){
			log_action("confirm_mail{$enddate}", 'Mail Sent', "To: {$this->name} Subject: {$subject}");
		}else{
			log_action("confirm_mail{$enddate}", 'Mail NotSent', "To: {$this->name} Subject: {$subject}");
		}
		return $result;
	}
	
	public static function find_all_type($type="") {
		return self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE type = '{$type}' ORDER BY id DESC");
	}

	public static function find_by_access_token($email, $access_token) {
		$result_array = static::find_by_sql("SELECT * FROM ".self::$table_name." WHERE email = '{$email}' AND access_token = '{$access_token}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false ;
	}
	
	public static function find_by_access_id($id, $access_token) {
		$result_array = static::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id = '{$id}' AND access_token = '{$access_token}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false ;
	}
	
	public static function find_by_email($email="") {
		$result_array = static::find_by_sql("SELECT * FROM ".self::$table_name." WHERE email = '{$email}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false ;
	}

	public static function find_name($user_id="") {
		$result_array = static::find_by_sql("SELECT id,name FROM ".self::$table_name." WHERE id = '{$user_id}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false ;
	}
}

?>