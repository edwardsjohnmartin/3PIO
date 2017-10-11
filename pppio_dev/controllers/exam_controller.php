<?php
	require_once('controllers/base_controller.php');
	class ExamController extends BaseController
	{
        //now has the basic actions

        public function index()
		{
			$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/exam/index.php';
			require_once('views/shared/layout.php');
        }

		public function update_times()
		{
			//if a exam id wasn't passed in, throw error
			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}
			else
			{
				$model = ($this->model_name)::get($_GET['id']);
				//if the exam with the passed in id doesn't exist, throw error
				if($model == null)
				{
					return call('pages', 'error');
				}
				else
				{
					$view_to_show = 'views/exam/update_times.php';
					$types = $model::get_types();
					$properties = $model->get_properties();
					require_once('views/shared/layout.php');
				}
			}
		}

        public function create()
        {
            require_once('models/section.php');
            $sections = section::get_pairs_for_owner($_SESSION['user']->get_id());
            $options = array('section' => $sections);
            if(count($sections) > 0)
			{
                if ($_SERVER['REQUEST_METHOD'] === 'POST')
                {
					$postedToken = filter_input(INPUT_POST, 'token');
					if(!empty($postedToken) && isTokenValid($postedToken))
					{
						//probably i should do that isset stuff
						$model = new $this->model_name();
						$model->set_properties($_POST);
						$model->set_owner($_SESSION['user']->get_id());
						if($model->is_valid())
						{
                            $model->create();
                            add_alert('Successfully created!', Alert_Type::SUCCESS);
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
                $view_to_show = 'views/' . strtolower($this->model_name) . '/create.php';
				if(!file_exists($view_to_show))
				{
					$view_to_show = 'views/shared/create.php';
                }
                $properties = $this->model_name::get_available_properties();
                $types = $this->model_name::get_types();
				unset($properties['owner']);
				unset($types['owner']);
				unset($properties['questions']);
				unset($types['questions']);
                require_once('views/shared/layout.php');
            }
            else
            {
                add_alert('Oops, you don\'t have any sections. Exams must be added to section. Please <a href="?controller=section&action=create">create a section</a> before creating an exam!', Alert_Type::DANGER);
                redirect('section', 'index');
            }
        }

		public function create_file()
		{
			$success = false;
			//need to check permissions

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken)){
					$failed = false;

					if(!isset($_FILES['file']['error']) || is_array($_FILES['file']['error']))
					{
						add_alert('Invalid file.', Alert_Type::DANGER);
						$failed = true;
					}

					if(!$failed)
					{
						switch ($_FILES['file']['error']) {
							case UPLOAD_ERR_OK:
								break;
							case UPLOAD_ERR_NO_FILE:
								add_alert('No file sent.', Alert_Type::DANGER);
								$failed = true;
								break;
							case UPLOAD_ERR_INI_SIZE:
							case UPLOAD_ERR_FORM_SIZE:
								add_alert('Exceeded filesize limit.', Alert_Type::DANGER);
								$failed = true;
								break;
							default:
								add_alert('Unknown errors.', Alert_Type::DANGER);
								$failed = true;
						}
					}

					//max length = 2MB = 2097152 bytes
					if (!$failed && $_FILES['file']['size'] > 2097152) {
						add_alert('Exceeded filesize limit.', Alert_Type::DANGER);
						$failed = true;
					}

					if(!$failed)
					{
						$finfo = new finfo(FILEINFO_MIME_TYPE);
						if (false === $ext = array_search(
							$finfo->file($_FILES['file']['tmp_name']),
							array('text/plain', 'text/x-fortran'),
							true
						)) {
							add_alert('Invalid file format.', Alert_Type::DANGER);
							$failed = true;
						}
					}

					if(!$failed)
					{
						require_once('importer.php');
						//header('Content-Type: text/plain; charset=utf-8');
						$lessons = Importer::get_lessons(file_get_contents($_FILES['file']['tmp_name']));

						foreach($lessons as $lesson)
						{
							//validate...
							$lesson->set_properties(array('owner' => $_SESSION['user']->get_id()));
							$lesson->create(); //this will set the id
							foreach($lesson->get_properties()['exercises'] as $exercise) //the getter is bad... :/
							{
								$exercise->set_properties(array('lesson' => $lesson->get_id(), 'language' => 1)); //python hard coded
								$exercise->create();
							}
						}
						$success = true;
						add_alert('Successfully created!', Alert_Type::SUCCESS);
						//return redirect('lesson', 'index');
					}
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}
			$view_to_show = 'views/lesson/create_file.php';
			require_once('views/shared/layout.php');
		}

		public function read_for_student(){
			if (!isset($_GET['id'])) {
				return call('pages', 'error');
			}

			//$can_access = exam::can_preview($_GET['id'], $_SESSION['user']->get_id());

			//if(!$can_access){
				//return call('pages', 'error');
			//}

			$exam = Exam::get_for_student($_GET['id']);
			$view_to_show = 'views/exam/read_for_student.php';
			require_once('views/shared/layout.php');
		}
	}
?>