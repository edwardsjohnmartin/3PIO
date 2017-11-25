<?php
require_once('models/model.php');
class Survey extends Model
{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'instructions' => Type::STRING, 'survey_type' => Type::SURVEY_TYPE, 'concept' => Type::CONCEPT, 'lesson' => Type::LESSON);
	protected $name;
	protected $instructions;
	protected $survey_type;
	protected $concept;
	protected $lesson;
}
?>
