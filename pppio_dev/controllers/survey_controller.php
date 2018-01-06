<?php
require_once('controllers/base_controller.php');
class SurveyController extends BaseController{
	public function create(){
		require_once('models/survey_question.php');

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){
				if($_POST['name'] === ""){
					add_alert('The survey must have a name.', Alert_Type::DANGER);
				}else if(!isset($_POST['survey_questions'])){
					add_alert('The survey must include at least one question.', Alert_Type::DANGER);
				}else{
					$model = new $this->model_name();
					$model->set_properties($_POST);
					$model->create();
					add_alert('Successfully created!', Alert_Type::SUCCESS);
					return redirect($this->model_name, 'index');
				}
			}
			else{ add_alert('Please try again.', Alert_Type::DANGER);}
		}
		$survey_questions = Survey_Question::get_pairs();

		$view_to_show = 'views/survey/create.php';
		require_once('views/shared/layout.php');
	}

	//This will be used to assign existing surveys to concepts.
	public function assign(){
		require_once('models/survey.php');
		require_once('models/survey_type.php');
		require_once('models/concept.php');

		$assigned_surveys = Survey::get_all_assigned();
		$concepts = Concept::get_by_section($_SESSION['user']->get_id());
		$surveys = Survey::get_pairs();
		$survey_types = Survey_Type::get_pairs();

		$view_to_show = 'views/survey/assign.php';
		require_once('views/shared/layout.php');
	}

	//This will be used by the ajax call to save the assigned survey to the database
	public function assign_survey(){
		require_once('models/survey_type.php');
		require_once('models/survey.php');

		$success = true;

		if ($_POST['survey_id'] != "" and $_POST['concept_id'] != "" and $_POST['survey_type_id'] != ""){
			$survey_id = intval($_POST['survey_id']);
			$concept_id = intval($_POST['concept_id']);
			$survey_type_id = intval($_POST['survey_type_id']);
		}
		else{
			$message = 'Not all the required information was set.';
			$success = false;
		}

		if($success){
			$ret = Survey::assign_survey($survey_id, $concept_id, $survey_type_id);
			if(is_null($ret)){
				$message = 'A survey of that type already exists for that concept.';
				$success = false;
			} else {
				$message = 'A survey was successfully assigned.';
			}
		}

		$json_data = array('message' => $message, 'success' => $success);
		require_once('views/shared/json_wrapper.php');
	}

	public function get_assigned_surveys(){
		require_once('models/survey.php');

		$json_data = Survey::get_assigned($_POST['survey_id']);
		require_once('views/shared/json_wrapper.php');
	}

	public function do_survey(){
		require_once('models/survey.php');
		require_once('models/survey_question.php');
		require_once('models/survey_question_type.php');

		if(!isset($_GET['survey_id'])){
			add_alert('The survey to take was not set.', Alert_Type::DANGER);
			return call('pages', 'error');
		}

		//Make sure the survey exists and has questions
		$survey_questions = Survey::get_to_take($_GET['survey_id']);
		if(count($survey_questions) == 0){
			add_alert('The survey you are trying to access does not exist.', Alert_Type::DANGER);
			return call('pages', 'error');
		}

		//Check if the user has already done the survey
		if(Survey::check_is_complete($_GET['survey_id'])){
		    add_alert('You have already completed this survey.', Alert_Type::DANGER);
		    return call('pages', 'error');
		}

		foreach($survey_questions as $key => $value){
			if($value->get_survey_question_type() == Question_Type_Enum::MULTIPLE_CHOICE){
				$survey_questions[$key]->set_choices_from_db();
			} else if($value->get_survey_question_type() == Question_Type_Enum::RANGE){
				$survey_questions[$key]->set_ranges_from_db();
			}
		}

		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			if(count($_POST) < count($survey_questions)){
				add_alert('Please answer every question.', Alert_Type::DANGER);
			} else {
				//m = Multiple_Choice, r = Range, s = Short_Answer
				foreach($_POST as $q_id => $ans){
					if(substr($q_id, 0, 1) == 'm'){
						Survey::save_survey_answer(intval(substr($q_id, 2)), intval($ans), Question_Type_Enum::MULTIPLE_CHOICE);
					} else if(substr($q_id, 0, 1) == 'r'){
						Survey::save_survey_answer(intval(substr($q_id, 2)), intval($ans), Question_Type_Enum::RANGE);
					} else if(substr($q_id, 0, 1) == 's'){
						Survey::save_survey_answer(intval(substr($q_id, 2)), $ans, Question_Type_Enum::SHORT_ANSWER);
					}
				}

				add_alert('Your answers to the survey have been saved.', Alert_Type::SUCCESS);
				$go_home = true;
			}
		}

		if(isset($go_home) and $go_home){
			$view_to_show = 'views/pages/home.php';
		} else {
			$view_to_show = 'views/survey/take.php';
		}
		require_once('views/shared/layout.php');
	}

	public function read_responses(){
		require_once('models/survey.php');

		$surveys = Survey::get_pairs();

		$view_to_show = 'views/survey/read_responses.php';
		require_once('views/shared/layout.php');
	}
}
?>
