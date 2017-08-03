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



				//how to store the user?
				//i'll need something for dual login eventually...
				//do i want an array?
				//let's just put it in the session on its own right now...

				if ($model->get_id() != null)
				{
					//if it's not null/the id isn't null, store the user
					$_SESSION['user'] = $model; // ???
					require_once('models/section.php');
					$_SESSION['sections'] = Section::get_pairs_for_student($model->get_id()); //right now, since this only happens on login, the user will have to log in and log out to see new sections. maybe i should refill when going to the section list for user page
					require_once('models/role.php');
					$_SESSION['permissions'] = Role::get_permissions_for_role($model->get_properties()['role']);
					add_alert('Logged in!', Alert_Type::SUCCESS);
					//session_write_close();
					redirect_to_index();
				}
				else
				{
					add_alert('Email and password do not match.', Alert_Type::DANGER);
					//session_write_close(); //this causes problems
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
			$_SESSION['permissions'] = null;

			add_alert('Logged out!', Alert_Type::SUCCESS);
			//session_write_close();
			redirect_to_index();
			
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
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken))
				{
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
						//$_SESSION['alerts'][] = 'Please complete all fields.';
						add_alert('Please complete all fields.', Alert_Type::DANGER);
					}
					else
					{
						if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) //i want to check here so i can give an error.
						{
							$is_valid = false;
							//$_SESSION['alerts'][] = 'Please enter a valid email address.';
							add_alert('Please enter a valid email address.', Alert_Type::DANGER);

						}
						if(!$model::email_is_available($_POST['email']))
						{
							$is_valid = false;
							//$_SESSION['alerts'][] = 'An account is already associated with that email address.';
							add_alert('An account is already associated with that email address.', Alert_Type::DANGER);
						}
						if($_POST['password'] != $_POST['confirm_password'])
						{
							$is_valid = false;
							//$_SESSION['alerts'][] = 'The passwords entered do not match.';
							add_alert('The passwords entered do not match.', Alert_Type::DANGER);
						}
						//password complexity
						if(strlen($_POST['password']) < 8)
						{
							$is_valid = false;
							//$_SESSION['alerts'][] = 'Please use at least 8 characters in your password.';
							add_alert('Please use at least 8 characters in your password.', Alert_Type::DANGER);

						}
						if($is_valid && !$model->is_valid())
						{
							$is_valid = false;
							//$_SESSION['alerts'][] = 'This user is not valid.';
							add_alert('This user is not valid.', Alert_Type::DANGER);
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
						require_once('models/role.php');
						$_SESSION['permissions'] = Role::get_permissions_for_role($model->get_properties()['role']);

						//$_SESSION['alerts'][] = 'Welcome to 3PIO, ' . htmlspecialchars($model->get_properties()['name']) . '!';
							add_alert('Welcome to 3PIO, ' . htmlspecialchars($model->get_properties()['name']) . '!', Alert_Type::DANGER);
						//session_write_close();
						redirect_to_index();
					}
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}
			//require_once('views/shared/create.php'); //will this be a problem? i think i will know what model by what controller is called...
			$view_to_show = 'views/user/create.php';
			require_once('views/shared/layout.php');
		}

	}
?>
