<?php
	require_once('models/model.php');
	class Role extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING);
		protected $name;
	}
?>
