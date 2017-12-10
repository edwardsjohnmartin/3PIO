<?php
//TODO: Rename props to include the '_id' after them so they match whats in the db
require_once('models/model.php');
class Survey extends Model
{
	protected static $types = array('id' => Type::INTEGER, 'name' => Type::STRING, 'instructions' => Type::STRING, 'survey_type' => Type::SURVEY_TYPE, 'concept' => Type::CONCEPT, 'lesson' => Type::LESSON);
	protected $name;
	protected $instructions;
	protected $survey_type;
	protected $concept;
	protected $lesson;

	//Create the survey in the database
	public function create(){
		$props = $this->get_db_properties();

		//if no lesson was selected, make it null. assuming this is being error checked in the controller
		if($props['lesson'] == "0"){
			$props['lesson'] = null;
		}

		$req = Db::getWriter()->prepare(static::build_query(
			'sproc_write_survey_create',
			array('name', 'instructions', 'survey_type_id', 'concept_id', 'lesson_id')
		));

		$req->execute(array('name' => $props['name'],'instructions' => $props['instructions'],'survey_type_id' => $props['survey_type'],'concept_id' => $props['concept'],'lesson_id' => $props['lesson']));

		//get the id of the survey that was created
		$survey_id = $req->fetchColumn();

		//pass the questions for the survey to create_questions along with the survey_id
		$this->create_questions($survey_id, $_POST['questions']);
	}

	//Create all the questions for a survey in the database
	public function create_questions($survey_id, $questions){
		foreach($questions as $q_key => $q_value){
			$req = Db::getWriter()->prepare(static::build_query(
				'sproc_write_survey_question_create',
				array('instructions', 'survey_id')
			));

			$req->execute(array('instructions' => $q_value, 'survey_id' =>$survey_id));

			//get the id of the survey question that was created
			$survey_question_id = $req->fetchColumn();

			//pass the choices for the question to create_choices along with the survey_question_id
			$this->create_choices($survey_question_id, $_POST['choices'][$q_key]);
		}
	}

	//Create all the choices for a question in the database
	public function create_choices($survey_question_id, $choices){
		foreach($choices as $c_key => $c_value){
			$req = Db::getWriter()->prepare(static::build_query(
				'sproc_write_survey_choice_create',
				array('choice', 'survey_question_id')
			));

			$req->execute(array('choice' => $c_value, 'survey_question_id' =>$survey_question_id));
		}
	}

	//Gets an array of all the questions on a survey as key value pairs
	public function get_questions($survey_id){
		$req = Db::getReader()->prepare(static::build_query(
				'sproc_read_survey_questions_for_survey',
				array('survey_id')
			));

		$req->execute(array('survey_id' => intval($survey_id)));
		return $req->fetchAll(PDO::FETCH_KEY_PAIR);
	}

	//Gets an array of all the choices on a survey grouped by the survey_question_id they are for
	public function get_choices($survey_id){
		$req = Db::getReader()->prepare(static::build_query(
				'sproc_read_survey_choices_for_survey',
				array('survey_id')
			));

		$req->execute(array('survey_id' => intval($survey_id)));
		$ret = $req->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);

		if(!empty($ret))
		{
			foreach($ret as $ret_key => $ret_value)
			{
				//Sometimes the choices come back as \"[choice]\" with the '\"' being part of the string
				//To fix this, all '\' and '"' are removed from the choice

				$c_id_arr = trim($ret_value['choice_id'], "{}");
				$c_id_arr = str_replace(array('\\', '"'), "", $c_id_arr);
				$c_id_arr = explode(",",$c_id_arr);

				$c_arr = trim($ret_value['choices'], "{}");
				$c_arr = str_replace(array('\\', '"'), "", $c_arr);
				$c_arr = explode(",",$c_arr);

				$new_arr = array_combine($c_id_arr, $c_arr);
				$ret[$ret_key] = $new_arr;
			}
		}
		return $ret;
	}
}
?>
