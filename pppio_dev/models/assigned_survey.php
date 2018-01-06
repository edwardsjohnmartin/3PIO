<?php
require_once('models/model.php');
class Assigned_Survey extends Model{
	public $assigned_survey_id;
	public $survey;
	public $concept;
	public $survey_type;

	public static function get($assigned_survey_id){
		$db = Db::getReader();

		$assigned_survey_id = intval($assigned_survey_id);
		$function_name = 'sproc_read_survey_get_assigned_survey';
		$req = $db->prepare(static::build_query($function_name, array('assigned_survey_id')));
		$req->execute(array('assigned_survey_id' => $assigned_survey_id));

		$req->setFetchMode(PDO::FETCH_CLASS, 'Assigned_Survey');
		$ret = $req->fetch(PDO::FETCH_CLASS);

		if($ret){
			$ret->survey = json_decode($ret->survey);
			$ret->concept = json_decode($ret->concept);
			$ret->survey_type = json_decode($ret->survey_type);

			return $ret;
		}
	}
}
