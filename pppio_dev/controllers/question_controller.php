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
				add_alert('Oops, you don\'t have any exams. Questions must be added to exams. Please <a href="?controller=exam&action=create">create an exam</a> before creating an question!', Alert_Type::DANGER);
				redirect('question', 'index');
			}
		}

		public function read_for_student(){
			$readonly = false;
			if (!isset($_GET['id']))
			{
				add_alert('The question you are trying to access doesn\'t exist.', Alert_Type::DANGER);
				return call('pages', 'error'); //or even call a blank editor for playing around in
			}

			require_once('models/exam.php');
			require_once('models/section.php');

			$question = question::get($_GET['id']);
			$exam = Exam::get_for_student($_GET['exam_id']);

			//make sure exam and question exists
			if(!empty($exam) and !empty($question))
			{
				$exam_props = $exam->get_properties();
				$times = Exam::get_times_for_student($_GET['exam_id'], $_SESSION['user']->get_id());

				//make sure there exists times for this user and exam
				if(!empty($times))
				{
					$now = intval(date_format(new DateTime(), 'U'));
					$start = date_create_from_format('Y-m-d H:i:s', $times[0]->start_time);
					$start_seconds = intval(date_format($start, 'U'));
					$close = date_create_from_format('Y-m-d H:i:s', $times[0]->close_time);
					$close_seconds = intval(date_format($close, 'U'));

					//make sure the current time is within the start time and close time
					if(!($start_seconds < $now) or !($now < $close_seconds))
					{
						add_alert("The time to take this exam has expired.", Alert_Type::DANGER);
						return call('pages', 'error');
					}
				}
				else
				{
					add_alert("You have not been given a time to take this exam. Please contact instructor.", Alert_Type::DANGER);
					return call('pages', 'error');
				}

				//make sure the question exists in the exam
				if(!array_key_exists($_GET['id'], $exam_props['questions']))
				{
					add_alert("Invalid question/exam combination.", Alert_Type::DANGER);
					return call('pages', 'error');
				}

				//make sure student is in section the exam is for
				if (!section::is_student($exam_props['section']->key, $_SESSION['user']->get_id()))
				{
					add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
					return call('pages', 'error');
				}
			}
			else
			{
				add_alert("The item you are trying to access doesn't exist.", Alert_Type::DANGER);
				return call('pages', 'error');
			}

			$student_answer = Question::get_code_file($question->get_id(), $exam->get_id());
			$view_to_show = 'views/question/editor.php';
			require_once('views/shared/layout.php');
		}

		public function save_code()
		{
			$success = false;
			if (isset($_POST['question_id']) && isset($_POST['exam_id']) && isset($_POST['contents']) && isset($_POST['completion_status_id']))
			{
				require_once('models/exam.php');
				require_once('models/section.php');

				$exam = Exam::get_for_student($_POST['exam_id']);
				$exam_props = $exam->get_properties();
				$times = Exam::get_times_for_student($_POST['exam_id'], $_SESSION['user']->get_id());

				//make sure exam, exam_props, and times arent null
				if(!empty($exam) and !empty($exam_props) and !empty($times))
				{
					$user_id = $_SESSION['user']->get_id();
					$now = intval(date_format(new DateTime(), 'U'));
					$start = date_create_from_format('Y-m-d H:i:s', $times[0]->start_time);
					$start_seconds = intval(date_format($start, 'U'));
					$close = date_create_from_format('Y-m-d H:i:s', $times[0]->close_time);
					$close_seconds = intval(date_format($close, 'U'));

					//make sure the current time is within the start time and close time
					if(!($start_seconds < $now) or !($now < $close_seconds))
					{
						add_alert("The time to take this exam has expired.", Alert_Type::DANGER);
						return call('pages', 'error');
					}

					//make sure the question exists in the exam
					if(!array_key_exists($_POST['question_id'], $exam_props['questions']))
					{
						add_alert("Invalid question/exam combination.", Alert_Type::DANGER);
						return call('pages', 'error');
					}

					//make sure student is in section the exam is for
					if (!section::is_student($exam_props['section']->key, $_SESSION['user']->get_id()))
					{
						add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
						return call('pages', 'error');
					}

					question::update_code_file($_POST['question_id'], $_POST['exam_id'], $user_id, $_POST['contents'], $_POST['completion_status_id']);
					$success = true;
				}
				else
				{
					add_alert("The item you are trying to access doesn't exist.", Alert_Type::DANGER);
					return call('pages', 'error');
				}
			}
			else
			{
				add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');
		}

		public function create_occurrence()
		{
			$success = false;
			if (isset($_POST['user_id']) && isset($_POST['question_id']) && isset($_POST['exam_id']))
			{
				$user_id = intval($_POST['user_id']);
				$question_id = intval($_POST['question_id']);
				$exam_id = intval($_POST['exam_id']);
				$date_of_occurrence = date_format(new DateTime(), 'Y-m-d H:i:s');

				Question::create_occurrence($user_id, $question_id, $exam_id, $date_of_occurrence);
				$success = true;
			}
			else
			{
				add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');
		}

		public function read_occurrences()
		{
			require_once('models/section.php');
			require_once('models/exam.php');

			//get all sections current user owns
			//get all exams in those sections
			$sections = Section::get_pairs_for_owner($_SESSION['user']->get_id());
			if(empty($sections))
			{
				add_alert("You do not have access to any sections.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
			else
			{
				$finished_array = array();

				foreach($sections as $s_key => $s_value)
				{
					$data_arr = Section::get_students($_SESSION['user']->get_id());

					if(empty($data_arr))
					{
						add_alert("You do not have access to any sections.", Alert_Type::DANGER);
						return call('pages', 'error');
					}
					else
					{
						$students_ret = array();
						foreach($data_arr as $section)
						{
							if($section['id'] == $s_key)
							{
								foreach($section['students'] as $st_key => $st_value)
								{
									$students_ret[$st_value->key] = $st_value->value;
								}
								$finished_array[$s_value]['students'] = $students_ret;
							}
						}
					}

					$exams_ret = array();
					$exams = Exam::get_all_for_section($s_key);
					foreach($exams as $e_key => $e_value)
					{
						$exams_ret[$e_value['id']] = $e_value['name'];
					}
					$finished_array[$s_value]['exams'] = $exams_ret;
				}
			}

			$_SESSION['arr'] = $finished_array;

			unset($data_arr);
			unset($e_key);
			unset($e_value);
			unset($exams);
			unset($exams_ret);
			unset($s_key);
			unset($s_value);
			unset($section);
			unset($sections);
			unset($st_key);
			unset($st_value);
			unset($students_ret);
			unset($finished_array);

			$view_to_show = 'views/question/read_occurrences.php';
			require_once('views/shared/layout.php');
		}
	}
?>
