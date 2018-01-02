<?php
require_once('controllers/base_controller.php');
class SurveyController extends BaseController{
	public function create(){
		require_once('models/concept.php');
		require_once('models/survey_type.php');

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){
				if($_POST['name'] === ""){
					add_alert('The survey must have a name.', Alert_Type::DANGER);
				}else if(!isset($_POST['survey_questions']) or !isset($_POST['concept']) or !isset($_POST['survey_type'])){
					add_alert('The survey must include a concept, survey type, and at least one question.', Alert_Type::DANGER);
				}else{
					$model = new $this->model_name();
					$model->set_properties($_POST);
					if($model->is_valid()){
						$model->create();
						add_alert('Successfully created!', Alert_Type::SUCCESS);
						return redirect($this->model_name, 'index');
					}
					else{ add_alert('Please try again.', Alert_Type::DANGER);}
				}
			}
			else{ add_alert('Please try again.', Alert_Type::DANGER);}
		}
		$concepts = Concept::get_by_section($_SESSION['user']->get_id());
		$survey_types = Survey_Type::get_pairs();

		$view_to_show = 'views/survey/create.php';
		require_once('views/shared/layout.php');
	}
}
?>
