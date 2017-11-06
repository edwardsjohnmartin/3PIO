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

				$user_id = intval($_SESSION['user']->get_id());

				$securable_id = Securable::get_id_from_string($_POST['activity_name']);
				$activity_id = intval($_POST['activity_id']);

				$mouse_clicks = intval($_POST['mouseclicks']);
				$key_presses = intval($_POST['keypresses']);
				$times_ran = intval($_POST['timesran']);

				Session::write_session($user_id, $securable_id, $activity_id, $start_time, $end_time, $mouse_clicks, $key_presses, $times_ran);
			}
		}

		//Get all sessions for a specific student and pass them into session/read_all_for_student view to show them in tabular format
		public function read_all_for_student()
		{
			//Checks for the user to access a route for getting session info from the db
			//logged in user has to be a teacher
			//logged in user has to be a ta

			//user_id has to be in GET
			if(!isset($_GET['user_id']))
			{
				add_alert('Please try again.', Alert_Type::DANGER);
				return call('pages', 'error');
			}

			require_once('models/session.php');

			//$sessions = Session::get_all_for_student($_GET['user_id']);
			$exercise_sessions = Session::get_all_of_type_for_student(Securable::EXERCISE, $_GET['user_id']);
			$project_sessions = Session::get_all_of_type_for_student(Securable::PROJECT, $_GET['user_id']);
			$question_sessions = Session::get_all_of_type_for_student(Securable::QUESTION, $_GET['user_id']);
			$view_to_show = 'views/session/read_all_for_student.php';
			require_once('views/shared/layout.php');
		}

	}
?>