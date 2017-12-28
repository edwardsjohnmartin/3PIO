<?php
require_once('models/model.php');
class Survey_Question extends Model{
	protected static $types = array(
		'id' => Type::INTEGER, 
		'instructions' => Type::STRING, 
		'survey_question_type' => Type::SURVEY_QUESTION_TYPE,
		'survey_choices' => Type::LIST_SURVEY_CHOICE);
	protected $instructions;
	protected $survey_question_type;
	protected $survey_choices;
}
