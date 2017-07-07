<?php
	require_once('models/model.php');
	class Section extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'course' => Type::COURSE, 'teacher' => Type::USER, 'start_date' => Type::DATETIME, 'end_date' => Type::DATETIME); //use the enum
		protected $name;
		protected $course;
		protected $teacher;
		protected $start_date;
		protected $end_date;
	}
?>
