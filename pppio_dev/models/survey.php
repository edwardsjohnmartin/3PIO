<?php
require_once('models/model.php');
class Survey extends Model{
	protected static $types = array(
		'id' => Type::INTEGER,
		'name' => Type::STRING,
		'concept' => Type::CONCEPT,
		'survey_type' => Type::SURVEY_TYPE,
		'lesson' => Type::LESSON,
		'survey_questions' => Type::LIST_SURVEY_QUESTION
	);

	protected $name;
	protected $concept;
	protected $survey_type;
	protected $lesson;
	protected $survey_questions;

	public static function get_surveys_for_project($concept_id){
		$db = Db::getReader();
		$concept_id = intval($concept_id);

		$function_name = 'sproc_read_survey_get_for_project';
		$req = $db->prepare(static::build_query($function_name, array('concept_id')));
		$req->execute(array('concept_id' => $concept_id));

		$ret = $req->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
		return $ret;
	}
}
?>
