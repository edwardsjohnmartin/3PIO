<?php
	require_once('models/model.php');
	class Function extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'section' => Type::SECTION, 'user' => Type::USER, 'code' => Type::CODE); //use the enum
		protected $name;
		protected $section;
		protected $user;
		protected $code;
	}
?>
