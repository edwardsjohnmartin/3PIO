<?php
require_once('models/model.php');
class Survey_Question extends Model
{
	protected static $types = array(
		'id' => Type::INTEGER, 
		'instructions' => Type::STRING, 
		'survey_choices' => Type::LIST_SURVEY_CHOICE);
	protected $instructions;
	protected $survey_choices;
}