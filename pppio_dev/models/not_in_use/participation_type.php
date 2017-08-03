<?php
	require_once('models/model.php');
	class Participation_Type extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING); //use the enum
		protected $name;
	}
?>
