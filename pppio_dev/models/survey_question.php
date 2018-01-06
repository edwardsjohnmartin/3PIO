<?php
require_once('models/model.php');
class Survey_Question extends Model{
	protected static $types = array(
		'id' => Type::INTEGER,
		'prompt' => Type::STRING,
		'survey_question_type' => Type::SURVEY_QUESTION_TYPE,
		'survey_choices' => Type::LIST_SURVEY_CHOICE,
		'min' => Type::INTEGER,
		'max' => Type::INTEGER);
	protected $prompt;
	protected $survey_question_type;
	protected $survey_choices;
	protected $min;
	protected $max;

	public function create(){
		$db = Db::getWriter();

		$survey_choices = parent::php_array_to_pg_array($this->survey_choices);

		$function_name = 'sproc_write_survey_question_create';
		$req = $db->prepare(static::build_query($function_name, array('prompt', 'survey_question_type', 'survey_choices', 'min', 'max')));
		$req->execute(array('prompt' => $_POST['prompt'], 'survey_question_type' => intval($this->survey_question_type), 'survey_choices' => $survey_choices, 'min' => intval($this->min), 'max' => intval($this->max)));
		$ret = $req->fetch(PDO::FETCH_COLUMN);
		return $ret;
	}

	public function get_survey_question_type(){
		return $this->survey_question_type;
	}

	public function set_choices_from_db(){
		$db = Db::getReader();

		$question_id = intval($this->id);

	    $function_name = 'sproc_read_survey_question_get_choices';
	    $req = $db->prepare(static::build_query($function_name, array('survey_question_id')));
	    $req->execute(array('survey_question_id' => $question_id));

	    $ret = $req->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
	    $this->survey_choices = $ret;
	}

	public function set_ranges_from_db(){
		$db = Db::getReader();

		$question_id = intval($this->id);

	    $function_name = 'sproc_read_survey_question_get_ranges';
	    $req = $db->prepare(static::build_query($function_name, array('survey_question_id')));
	    $req->execute(array('survey_question_id' => $question_id));

	    $ret = $req->fetch(PDO::FETCH_ASSOC);
	    $this->min = $ret['min'];
	    $this->max = $ret['max'];
	}
}
?>
