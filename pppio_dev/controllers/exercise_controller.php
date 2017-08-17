<?php
	require_once('controllers/base_controller.php');
	class ExerciseController extends BaseController
	{
		public function index()
		{

			$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/shared/index.php';
			require_once('views/shared/layout.php');
		}

		public function create()
		{
			require_once('models/lesson.php');
			$lessons = lesson::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('lesson' => $lessons);
			if(count($lessons) > 0)
			{
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$postedToken = filter_input(INPUT_POST, 'token');
					if(!empty($postedToken) && isTokenValid($postedToken))
					{
						//probably i should do that isset stuff
						$model = new $this->model_name();
						$model->set_properties($_POST);
						if($model->is_valid() && array_key_exists($model->get_properties()['lesson'], $lessons)) //must make sure the lesson selected belongs to this user.
						{
							$model->create();
							add_alert('Successfully created!', Alert_Type::SUCCESS);
							return redirect($this->model_name, 'index');
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
				$view_to_show = 'views/' . strtolower($this->model_name) . '/create.php';
				if(!file_exists($view_to_show))
				{
					$view_to_show = 'views/shared/create.php';
				}

			$properties = $this->model_name::get_available_properties();
			$types = $this->model_name::get_types();
				require_once('views/shared/layout.php');
			}
			else
			{
					add_alert('Oops, you don\'t have any lessons. Exercises must be added to lessons. Please <a href="/?controller=lesson&action=create">create a lesson</a> before creating an exercise!', Alert_Type::DANGER);
					redirect('exercise', 'index');
			}
		}


		public function update() { //only differences: validation + get lessons
			require_once('models/lesson.php');
			$lessons = lesson::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('lesson' => $lessons);
			if (!isset($_GET['id']) || !exercise::is_owner($_GET['id'], $_SESSION['user']->get_id()))
			{
				return call('pages', 'error');
			}
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken))
				{
					$model = new $this->model_name();
					$model->set_id($_GET['id']); //i should not trust that...
					$model->set_properties($_POST);
					if($model->is_valid() && array_key_exists($model->get_properties()['lesson'], $lessons))
					{
						$model->update();
						add_alert('Successfully updated!', Alert_Type::SUCCESS);
						return redirect($this->model_name, 'index');
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
				$view_to_show = 'views/' . strtolower($this->model_name) . '/update.php';
				if(!file_exists($view_to_show))
				{
					$view_to_show = 'views/shared/update.php';
				}
				$types = $model::get_types();
				$properties = $model->get_properties();
				require_once('views/shared/layout.php');
			}
		}

		public function try_it() //check if can access!!
		{
			//requires exercise id, lesson id, concept id in query sting
			//at this point, i don't need to pass in the lesson id because it's on the exercise, but it will be needed if we decide to go back to having a pool of exercises, so i'm keeping it here
			if (!isset($_GET['id']) || !isset($_GET['lesson_id']) || !isset($_GET['concept_id']) || !exercise::can_access($_GET['id'], $_GET['lesson_id'], $_GET['concept_id'], $_SESSION['user']->get_id()))
			{
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}
			$exercise = exercise::get($_GET['id']); //what if it's null? don't want that.. need to be careful of that in base, too
			$lesson_id = $_GET['lesson_id'];

			require_once('models/lesson.php');
			$lessons = lesson::get_all_for_concept_and_student($_GET['concept_id'], $_SESSION['user']->get_id());


			require_once('models/concept.php');
			$concept = concept::get($_GET['concept_id']); //all i really want is the section id for links...

			//require_once('views/shared/editor.php');

			$view_to_show = 'views/exercise/editor.php';
			require_once('views/shared/layout.php');
		}

		public function mark_as_completed() //
		{
			require_once('enums/completion_status.php');

			/*
			to mark completed
				-must be allowed to access (check in stored procedure)
				-don't mark again if already completed.
			*/
			$success = false;

			
			//return success true/false
			if (isset($_POST['id']) && isset($_POST['lesson_id']) && isset($_POST['concept_id']) && exercise::can_access($_POST['id'], $_POST['lesson_id'], $_POST['concept_id'], $_SESSION['user']->get_id()))
			{
				//if it accidentally gets marked twice somehow, it's not a problem, but let's try to avoid
				if(exercise::get_completion_status($_POST['id'], $_POST['lesson_id'], $_POST['concept_id'], $_SESSION['user']->get_id()) != Completion_Status::COMPLETED)
				{
					exercise::set_completion_status($_POST['id'], $_POST['lesson_id'], $_POST['concept_id'], $_SESSION['user']->get_id(), Completion_Status::COMPLETED);
				}
				$success = true;
			}
			
			
			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');

			//should i check the referrer? $_SERVER['HTTP_REFERER'];
			//echo $_SERVER['HTTP_REFERER'];
			//it's still really easy to cheat, but it should help a little...
		}

	}
?>
