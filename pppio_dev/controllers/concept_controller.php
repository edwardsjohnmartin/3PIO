<?php
	require_once('controllers/base_controller.php');
	class ConceptController extends BaseController
	{
		public function index()
		{

			$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/shared/index.php';
			require_once('views/shared/layout.php');
		}

		public function create()
		{
			require_once('models/section.php');
			require_once('models/lesson.php');
			$sections = section::get_pairs_for_owner($_SESSION['user']->get_id());
			$lessons = lesson::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('section' => $sections, 'lessons' => $lessons);
			if(count($sections) > 0)
			{
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$postedToken = filter_input(INPUT_POST, 'token');
					if(!empty($postedToken) && isTokenValid($postedToken))
					{
						$model = new $this->model_name();
						$model->set_properties($_POST);
						if($model->is_valid() && array_key_exists($model->get_properties()['section'], $sections)) //must make sure the lesson selected belongs to this user.
						{
							$lessons_belong_to_user = true;
							foreach($model->get_properties()['lessons'] as $lesson)
							{
								if(!array_key_exists($lesson, $lessons))
								{
									$lessons_belong_to_user = false;
									break;
								}
							}
							if($lessons_belong_to_user)
							{
								//add alerts to session or something
								//http://getbootstrap.com/components/#alerts
								//redirect header("Location: ...");
								$model->create();
								//$_SESSION['alerts'][] = 'Successfully created!';
								add_alert('Successfully created!', Alert_Type::SUCCESS);
								//session_write_close();
								return redirect($this->model_name, 'index');
							}
							else
							{
								add_alert('Please try again. 1', Alert_Type::DANGER);
							}
						}
						else
						{
							add_alert('Please try again. 2', Alert_Type::DANGER);
						}
					}
					else
					{
						add_alert('Please try again. 3', Alert_Type::DANGER);
					}
				}
				//require_once('views/shared/create.php'); //will this be a problem? i think i will know what model by what controller is called...
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
					add_alert('Oops, you don\'t have any sections. Concepts must be added to section. Please <a href="?controller=section&action=create">create a section</a> before creating an exercise!', Alert_Type::DANGER);
					redirect('concept', 'index');
			}
		}

		public function update() { //only differences: validation + get lessons
			require_once('models/section.php');
			require_once('models/lesson.php');
			if (!isset($_GET['id']) || !concept::is_owner($_GET['id'], $_SESSION['user']->get_id()))
			{
				return call('pages', 'error');
			}
			$sections = section::get_pairs_for_owner($_SESSION['user']->get_id());
			$lessons = lesson::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('section' => $sections, 'lessons' => $lessons);
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken))
				{
					$model = new $this->model_name();
					$model->set_id($_GET['id']); //i should not trust that...
					$model->set_properties($_POST);
					if($model->is_valid() && array_key_exists($model->get_properties()['section'], $sections))
					{
						$lessons_belong_to_user = true;
						foreach($model->get_properties()['lessons'] as $lesson)
						{
							if(!array_key_exists($lesson, $lessons))
							{
								$lessons_belong_to_user = false;
								break;
							}
						}
						if($lessons_belong_to_user)
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

		public function read()
		{
			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}
			else
			{
				$model = ($this->model_name)::get($_GET['id']);
				if($model == null)
				{
					add_alert('The item you are trying to access doesn\'t exist.', Alert_Type::DANGER);
					return call('pages', 'error');
				}
				else
				{
					$is_owner = concept::is_owner($model->get_id(), $_SESSION['user']->get_id());
					$is_ta = concept::is_teaching_assistant($model->get_id(), $_SESSION['user']->get_id());

					//Because these return arrays, they are destroyed before the program gets to the view
					//By storing it in the session, we can make sure it gets to the view
					$_SESSION['progress'] = concept::get_progress($model->get_id());
					$_SESSION['project_completion'] = Concept::get_project_completion($model->get_id());

					$view_to_show = 'views/' . strtolower($this->model_name) . '/read.php';
					if(!file_exists($view_to_show))
					{
						$view_to_show = 'views/shared/read.php';
					}
					$types = $model::get_types();
					$properties = $model->get_properties();
					require_once('views/shared/layout.php');
				}
			}
		}
	}
?>
