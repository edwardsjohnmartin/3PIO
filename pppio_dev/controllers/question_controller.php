<?php
	require_once('controllers/base_controller.php');
	class QuestionController extends BaseController
	{
		public function index()
		{
			$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/shared/index.php';
			require_once('views/shared/layout.php');
		}

		public function create()
		{
			require_once('models/exam.php');
			$exams = exam::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('exam' => $exams);
			if(count($exams) > 0)
			{
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$postedToken = filter_input(INPUT_POST, 'token');
					if(!empty($postedToken) && isTokenValid($postedToken))
					{
						//probably i should do that isset stuff
						$model = new $this->model_name();
						//Default weight to 10 if nothing was entered
						if(empty($_POST['weight']))
						{
							$_POST['weight'] = "10";
						}
						$model->set_properties($_POST);
						if($model->is_valid() && array_key_exists($model->get_properties()['exam'], $exams)) //must make sure the lesson selected belongs to this user.
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
				add_alert('Oops, you don\'t have any exams. Questions must be added to exams. Please <a href="?controller=exam&action=create">create a exam</a> before creating an question!', Alert_Type::DANGER);
				redirect('question', 'index');
			}
		}

		public function read_for_student(){
			$readonly = false;
			if (!isset($_GET['id']))
			{
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}

			$question = question::get($_GET['id']);

			require_once('models/exam.php');
			$exam = Exam::get_for_student($_GET['exam_id']);
			$student_answer = Question::get_code_file($question->get_id(), $exam->get_id());

			$view_to_show = 'views/question/editor.php';
			require_once('views/shared/layout.php');
		}

		public function save_code()
		{
			$success = true;
			if (isset($_POST['question_id']) && isset($_POST['exam_id']) && isset($_POST['contents']) && isset($_POST['completion_status_id']))
			{
				require_once('models/exam.php');
				$exam = Exam::get_for_student($_POST['exam_id']);
				if($exam != null)
				{
					$user_id = $_SESSION['user']->get_id();
					question::update_code_file($_POST['question_id'], $_POST['exam_id'], $user_id, $_POST['contents'], $_POST['completion_status_id']);
					$success = true;
				}
			}
			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');
		}

		public function mark_as_completed() //
		{
			require_once('enums/completion_status.php');
			$success = false;

			if (isset($_POST['question_id']) && isset($_POST['exam_id']))
			{
				//if it accidentally gets marked twice somehow, it's not a problem, but let's try to avoid
				if(question::get_completion_status($_POST['question_id'], $_POST['exam_id'], $_SESSION['user']->get_id()) != Completion_Status::COMPLETED)
				{
					question::set_completion_status($_POST['question_id'], $_POST['exam_id'], $_SESSION['user']->get_id(), Completion_Status::COMPLETED);
				}
				$success = true;
			}

			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');
		}

		public function mark_as_in_progress()
		{
			require_once('enums/completion_status.php');
			$success = false;

			if (isset($_POST['question_id']) && isset($_POST['exam_id']))
			{
				//if it accidentally gets marked twice somehow, it's not a problem, but let's try to avoid
				if(question::get_completion_status($_POST['question_id'], $_POST['exam_id'], $_SESSION['user']->get_id()) != Completion_Status::STARTED)
				{
					question::set_completion_status($_POST['question_id'], $_POST['exam_id'], $_SESSION['user']->get_id(), Completion_Status::STARTED);
				}
				$success = true;
			}

			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');
		}
	}
?>
