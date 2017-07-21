<?php
	require_once('models/model.php');
	class Project extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'description' => Type::STRING, 'starter_code'=> Type::CODE, 'language' => Type::LANGUAGE);
		protected $name;
		protected $description;
		protected $starter_code;
		protected $language;
	}
?>
