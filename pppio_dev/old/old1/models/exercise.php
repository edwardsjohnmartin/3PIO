<?php
	require_once('models/model.php');
	class Exercise extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'starter_code' => Type::CODE, 'test_code' => Type::CODE, 'language' => Type::LANGUAGE, 'tags' => TYPE::LIST_TAG); //use the enum
		protected $name;
		protected $description;
		protected $starter_code;
		protected $test_code;
		protected $language;
		protected $tags;
	}
?>
