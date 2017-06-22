<?php
	require_once('models/model.php');
	require_once('connection.php');
	require_once('type.php');
	
	class Will extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'dubs' => Type::DOUBLE, 'boo' => Type::BOOLEAN, 'dt' => Type::DATETIME, 'name' => Type::STRING, 'code' => Type::CODE, 'mail' => Type::EMAIL, 'pass' => Type::PASSWORD, 'role' => Type::ROLE); //use the enum
		protected $dubs;
		protected $boo;
		protected $dt;
		protected $code;
		protected $mail;
		protected $pass;
		protected $name;
		protected $role;
	}
	$date = new DateTime();
	$result = $date->format('Y-m-d H:i:s');
	$will_array = array('name' => "Will", 'dubs' => 4.1, 'boo' => true, 'dt' => $date, 'name' => "Will", 'code' => 'this is some code', 'mail' => 'email', 'pass' => 'apple1', 'role' => 1);
	$will = new Will($will_array);
	$will->set_id(5);
	$will->set_properties($will_array);
	$will_props = $will->get_properties();
	
	$will_bool = (string)$will->is_valid();
	echo $will_bool;
?>