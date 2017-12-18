<?php
require_once('controllers/base_controller.php');
class SectionController extends BaseController
{
	public function index()
	{
		$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
		$view_to_show = 'views/shared/index.php';
		require_once('views/shared/layout.php');
	}

	public function create()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){

				//Check to make sure users aren't in both is_study_students and not_study_students
				$intersection = array_intersect($_POST['is_study_students'], $_POST['not_study_students']);
				if(!isset($intersection) or count($intersection) > 0){
					unset($intersection);
					add_alert("A user can only be placed in one of the Students boxes.", Alert_Type::DANGER);
				}else{

					//Check to make sure a user is only being set as a student or a TA, not both
					$ta_intersection = array_intersect($_POST['teaching_assistants'], array_merge($_POST['is_study_students'], $_POST['not_study_students']));
					if(!isset($ta_intersection) or count($ta_intersection) > 0){
						unset($ta_intersection);
						add_alert("A user cannot be a Student and Teaching Assistant for a section.", Alert_Type::DANGER);
					}else{
						$model = new $this->model_name();
						$model->set_properties($_POST);
						$model->set_properties(array('teacher' => $_SESSION['user']->get_id()));
						if($model->is_valid()){
							$model->create();
							add_alert('Successfully created!', Alert_Type::SUCCESS);
							return redirect('section', 'index');
						}
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

	public function update() {
		//check if this lesson belongs to me
		//check the owner!!!!!!!

		//must set id and the rest too. id is separate.
		//for users especially, i need to be more careful.
		//this is a basic one without permissions.

		if (!isset($_GET['id']) || !section::is_owner($_GET['id'], $_SESSION['user']->get_id()))
		{
			return call('pages', 'error');
		}

		//if there is post data...
		//todo: i need to check if the model actually exists on post, too!!!!
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){
				//probably i should do that isset stuff
				$model = new $this->model_name();
				$model->set_id($_GET['id']); //i should not trust that...
				$model->set_properties($_POST);
				$model->set_properties(array('teacher' => $_SESSION['user']->get_id()));
				if($model->is_valid())
				{
					$model->update();
					add_alert('Successfully updated!', Alert_Type::SUCCESS);
					return redirect('section', 'index');
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}
			else
			{
				add_alert('Please try again.', Alert_Type::DANGER);
			}
		}

		$model = ($this->model_name)::get($_GET['id']);
		if($model == null)
		{
			return call('pages', 'error');
		}
		else
		{
			$view_to_show = 'views/shared/update.php';
			$properties = $model->get_properties();
			$types = $model::get_types();
			unset($properties['teacher']);
			unset($types['teacher']);
			unset($properties['concepts']);
			unset($types['concepts']);
			require_once('views/shared/layout.php');
		}
		//i need to be better about the order of things.

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
