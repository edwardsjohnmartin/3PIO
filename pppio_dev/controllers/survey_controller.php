<?php
require_once('controllers/base_controller.php');
class SurveyController extends BaseController
{
	//List all surveys in a table
	//public function index(){
	//    $models = ($this->model_name)::get_pairs();
	//    $view_to_show = 'views/shared/index.php';
	//    require_once('views/shared/layout.php');
	//}

	//Create a survey and save it to the database
	//public function create(){
	//    ////TODO: Make a custom create view for this action
	//    ////It will need to be able to set name, instructions, survey type, concept, and lesson
	//    ////The lesson will only be set if the survey type is Pre- or Post-Lesson
	//    ////It will need:
	//    //// add question button
	//    //// add choice button
	//    //// delete question button
	//    //// delete choice button
	//    //// way to reorder questions
	//    //// way to reorder choices
	//    ////To be able to save:
	//    //// survey needs at least 1 question
	//    //// questions needs at least 2 choices
	//    //// lesson must be null unless survey type is Pre- or Post-Lesson
	//    //// if lesson is set, it must be in the concept
	//    ////Initial Checks:
	//    //// A concept has to exist

	//    //require_once('enums/survey_type_enum.php');

	//    //if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	//    //    $postedToken = filter_input(INPUT_POST, 'token');
	//    //    if(!empty($postedToken) && isTokenValid($postedToken)){
	//    //        if($_POST['survey_type'] == survey_type_enum::PRE_LESSON or $_POST['survey_type'] == survey_type_enum::POST_LESSON){
	//    //            if(!isset($_POST['lesson']) or $_POST['lesson'] == "0"){
	//    //                add_alert('Lesson must be set for Pre Lesson and Post Lesson survey types.', Alert_Type::DANGER);
	//    //                //return redirect($this->model_name, 'create');
	//    //            }
	//    //            else{
	//    //                $model = new $this->model_name();
	//    //                $model->set_properties($_POST);
	//    //                if($model->is_valid())
	//    //                {
	//    //                    $model->create();
	//    //                    add_alert('Successfully created!', Alert_Type::SUCCESS);
	//    //                    return redirect($this->model_name, 'index');
	//    //                }
	//    //                else
	//    //                {
	//    //                    add_alert('Survey could not be created.', Alert_Type::DANGER);
	//    //                }
	//    //            }
	//    //        }
	//    //        else{
	//    //            $_POST['lesson'] = "0";

	//    //            $model = new $this->model_name();
	//    //            $model->set_properties($_POST);
	//    //            if($model->is_valid())
	//    //            {
	//    //                $model->create();
	//    //                add_alert('Successfully created!', Alert_Type::SUCCESS);
	//    //                return redirect($this->model_name, 'index');
	//    //            }
	//    //            else
	//    //            {
	//    //                add_alert('Survey could not be created.', Alert_Type::DANGER);
	//    //            }
	//    //        }
	//    //    }
	//    //    else{
	//    //        add_alert('Survey could not be created.', Alert_Type::DANGER);
	//    //    }
	//    //}
	//    //$view_to_show = 'views/survey/create.php';
	//    //require_once('views/shared/layout.php');
	//}

	//Preview the survey
	//public function read(){
		//if (!isset($_GET['id']))
		//{
		//    return call('pages', 'error');
		//}
		//else
		//{
		//    $model = $this->model_name::get($_GET['id']);
		//    if($model == null)
		//    {
		//        add_alert('The ' . $this->model_name . ' you are trying to access doesn\'t exist.', Alert_Type::DANGER);
		//        return call('pages', 'error');
		//    }
		//    else
		//    {
		//        $questions = $model::get_questions($_GET['id']);
		//        $choices = $model::get_choices($_GET['id']);

		//        $view_to_show = 'views/' . strtolower($this->model_name) . '/read.php';
		//        if(!file_exists($view_to_show))
		//        {
		//            $view_to_show = 'views/shared/read.php';
		//        }

		//        require_once('models/survey_type.php');
		//        require_once('models/concept.php');
		//        require_once('models/lesson.php');
		//        require_once('views/shared/layout.php');
		//    }
		//}
	//}
}
?>
