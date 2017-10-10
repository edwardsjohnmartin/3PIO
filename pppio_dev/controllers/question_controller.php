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
			if (!isset($_GET['id']) || !isset($_GET['exam_id']))
			{
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}

			$question = question::get($_GET['id']);

			require_once('models/exam.php');
			$exam = Exam::get_for_student($_GET['id']);

			$view_to_show = 'views/question/editor.php';
			require_once('views/shared/layout.php');
		}
	}
?>
