<?php
	require_once('controllers/base_controller.php');
	class UserController extends BaseController
	{
		public function index()
		{
			$models = ($this->model_name)::get_all();
			$view_to_show = 'views/user/index.php';
			require_once('views/shared/layout.php');
		}

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
					$_SESSION['sections_student'] = Section::get_pairs_for_student($model->get_id());
					$_SESSION['sections_ta'] = Section::get_pairs_for_teaching_assistant($model->get_id()); //right now, since this only happens on login, the user will have to log in and log out to see new sections. maybe i should refill when going to the section list for user page
					$_SESSION['sections_owner'] = Section::get_pairs_for_owner($model->get_id());
					require_once('models/role.php');
					$_SESSION['permissions'] = Role::get_permissions_for_role($model->get_properties()['role']);
					add_alert('Logged in! Welcome back, ' . htmlspecialchars($model->get_properties()['name']) . '.', Alert_Type::SUCCESS);
					session_write_close();
					redirect_to_index();
				}
				else
				{
					add_alert('Email and password do not match.', Alert_Type::DANGER);
				}
			}

			$view_to_show = 'views/user/log_in.php';
			require_once('views/shared/layout.php');
		}

		public function log_in_partner()
		{

			//restrictions - can't be the user that's already logged in
			//can't already be logged in as a partner
			if ($_SERVER['REQUEST_METHOD'] === 'POST')
			{
 				//should make sure email and password aren't null...

				$model = User::get_for_login($_POST['email'], $_POST['password']);

				if ($model->get_id() == null)
				{
					add_alert('Email and password do not match.', Alert_Type::DANGER);
				}
				else if($model->get_id() == $_SESSION['user']->get_id())
				{
					add_alert('You can\'t be your own partner.', Alert_Type::DANGER);
				}
				else if(isset($_SESSION['partners']) && $_SESSION['partners'] != null && array_key_exists($model->get_id(), $_SESSION['partners']))
				{
					add_alert(htmlspecialchars($model->get_properties()['name']) . ' is already your partner.', Alert_Type::DANGER);
				}
				else
				{
					$_SESSION['partners'][$model->get_id()] = $model; // ???
					add_alert(htmlspecialchars($model->get_properties()['name']) . ' is now logged in as a partner.', Alert_Type::SUCCESS);
					session_write_close();
					redirect('user', 'manage_partners');
				}
			}
			$view_to_show = 'views/user/log_in.php';
			require_once('views/shared/layout.php');
		}

		public function manage_partners()
		{
			$view_to_show = 'views/user/manage_partners.php';
			require_once('views/shared/layout.php');
		}

		public function log_out_partner()
		{
			if (!isset($_GET['id']))
			{
					redirect('user', 'manage_partners');
			}
			else if(!isset($_SESSION['partners']) || $_SESSION['partners'] == null || count($_SESSION['partners']) == 0 || !array_key_exists($_GET['id'], $_SESSION['partners']))
			{
					add_alert('This user is not a partner.', Alert_Type::DANGER);
					session_write_close();
					redirect('user', 'manage_partners');
			}
			else
			{
					$name = $_SESSION['partners'][$_GET['id']]->get_properties()['name'];
					unset($_SESSION['partners'][$_GET['id']]);
					add_alert('Successfully logged out ' . htmlspecialchars($name) . '.', Alert_Type::SUCCESS);
					session_write_close();
					redirect('user', 'manage_partners');
			}

		}

		public function log_out()
		{
			//clear the session and stuff
			$_SESSION['user'] = null;
			$_SESSION['partners'] = null;
			$_SESSION['sections_student'] = null;
			$_SESSION['sections_ta'] = null;
			$_SESSION['sections_owner'] = null;
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
					/*
					things to check
					-user not logged in
					-password/confirm password match
					-email is available
					-email is an email (added to model validation. don't need to check again)
					*/
					$model = new $this->model_name();
					$model->set_properties($_POST); //i need to add the server salt to the password!
					$model->set_properties(array('role'=>3)); //HARD CODED STUDENT!!

					$is_valid = true;
					if(!isset($_POST['email']) || !isset($_POST['name']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || ($_POST['email'] == null) || ($_POST['name'] == null) || ($_POST['password'] == null) || ($_POST['confirm_password'] == null))
					{
						$is_valid = false;
						add_alert('Please complete all fields.', Alert_Type::DANGER);
					}
					else
					{
						if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) //i want to check here so i can give an error.
						{
							$is_valid = false;
							add_alert('Please enter a valid email address.', Alert_Type::DANGER);

						}
						if(!$model::email_is_available($_POST['email']))
						{
							$is_valid = false;
							add_alert('An account is already associated with that email address.', Alert_Type::DANGER);
						}
						if($_POST['password'] != $_POST['confirm_password'])
						{
							$is_valid = false;
							add_alert('The passwords entered do not match.', Alert_Type::DANGER);
						}
						//password complexity
						if(strlen($_POST['password']) < 8)
						{
							$is_valid = false;
							add_alert('Please use at least 8 characters in your password.', Alert_Type::DANGER);

						}
						if($is_valid && !$model->is_valid())
						{
							$is_valid = false;
							add_alert('This user is not valid.', Alert_Type::DANGER);
						}
					}

					$model->set_properties(array('password'=> $_POST['password'])); //add salt here

					if($is_valid)
					{
						$model->create(); //this could fail on the email still...
						//the password has already been cleared
						$_SESSION['user'] = $model;
						require_once('models/role.php');
						$_SESSION['permissions'] = Role::get_permissions_for_role($model->get_properties()['role']);
						add_alert('Welcome to 3PIO, ' . htmlspecialchars($model->get_properties()['name']) . '!', Alert_Type::DANGER);
						redirect_to_index();
					}
				}
				else
				{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}
			$view_to_show = 'views/user/create.php';
			$model_props = $model->get_properties();
			$properties = array('email' => $model_props['email'], 'name' => $model_props['name'], 'password' => '', 'confirm_password' => '');
			$types = array('email' => Type::EMAIL, 'name' => Type::STRING, 'password' => Type::PASSWORD, 'confirm_password' => Type::PASSWORD);
			require_once('views/shared/layout.php');
		}

		public function update() {
			//must set id and the rest too. id is separate.
			//for users especially, i need to be more careful.
			//this is a basic one without permissions.

			if (!isset($_GET['id']))
			{
				return call('pages', 'error');
			}

			//if there is post data...
			//todo: i need to check if the model actually exists on post, too!!!!
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken))
				{
					//probably i should do that isset stuff
					$model = new $this->model_name();
					$model->set_id($_GET['id']); //i should not trust that...
					$model->set_properties($_POST);
					$model->set_properties(array('password' => ''));
					if($model->is_valid())
					{
						$is_valid = true;
						if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) //i want to check here so i can give an error.
						{
							$is_valid = false;
							add_alert('Please enter a valid email address.', Alert_Type::DANGER);

						}
						if(!$model::email_is_available($_POST['email'], $_GET['id']))
						{
							$is_valid = false;
							add_alert('An account is already associated with that email address.', Alert_Type::DANGER);
						}
						if($is_valid)
						{
							$model->update(); //do i call validate here, or in the update function?
							//layout has already been created. can't add the alerts now
							//but redirecting anyway
							//could use ajax instead
							//for now, i'll stick to a redirect
							//add alerts to session or something
							//i want to redirect, but it doesn't seem like the php way...
							//$_SESSION['alerts'][] = 'Successfully updated!';
							add_alert('Successfully updated!', Alert_Type::SUCCESS);
							//session_write_close();
							return redirect($this->model_name, 'index');
						}
						//http://getbootstrap.com/components/#alerts
						//exit properly first!
						//redirect header("Location: ...");
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
				//require_once('views/shared/update.php');
				$view_to_show = 'views/shared/update.php';
				$types = $model::get_types();
				$properties = $model->get_properties();
				unset($properties['password']);
				unset($types['password']);
				require_once('views/shared/layout.php');
			}
			//i need to be better about the order of things.

		}

		public function delete() {
			if (!isset($_GET['id']))
			{
				add_alert("No user defined to delete.", Alert_Type::DANGER);
				return call('pages', 'error');
			}

			if ($_SESSION['user']->get_id() == $_GET['id'])
			{
				add_alert("You cannot delete youself.", Alert_Type::DANGER);
				return call('pages', 'error');
			}

			$model = ($this->model_name)::get($_GET['id']);
			if ($model == null)
			{
				add_alert("User does not exist.", Alert_Type::DANGER);
				return call('pages', 'error');
			}

			//Hard coded this in so only students can be deleted
			//Not sure what kind of issues could arise from deleting teachers and admins
			$userProps = $model->get_properties();
			if(!($userProps['role']->key === 3)) {
				add_alert("Only students can be deleted.", Alert_Type::DANGER);
				return call('pages', 'error');
			}

			$model->delete($_GET['id']);
			return redirect($this->model_name, 'index');
		}
	}
?>
