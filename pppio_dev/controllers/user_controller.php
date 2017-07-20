<?php
	require_once('controllers/base_controller.php');
	class UserController extends BaseController
	{
		//now has the basic actions

		public function log_in()
		{
			//i need to make sure not already logged in

			//if get, show the login page

			//if post
				//get user
				//if good, redirect
				//else show page again
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				//probably i should do that isset stuff
				//$_POST['email'];
				//$_POST['password'];
				//i need to add a server salt

				//should make sure email and password aren't null...

				$model = User::get_for_login($_POST['email'], $_POST['password']);

				//if it's not null/the id isn't null, store the user
				$_SESSION['user'] = $model; // ???

				//how to store the user?
				//i'll need something for dual login eventually...
				//do i want an array?
				//let's just put it in the session on its own right now...

				if ($model->get_id() != null)
				{
					require_once('models/section.php');
					$_SESSION['sections'] = Section::get_pairs_for_user($model->get_id()); //right now, since this only happens on login, the user will have to log in and log out to see new sections. maybe i should refill when going to the section list for user page
					$_SESSION['alerts'][] = 'Logged in!';
					session_write_close();
					header('Location: ' . '/');
				}
				else
				{
					$_SESSION['alerts'][] = 'Email and password do not match.';
				}
			}
			//require_once('views/user/log_in.php');

			$view_to_show = 'views/user/log_in.php';
			require_once('views/shared/layout.php');

		}

		public function log_out()
		{
			//clear the session and stuff
			$_SESSION['user'] = null;
			$_SESSION['sections'] = null;

			$_SESSION['alerts'][] = 'Logged out!';
			session_write_close();
			header('Location: ' . '/');
			
		}


		public function create()
		{
			//user won't have any sections yet, so no need to fill.

			//get from post.
			//validate, fill.
			//$model_name = $this->model_name; //not the best way to do this.
			//if there isn't post data, or if the data is not valid, i need to show the form.
			//i should show errors somehow. how?
			$model = new $this->model_name();

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				//probably i should do that isset stuff

				/*
				things to check
				-user not logged in
				-password/confirm password match
				-email is available
				-email is an email (added to model validation. don't need to check again)
				*/
				$model = new $this->model_name();
				$model->set_properties($_POST); //i need to add the server salt to the password!
				$model->set_properties(array('role'=>1)); //HARD CODED ADMIN!!


				$is_valid = true;
				if(!isset($_POST['email']) || !isset($_POST['name']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || ($_POST['email'] == null) || ($_POST['name'] == null) || ($_POST['password'] == null) || ($_POST['confirm_password'] == null))
				{
					$is_valid = false;
					$_SESSION['alerts'][] = 'Please complete all fields.';
				}
				else
				{
					if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) //i want to check here so i can give an error.
					{
						$is_valid = false;
						$_SESSION['alerts'][] = 'Please enter a valid email address.';

					}
					if(!$model::email_is_available($_POST['email']))
					{
						$is_valid = false;
						$_SESSION['alerts'][] = 'An account is already associated with that email address.';
					}
					if($_POST['password'] != $_POST['confirm_password'])
					{
						$is_valid = false;
						$_SESSION['alerts'][] = 'The passwords entered do not match.';
					}
					//password complexity
					if(strlen($_POST['password']) < 8)
					{
						$is_valid = false;
						$_SESSION['alerts'][] = 'Please use at least 8 characters in your password.';

					}
					if($is_valid && !$model->is_valid())
					{
						$is_valid = false;
						$_SESSION['alerts'][] = 'This user is not valid.';
					}
				}

				$model->set_properties(array('password'=> $_POST['password'])); //add salt here

				if($is_valid)
				{
					

					//add alerts to session or something
					//http://getbootstrap.com/components/#alerts
					//redirect header("Location: ...");
					$model->create(); //this could fail on the email still...
					//the password has already been cleared

					$_SESSION['user'] = $model;

					$_SESSION['alerts'][] = 'Welcome to 3PIO, ' . $model->get_properties()['name'] . '!';
					session_write_close();
					header('Location: ' . '/');
				}
			}
			//require_once('views/shared/create.php'); //will this be a problem? i think i will know what model by what controller is called...
			$view_to_show = 'views/user/create.php';
			require_once('views/shared/layout.php');
		}

	}
?>
