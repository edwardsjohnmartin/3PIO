<?php
require_once('controllers/base_controller.php');
class SectionController extends BaseController{
	public function index(){
		$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
		$view_to_show = 'views/shared/index.php';
		require_once('views/shared/layout.php');
	}

	public function create(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){

				//Handle students and teaching assistants if any are set
				if(isset($_POST['is_study_students']) or isset($_POST['is_study_students']) or isset($_POST['is_study_students'])){

					//Check to make sure users aren't in both is_study_students and not_study_students
					$intersection = array_intersect($_POST['is_study_students'], $_POST['not_study_students']);
					if(!isset($intersection) or count($intersection) > 0){
						unset($intersection);
						add_alert("A user can only be placed in one of the students boxes.", Alert_Type::DANGER);
					}else{

						//Check to make sure a user is only being set as a student or a TA, not both
						$ta_intersection = array_intersect($_POST['teaching_assistants'], array_merge($_POST['is_study_students'], $_POST['not_study_students']));
						if(!isset($ta_intersection) or count($ta_intersection) > 0){
							unset($ta_intersection);
							add_alert("A user cannot be a student and teaching assistant for a section.", Alert_Type::DANGER);
						}else{
							$model = new $this->model_name();
							$model->set_properties($_POST);
							$model->set_properties(array('teacher' => $_SESSION['user']->get_id()));

							//HACK: The 'students property of the section model will not get filled here. The data will get passed through the $_POST and be filled in the create() function in the section class.
							if($model->is_valid()){
								$model->create();
								add_alert('Successfully created!', Alert_Type::SUCCESS);
								return redirect('section', 'index');
							}
						}
					}
				} else{
					$model = new $this->model_name();
					$model->set_properties($_POST);
					$model->set_properties(array('teacher' => $_SESSION['user']->get_id()));

					//HACK: The 'students property of the section model will not get filled here. The data will get passed through the $_POST and be filled in the create() function in the section class.
					if($model->is_valid()){
						$model->create();
						add_alert('Successfully created!', Alert_Type::SUCCESS);
						return redirect('section', 'index');
					}
				}
			}
			else{
				add_alert('Please try again.', Alert_Type::DANGER);
			}
		}
		$view_to_show = 'views/shared/create.php';
		$properties = $this->model_name::get_properties_for_create();
		$types = $this->model_name::get_types_for_create();
		require_once('views/shared/layout.php');
	}

	public function read(){
		$model = Section::get($_GET['id']);
		if($model == null){
			add_alert('The section you are trying to access doesn\'t exist.', Alert_Type::DANGER);
			return call('pages', 'error');
		}
		else{
			$view_to_show = 'views/section/read.php';
			if(!file_exists($view_to_show)){
				$view_to_show = 'views/shared/read.php';
			}
			$types = $model::get_types_for_create();
			$properties = $model->get_properties_for_update();
			require_once('views/shared/layout.php');
		}
	}

	public function update(){
		//If a section_id wasn't passed in or if the user isn't the owner of the section, throw an error
		if (!isset($_GET['id']) || !section::is_owner($_GET['id'], $_SESSION['user']->get_id())){
			return call('pages', 'error');
		}

		//Get the section associated to the section_id passed through $_GET
		$section = Section::get($_GET['id']);
		if($section == null){
			add_alert("The item you are trying to access doesn't exist.", Alert_Type::DANGER);
			return call('pages', 'error');
		}
		else{
			$section_id = $_GET['id'];
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){

				//Initialize any $_POST arrays that didn't have any entries
				if(!isset($_POST['is_study_students'])){
					$_POST['is_study_students'] = array();}
				if(!isset($_POST['not_study_students'])){
					$_POST['not_study_students'] = array();}
				if(!isset($_POST['teaching_assistants'])){
					$_POST['teaching_assistants'] = array();}

				//Check to make sure users aren't in both is_study_students and not_study_students
				$intersection = array_intersect($_POST['is_study_students'], $_POST['not_study_students']);
				if(!isset($intersection) or count($intersection) > 0){
					unset($intersection);
					add_alert("A user can only be placed in one of the students boxes.", Alert_Type::DANGER);
				}
				else{
					//Check to make sure a user is only being set as a student or a TA, not both
					$ta_intersection = array_intersect($_POST['teaching_assistants'], array_merge($_POST['is_study_students'], $_POST['not_study_students']));
					if(!isset($ta_intersection) or count($ta_intersection) > 0){
						unset($ta_intersection);
						add_alert("A user cannot be a student and teaching assistant for a section.", Alert_Type::DANGER);
					}else{
						$section = new Section();
						$section->set_id($section_id);
						$section->set_properties($_POST);
						$section->set_properties(array('teacher' => $_SESSION['user']->get_id()));

						//HACK: The 'students property of the section model will not get filled here. The data will get passed through the $_POST and be filled in the create() function in the section class.
						if($section->is_valid()){
							$section->update();
							add_alert('Successfully updated!', Alert_Type::SUCCESS);
							return redirect('section', 'index');
						}
						else{
							add_alert('Please try again.', Alert_Type::DANGER);
						}
					}
				}
			}
		}

		$view_to_show = 'views/shared/update.php';
		$properties = $section->get_properties_for_update();
		$types = $this->model_name::get_types_for_create();

		unset($properties['teacher']);
		unset($types['teacher']);
		unset($properties['concepts']);
		unset($types['concepts']);

		require_once('views/shared/layout.php');
	}

	public function read_student(){
		if (!isset($_GET['id'])){
			return call('pages', 'error');}
		else if (!section::is_student($_GET['id'], $_SESSION['user']->get_id())){
			add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
			return call('pages', 'error');}
		else{
			$section = ($this->model_name)::get($_GET['id']);
			if ($section == null){
				return call('pages', 'error');}
			else{
				require_once('models/concept.php');
				require_once('models/exam.php');
				$concepts = concept::get_all_for_section_and_student($_GET['id'], $_SESSION['user']->get_id());
				$exams = exam::get_all_for_section_and_student($_GET['id'], $_SESSION['user']->get_id());

				$view_to_show = 'views/section/read_student_v3.php';
				require_once('views/shared/layout.php');
			}
		}
	}
}
?>
