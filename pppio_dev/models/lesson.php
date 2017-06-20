<?php
	require_once('models/model.php');
	class Lesson extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'owner' => Type::USER); //use the enum
		protected $name;
		protected $description;
		protected $user;
	}
?>
