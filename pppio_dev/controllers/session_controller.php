<?php
	require_once('controllers/base_controller.php');
	class SessionController extends BaseController
	{
		//Save the session with the attributes in POST to the database for the logged in user
		public function save()
		{
			if(isset($_POST['start']))
			{
				$start_time = date("Y-m-d H:i:s", intval($_POST['start']));
				$end_time = date("Y-m-d H:i:s");

				if($end_time < $start_time)
				{
					$start_time = $end_time;
				}

				$user_id = intval($_SESSION['user']->get_id());

				$securable_id = Securable::get_id_from_string($_POST['activity_name']);
				$activity_id = intval($_POST['activity_id']);

				$mouse_clicks = intval($_POST['mouseclicks']);
				$key_presses = intval($_POST['keypresses']);
				$times_ran = intval($_POST['timesran']);
				$error_count = intval($_POST['errorCount']);

				Session::write_session($user_id, $securable_id, $activity_id, $start_time, $end_time, $mouse_clicks, $key_presses, $times_ran, $error_count);
			}
		}

		//Get all sessions for a specific student and pass them into session/read_all_for_student view to show them in tabular format
		public function read_all_for_student()
		{
			require_once("enums/role.php");
			require_once("enums/participation_type.php");

			$cur_user = $_SESSION['user'];
			$ta_sections = $cur_user->get_sections_by_participation_type(Participation_Type::TEACHING_ASSISTANT);

			//user has to either be an admin, teacher, or a ta for at least 1 section
			if($ta_sections === false and $cur_user->get_properties()['role'] !== Role::ADMIN and $cur_user->get_properties()['role'] !== Role::TEACHER)
			{
				add_alert('You do not have permission to access this.', Alert_Type::DANGER);
				return call('pages', 'error');
			}

			//user_id has to be in GET
			if(!isset($_GET['user_id']))
			{
				add_alert('A user was not specified.', Alert_Type::DANGER);
				return call('pages', 'error');
			}

			//user associated with user_id has to exist
			$user = User::get($_GET['user_id']);
			if(!$user)
			{
				add_alert('User does not exist.', Alert_Type::DANGER);
				return call('pages', 'error');
			}
			else
			{
				$name = $user->get_properties()['name'];
			}

			require_once('models/session.php');
			$exercise_sessions = Session::get_all_of_type_for_student(Securable::EXERCISE, $_GET['user_id']);
			$project_sessions = Session::get_all_of_type_for_student(Securable::PROJECT, $_GET['user_id']);
			$question_sessions = Session::get_all_of_type_for_student(Securable::QUESTION, $_GET['user_id']);
			$view_to_show = 'views/session/read_all_for_student.php';
			require_once('views/shared/layout.php');
		}

		public function index(){
			require_once("enums/role.php");
			require_once("enums/participation_type.php");

			$cur_user = $_SESSION['user'];
			$ta_sections = $cur_user->get_sections_by_participation_type(Participation_Type::TEACHING_ASSISTANT);

			//user has to either be an admin, teacher, or a ta for at least 1 section
			if($ta_sections === false and $cur_user->get_properties()['role'] !== Role::ADMIN and $cur_user->get_properties()['role'] !== Role::TEACHER)
			{
				add_alert('You do not have permission to access this.', Alert_Type::DANGER);
				return call('pages', 'error');
			}

			$users = User::get_all();
			$view_to_show = 'views/session/index.php';
			require_once('views/shared/layout.php');
		}
	}
?>
