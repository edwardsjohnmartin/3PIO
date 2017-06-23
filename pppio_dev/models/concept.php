<?php
	require_once('models/model.php');
	class Concept extends Model
	{
		protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'section' => Type::SECTION, 'project' => Type::PROJECT, 'project_open_date' => Type::DATETIME, 'project_due_date' => Type::DATETIME, 'lessons' => Type::LIST_LESSON); //use the enum
		protected $name;
		protected $section;
		protected $project;
		protected $project_open_date;
		protected $project_due_date;
		protected $lessons;
	}
?>
