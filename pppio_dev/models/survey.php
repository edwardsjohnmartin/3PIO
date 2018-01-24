<?php
require_once('models/model.php');
class Survey extends Model{
	protected static $types = array(
		'id' => Type::INTEGER,
		'name' => Type::STRING,
		'survey_questions' => Type::LIST_SURVEY_QUESTION
	);

	protected $name;
	protected $survey_questions;

	//Will return false if survey doesn't exist.
	//Will return assigned_survey_id=[key] and date_completed=[null] if survey exists but user hasn't completed it
	//Will return assigned_survey_id=[key] and date_completed=[date] if survey exists and user has completed it
	public static function check_for_project($survey_type_id, $concept_id){
	    $db = Db::getReader();
		$survey_type_id = intval($survey_type_id);
	    $concept_id = intval($concept_id);
		$user_id = intval($_SESSION['user']->get_id());

	    $function_name = 'sproc_read_survey_check_for_project';
	    $req = $db->prepare(static::build_query($function_name, array('concept_id', 'survey_type_id', 'user_id')));
	    $req->execute(array('concept_id' => $concept_id, 'survey_type_id' => $survey_type_id, 'user_id' => $user_id));

	    $ret = $req->fetch(PDO::FETCH_ASSOC);
	    return $ret;
	}

	public static function check_is_complete($survey_id){
	    $db = Db::getReader();
		$survey_id = intval($survey_id);
		$user_id = intval($_SESSION['user']->get_id());

	    $function_name = 'sproc_read_survey_check_is_complete';
	    $req = $db->prepare(static::build_query($function_name, array('user_id', 'survey_id')));
	    $req->execute(array('user_id' => $user_id, 'survey_id' => $survey_id));

	    $ret = $req->fetch(PDO::FETCH_ASSOC);
	    return $ret;
	}

	public static function get_all_assigned(){
	    $db = Db::getReader();

	    $function_name = 'sproc_read_survey_get_all_assigned';
	    $req = $db->prepare(static::build_query($function_name, array()));
	    $req->execute(array());

	    $ret = $req->fetchAll(PDO::FETCH_ASSOC);
		foreach($ret as $key => $val){
			$ret[$key]['section'] = json_decode($val['section']);
			$ret[$key]['concept'] = json_decode($val['concept']);
			$ret[$key]['project'] = json_decode($val['project']);
			$ret[$key]['survey'] = json_decode($val['survey']);
			$ret[$key]['survey_type'] = json_decode($val['survey_type']);
		}
	    return $ret;
	}

	public static function get_all_unassigned(){
	    $db = Db::getReader();

	    $function_name = 'sproc_read_survey_get_all_unassigned';
	    $req = $db->prepare(static::build_query($function_name, array()));
	    $req->execute(array());

	    $ret = $req->fetchAll(PDO::FETCH_ASSOC);
		foreach($ret as $key => $val){
			$ret[$key]['section'] = json_decode($val['section']);
			$ret[$key]['concept'] = json_decode($val['concept']);
			$ret[$key]['project'] = json_decode($val['project']);
			$ret[$key]['survey'] = json_decode($val['survey']);
			$ret[$key]['survey_type'] = json_decode($val['survey_type']);
		}
	    return $ret;
	}

	public static function get_assigned($survey_id){
	    $db = Db::getReader();

		$survey_id = intval($survey_id);

	    $function_name = 'sproc_read_survey_get_assigned';
	    $req = $db->prepare(static::build_query($function_name, array('survey_id')));
	    $req->execute(array('survey_id' => $survey_id));

	    $ret = $req->fetchAll(\PDO::FETCH_ASSOC|\PDO::FETCH_UNIQUE);
	    return $ret;
	}

	public static function assign_survey($survey_id, $concept_id, $survey_type_id){
		$db = Db::getWriter();

		$function_name = 'sproc_write_survey_assign';
		$req = $db->prepare(static::build_query($function_name, array('survey', 'concept', 'survey_type', 'date_assigned')));
		$req->execute(array('survey' => $survey_id, 'concept' => $concept_id, 'survey_type' => $survey_type_id, 'date_assigned' => date("Y-m-d H:i:s")));
		$ret = $req->fetch(PDO::FETCH_COLUMN);
		return $ret;
	}

	public static function unassign_survey($assigned_survey_id){
		$db = Db::getWriter();

		$function_name = 'sproc_write_survey_unassign';
		$req = $db->prepare(static::build_query($function_name, array('assigned_survey_id', 'date_unassigned')));
		$req->execute(array('assigned_survey_id' => $assigned_survey_id, 'date_unassigned' => date("Y-m-d H:i:s")));
		$ret = $req->fetch(PDO::FETCH_COLUMN);
		return $ret;
	}

	public static function reassign_survey($assigned_survey_id){
		$db = Db::getWriter();

		$function_name = 'sproc_write_survey_reassign';
		$req = $db->prepare(static::build_query($function_name, array('assigned_survey_id')));
		$req->execute(array('assigned_survey_id' => $assigned_survey_id));
		$ret = $req->fetch(PDO::FETCH_COLUMN);
		return $ret;
	}

	//Returns all questions on a particular assigned_survey 
	public static function get_to_take($assigned_survey_id){
		$db = Db::getWriter();

		$assigned_survey_id = intval($assigned_survey_id);
		$function_name = 'sproc_read_survey_get_to_take';
		$req = $db->prepare(static::build_query($function_name, array('assigned_survey_id')));
		$req->execute(array('assigned_survey_id' => $assigned_survey_id));

		$ret = $req->fetchAll(PDO::FETCH_CLASS, 'Survey_Question');
		return $ret;
	}

	//Saves a users answer to a single question to the database
	public static function save_survey_answer($survey_question_id, $answer, $survey_question_type_id){
		$db = Db::getWriter();

		if($survey_question_type_id == Question_Type_Enum::MULTIPLE_CHOICE){
			$function_name = 'sproc_write_survey_save_mult_response';
		} else if($survey_question_type_id == Question_Type_Enum::RANGE){
			$function_name = 'sproc_write_survey_save_range_response';
		} else if($survey_question_type_id == Question_Type_Enum::SHORT_ANSWER){
			$function_name = 'sproc_write_survey_save_short_response';
		}

		$answers['user_id'] = intval($_SESSION['user']->get_id());
		$answers['assigned_survey_id'] = intval($_GET['survey_id']);
		$answers['date_responded'] = date('Y-m-d H:i:s');
		$answers['survey_question_id'] = $survey_question_id;
		$answers['answer'] = $answer;

		$req = $db->prepare(static::build_query($function_name, array_keys($answers)));
		$ret = $req->execute($answers);
		return $ret;
	}
}
?>
