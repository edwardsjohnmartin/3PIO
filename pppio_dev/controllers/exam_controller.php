<?php
	/*The controller for exams.
	Actions:
		index: List of all owned exams and links to view the exam times and updating the exam
		update_times: List of exam properties and table of current exam times and controls to change it
		create: Create a new exam by inputting its name, instruction, and section
		create_file: Create a new exam from a text file as well as the questions defined in the text file.
		review_exam: View for a ta/teacher to review a students answer for the question on the exam. Utilizes the dynamic view.
	*/
	//TODO: The review_exam action contains a lot more information than is required. Trimming it down would be a good idea.
	require_once('controllers/base_controller.php');
	class ExamController extends BaseController{
		/*This doesn't do anything much different from the index in the base controller, but it needs to be here
		because if the index in the base controller is used, it will return all existing exams instead of
		just the exams belonging to (created by) the current logged in user*/
        public function index(){
			$models = $this->model_name::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/exam/index.php';
			require_once('views/shared/layout.php');
        }

		/*Action for viewing the exam properties and the table of currently assigned exam times for the student.
		Also contains controls for selecting students to update their start and close times.*/
		public function update_times(){
			//if a exam id wasn't passed in, throw error
			if (!isset($_GET['id'])){
				add_alert("No exam was selected to update times for.", Alert_Type::DANGER);
				return call('pages', 'error');
			}else{
				$exam = exam::get($_GET['id']);

				//if the exam with the passed in id doesn't exist, throw error
				if(empty($exam)){
					add_alert("The exam you are trying to access doesn't exist.", Alert_Type::DANGER);
					return call('pages', 'error');
				}else{
					require_once("models/section.php");

					$s_id = $exam->get_section_id();
					$is_owner = Section::is_owner($s_id, $_SESSION['user']->get_id());
					$is_ta = Section::is_teaching_assistant($s_id, $_SESSION['user']->get_id());

					if(!$is_owner and !$is_ta){
						add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
						return call('pages', 'error');
					}

					if ($_SERVER['REQUEST_METHOD'] === 'POST'){
						$postedToken = filter_input(INPUT_POST, 'token');
						if(!empty($postedToken) && isTokenValid($postedToken)){
							$times = array('students' => $_POST['students'], 'exam_id' => $_GET['id'] , 'start_time' => $_POST['start_time'], 'close_time' => $_POST['close_time']);
							if(!isset($times['students']) || !$this::is_valid_date($times['start_time']) || !$this::is_valid_date($times['close_time'])){
								add_alert('Please try again.', Alert_Type::DANGER);
							}else{
								add_alert('Successfully updated times!', Alert_Type::SUCCESS);
								$exam->update_times($times);
							}
						}else{
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

        public function create(){
            require_once('models/section.php');
            $sections = section::get_pairs_for_owner($_SESSION['user']->get_id());
            $options = array('section' => $sections);
            if(count($sections) > 0){
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

		//Create exam and questions from a text file
		public function create_file(){
			$success = false;
			//need to check permissions

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken)){
					$failed = false;

					if(!isset($_FILES['file']['error']) || is_array($_FILES['file']['error'])){
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
							array('text/plain', 'text/x-c++', 'text/x-fortran', 'text/x-python'),
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

		/*Requires stud_id, exam_id, question_id in $_GET
		Allows a ta or teacher to review a students answers on a question on an exam
		Gathers information about the exam pertaining to the student and displays it
		using the dynamic code editor view.*/
		public function review_exam(){
			if (!isset($_GET['stud_id']) or !isset($_GET['exam_id']) or !isset($_GET['question_id'])){
				return call('pages', 'error');
			}

			require_once('models/section.php');
			require_once('models/exam.php');
			require_once('models/question.php');

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
						break;
					}
				}

				include('views/shared/dynamic_code_page.php');
				include('models/dynamic_view_objects/button.php');
				include('models/dynamic_view_objects/dropdown_item.php');

				//If there is no answer saved for the student on this question, set some defaults
				if(!isset($current_question_results))
				{
					$question_props = question::get($current_question_id)->get_properties();
					$current_question_results['contents'] = '--No Answer Recorded--';
					$current_question_results['start_code'] = $question_props['start_code'];
					$current_question_results['test_code'] = $question_props['test_code'];
					$current_question_results['instructions'] = $question_props['instructions'];
				}

				$title = '-' . $exam_props['name'] . '- Review for ' . $stud_props['name'];
				$left_title =  'Question Selector';

				//If the question was answered, show the last time is was updated in the left_subtitle area
				if(isset($current_question_results['date_update']))
				{
					$time = new DateTime($current_question_results['date_update']);
					if($time->format('G') >= 12)
					{
						$time = $time->format('g:iA M j, Y');
					}
					else
					{
						$time = $time->format('g:ia M j, Y');
					}

					$left_subtitle = 'Last Save: ' . $time;
				}
				else
				{
					$left_subtitle = '';
				}

				//Fill $buttons with the info for the tiles on the left navbar, they will be a link to review questions on the exam
				//They will be colored by the students completion_status and the current one will have a border
				$index = 0; //used to index the $exam_results array
				$q_index = 1; //used to put the question number in the tiles
				$buttons = array();
				foreach($exam_props['questions'] as $q_key => $q_value)
				{
					$btn_color = 'btn-default';

					//$exam_results has to be populated. if a student runs the code on a question, it will have an entry in $exam_results
					if(count($exam_results) > 0)
					{
						//check if there is an entry for the question
						if(array_key_exists($index, $exam_results) and $exam_results[$index]['id'] === $q_key)
						{
							//set $btn_color based on the completion_status
							if($exam_results[$index]['completion_status_id'] === 1)
							{
								$btn_color = 'btn-success';
							}
							else if($exam_results[$index]['completion_status_id'] === 2)
							{
								$btn_color = 'btn-started';
							}

							$index++;
						}
					}

					//add a class to the tile for the current question to it has a border
					if($q_key === intval($current_question_id))
					{
						$btn_color .= ' btn-current';
					}

					array_push($buttons, new button('btn_' . $q_key, $q_index, '"?controller=exam&action=review_exam&stud_id=' . $stud_id . '&exam_id=' . $exam_id . '&question_id=' . $q_key . '"', $btn_color));
					$q_index++;
				}

				//Scrub strings so they can be output in html
				//This will eventually be put into a method in site_functions.php
				$new_instructions = str_replace(array('"', "\r", "\n", "'"), array('&quot', '', '\n', '&quot'), $current_question_results['instructions']);
				$new_contents = str_replace(array('"', "\r", "\n", "'"), array('&quot', '', '\n', '&quot'), $current_question_results['contents']);
				$new_start_code = str_replace(array('"', "\r", "\n", "'"), array('&quot', '', '\n', '&quot'), $current_question_results['start_code']);
				$new_test_code = str_replace(array('"', "\r", "\n", "'"), array('&quot', '', '\n', '&quot'), $current_question_results['test_code']);

				$dropdown_items = array(
					new dropdown_item('drp_instructions', 'Instructions', $new_instructions),
					new dropdown_item('drp_contents', 'Student Answer',$new_contents),
					new dropdown_item('drp_start_code', 'Start Code', $new_start_code),
					new dropdown_item('drp_test_code', 'Test Code', $new_test_code)
				);

				$default_code = str_replace("&quot", "'", $new_contents);

				$params = array(
					'title' => $title,
					'left_title' => $left_title,
					'left_subtitle' => $left_subtitle,
					'buttons' => $buttons,
					'dropdown_items' => $dropdown_items,
					'default_code' => $default_code,
					'show_dropdown_index' => 0
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

		//Takes in a date and format string to check the validity of the date
		public static function is_valid_date($date, $format = 'm/d/Y g:i A'){
			return date($format, strtotime($date)) == $date;
		}
	}
?>
