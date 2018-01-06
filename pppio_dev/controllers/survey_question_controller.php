<?php
require_once('controllers/base_controller.php');
class Survey_QuestionController extends BaseController{
	public function create(){
		require_once('models/survey_choice.php');
		require_once('models/survey_question_type.php');

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){
				$failed = false;
				if($_POST['prompt'] === ''){
					$failed = true;
					$alert = 'The question must have a prompt.';
				}

				if(!isset($_POST['survey_question_type'])){
					$failed = true;
					$alert = 'Please select a survey question type';
				} else{
					if($_POST['survey_question_type'] == Question_Type_Enum::MULTIPLE_CHOICE){
						if(!isset($_POST['survey_choices'])){
							$failed = true;
							$alert = 'Please select at least 2 survey choices.';
						} else if(count($_POST['survey_choices']) <= 1){
							$failed = true;
							$alert = 'A multiple choice question needs to have at least 2 choices.';
						}

						unset($_POST['min']);
						unset($_POST['max']);
					} else if($_POST['survey_question_type'] == Question_Type_Enum::RANGE){
						if($_POST['min'] == '' or $_POST['max'] == ''){
							$failed = true;
							$alert = 'Please enter a min and max range.';
						} else {
							$min = intval($_POST['min']);
							$max = intval($_POST['max']);

							if($min > $max){
								$failed = true;
								$alert = 'The min range must be less than the max range.';
							}

							if($min === $max){
								$failed = true;
								$alert = 'The min range cannot be the same as the max range.';
							}
						}

						unset($min);
						unset($max);
						unset($_POST['survey_choices']);
					} else if($_POST['survey_question_type'] == Question_Type_Enum::SHORT_ANSWER){
						unset($_POST['min']);
						unset($_POST['max']);
						unset($_POST['survey_choices']);
					}
				}

				if(!$failed){
					$model = new Survey_Question();
					$model->set_properties($_POST);
					$model->create();
					add_alert('Successfully created!', Alert_Type::SUCCESS);
					return redirect($this->model_name, 'index');
				} else {
					add_alert($alert, Alert_Type::DANGER);
				}
			} else {
				add_alert('Please try again.', Alert_Type::DANGER);
			}
		}

		$survey_choices = Survey_Choice::get_pairs();
		$survey_question_types = Survey_Question_Type::get_pairs();

		$view_to_show = 'views/survey_question/create.php';
		require_once('views/shared/layout.php');
	}
}
?>
