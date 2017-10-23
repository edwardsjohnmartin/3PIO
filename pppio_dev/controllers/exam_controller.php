<?php
	require_once('controllers/base_controller.php');
	class ExamController extends BaseController
	{
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
				add_alert("No exam was selected to update times for.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
			else
			{
				$exam = exam::get($_GET['id']);

				//if the exam with the passed in id doesn't exist, throw error
				if(empty($exam))
				{
					add_alert("The exam you are trying to access doesn't exist.", Alert_Type::DANGER);
					return call('pages', 'error');
				}
				else
				{
					require_once("models/section.php");

					$s_id = $exam->get_section_id();
					$is_owner = Section::is_owner($s_id, $_SESSION['user']->get_id());
					$is_ta = Section::is_teaching_assistant($s_id, $_SESSION['user']->get_id());

					if(!$is_owner and !$is_ta)
					{
						add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
						return call('pages', 'error');
					}

					if ($_SERVER['REQUEST_METHOD'] === 'POST')
					{
						$postedToken = filter_input(INPUT_POST, 'token');
						if(!empty($postedToken) && isTokenValid($postedToken))
						{
							$times = array('students' => $_POST['students'], 'exam_id' => $_GET['id'] , 'start_time' => $_POST['start_time'], 'close_time' => $_POST['close_time']);
							if(!isset($times['students']) || !$this::is_valid_date($times['start_time']) || !$this::is_valid_date($times['close_time']))
							{
								add_alert('Please try again.', Alert_Type::DANGER);
							}
							else
							{
								add_alert('Successfully updated times!', Alert_Type::SUCCESS);
								$exam->update_times($times);
							}
						}
						else
						{
							add_alert('Please try again.', Alert_Type::DANGER);
						}
					}
					$view_to_show = 'views/exam/update_times.php';
					$types = $exam::get_types();
					$properties = $exam->get_properties();
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
							array('text/plain', 'text/x-fortran', 'text/x-python'),
							true
						)) {
							add_alert('Invalid file format: '.$finfo->file($_FILES['file']['tmp_name']), Alert_Type::DANGER);
							$failed = true;
						}
					}

					if(!$failed)
					{
						require_once('importer.php');
						//header('Content-Type: text/plain; charset=utf-8');
						$exams = Importer::get_exams(file_get_contents($_FILES['file']['tmp_name']));

						foreach($exams as $exam)
						{
							//validate...
							$exam->set_properties(array('owner' => $_SESSION['user']->get_id(), 'section' => 1));
							$exam->create(); //this will set the id
							foreach($exam->get_properties()['questions'] as $question) //the getter is bad... :/
							{
								$question->set_properties(array('exam' => $exam->get_id(), 'language' => 1)); //python hard coded
								$question->create();
							}
						}
						$success = true;
						add_alert('Successfully created!', Alert_Type::SUCCESS);
					}
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}
			$view_to_show = 'views/exam/create_file.php';
			require_once('views/shared/layout.php');
		}

		public static function is_valid_date($date, $format = 'm/d/Y g:i A')
		{
			return date($format, strtotime($date)) == $date;
		}

		public function review_exam()
		{
		    //must pass in user_id through get  //must pass in exam_id through get
			if (!isset($_GET['stud_id']) or !isset($_GET['exam_id']) or !isset($_GET['question_id']))
			{
				return call('pages', 'error');
			}

			require_once('models/section.php');
			require_once('models/exam.php');

			$stud_id = $_GET['stud_id'];
			$student = User::get($stud_id);
			$stud_props = $student->get_properties();

			$exam_id = $_GET['exam_id'];
			$exam = exam::get_for_student($exam_id);
			$exam_props = $exam->get_properties();

			$current_question_id = $_GET['question_id'];
			$section_id = $exam->get_section_id();

			$user_id = $_SESSION['user']->get_id();
			$is_owner = Section::is_owner($section_id, $user_id);
			$is_ta = Section::is_teaching_assistant($section_id, $user_id);

		    //must be teacher or ta for section exam belongs to
			if($is_ta or $is_owner)
			{
				unset($user_id);
				unset($is_ta);
				unset($is_owner);

				$exam_results = Exam::get_exam_review_for_student($exam_id, $stud_id);
				$exam_props = $exam->get_properties();

				foreach($exam_results as $e_key => $e_value)
				{
					if($e_value['id'] == $current_question_id)
					{
						$current_question_results = $e_value;
					}
					//$exam_results[$e_value['id']] = $e_value;
					//unset($exam_results[$e_key]);
					//unset($exam_results[$e_value['id']]['id']);
				}

				include('views/shared/site_functions.php');
				include('models/html_objects/button.php');
				include('models/html_objects/dropdown_item.php');

				$title = $exam_props['name'] . ' Review for ' . $stud_props['name'];
				$left_title =  $current_question_results['name'];
				$left_subtitle = 'Date Updated: ' . $current_question_results['date_update'];

				$index = 1;
				$buttons = array();
				foreach($exam_props['questions'] as $q_key => $q_value)
				{
					array_push($buttons, new button('btn_' . $q_key, $index, '"?controller=exam&action=review_exam&stud_id=' . $stud_id . '&exam_id=' . $exam_id . '&question_id=' . $q_key . '"'));
					$index++;
				}

				//$find = '"';
				//$replace = '&quot';

				$new_contents = str_replace('"', '&quot', $current_question_results['contents']);
				$new_contents = str_replace("\r", '', $new_contents);
				$new_contents = str_replace("\n", '\n', $new_contents);

				$new_start_code = str_replace('"', '&quot', $current_question_results['start_code']);
				$new_start_code = str_replace("\r", '', $new_start_code);
				$new_start_code = str_replace("\n", '\n', $new_start_code);

				$new_test_code = str_replace('"', '&quot', $current_question_results['test_code']);
				$new_test_code = str_replace("\r", '', $new_test_code);
				$new_test_code = str_replace("\n", '\n', $new_test_code);

				$dropdown_items = array(
					new dropdown_item('drp_instructions', 'Instructions', $current_question_results['instructions']),
					new dropdown_item('drp_contents', 'Student Answer',$new_contents),
					new dropdown_item('drp_start_code', 'Start Code', $new_start_code),
					new dropdown_item('drp_test_code', 'Test Code', $new_test_code)
				);
				if($current_question_results['start_code'] != "")
				{
					//$default_code = str_replace(array("\r", "\""), "&quot", $current_question_results['start_code']);
				}

				$params = array(
					'title' => $title,
					'left_title' => $left_title,
					'left_subtitle' => $left_subtitle,
					'buttons' => $buttons,
					'dropdown_items' => $dropdown_items
					//'default_code' => $default_code
				);
				$view_to_show = "";
				require_once('views/shared/layout.php');
				create_code_editor_view($params);
			}
			else
			{
				add_alert("You do not have access to this section.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
		}
	}
?>
