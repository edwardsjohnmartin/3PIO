<?php
	require_once('models/model.php');
	class Problem extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'starter_code'=> Type::CODE, 'test_code' =>  Type::CODE, 'language' => Type::LANGUAGE);
		protected $name;
		protected $description;
		protected $starter_code;
		protected $test_code;
		protected $language; //id or key/value
	}
?>
