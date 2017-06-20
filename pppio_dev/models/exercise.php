<?php
	require_once('models/model.php');
	class Exercise extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'test_code' => Type::CODE, 'language' => Type::LANGUAGE); //use the enum
		protected $name;
		protected $description;
		protected $user;
		protected $test_code;
		protected $language;
	}
?>
