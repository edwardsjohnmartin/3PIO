<?php
//TODO: Check what gets stored in $_SESSION['permissions'] when a user has different roles in multiple sections
//TODO: Create a set session variables function that will store the sections they are
//participating in into the session.

require_once('controllers/base_controller.php');
class UserController extends BaseController{
	public function index(){
		require_once('enums/role.php');

		//if the user is a teacher or admin, user can access
		//if the user is a student, user can only access if they are a ta

		$can_access = false;

		if($_SESSION['user']->get_properties()['role'] === Role::ADMIN or $_SESSION['user']->get_properties()['role'] === Role::TEACHER){
			$can_access = true;
		} else if($_SESSION['user']->get_properties()['role'] === Role::STUDENT and isset($_SESSION['sections_ta']) and count($_SESSION['sections_ta']) > 0){
			$can_access = true;
		}

		if($can_access){
			$models = ($this->model_name)::get_all();
			$view_to_show = 'views/user/index.php';
			require_once('views/shared/layout.php');
		}else{
			add_alert('Sorry, you dont have permission to access this page.', Alert_Type::DANGER);
			redirect_to_index();
		}
	}

	//Any user login. Takes in email and password through POST. Also stores what sections the user has access to in the SESSION.
	public function log_in(){
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){

			//This will pass the correct values to get_for_login depending on if a salt exists or not
			if(defined(salt)){
				$model = User::get_for_login($_POST['email'], salt . $_POST['password']);
			}else{
				$model = User::get_for_login($_POST['email'], $_POST['password']);
			}

			if ($model->get_id() != null){
				require_once('models/section.php');
				require_once('models/role.php');

				//if it's not null/the id isn't null, store the user
				$_SESSION['user'] = $model;

				//Store what sections they have access to as each role.
				$_SESSION['sections_student'] = Section::get_pairs_for_student($model->get_id());
				$_SESSION['sections_ta'] = Section::get_pairs_for_teaching_assistant($model->get_id());
				$_SESSION['sections_owner'] = Section::get_pairs_for_owner($model->get_id());
				$_SESSION['sections_is_study_participant'] = Section::get_study_pairs_for_student($model->get_id());

				$_SESSION['permissions'] = Role::get_permissions_for_role($model->get_properties()['role']);

				add_alert('Logged in! Welcome back, ' . htmlspecialchars($model->get_properties()['name']) . '.', Alert_Type::SUCCESS);

				session_write_close();
				redirect_to_index();
			} else{
				add_alert('Email and password do not match.', Alert_Type::DANGER);
			}
		}

		$view_to_show = 'views/user/log_in.php';
		require_once('views/shared/layout.php');
	}

	public function log_in_partner(){
		//restrictions - can't be the user that's already logged in
		//can't already be logged in as a partner
		if ($_SERVER['REQUEST_METHOD'] === 'POST'){
			//should make sure email and password aren't null...

			//This will pass the correct values to get_for_login depending on if a salt exists or not
			if(defined(salt)){
				$model = User::get_for_login($_POST['email'], salt . $_POST['password']);
			}else{
				$model = User::get_for_login($_POST['email'], $_POST['password']);
			}

			if ($model->get_id() == null){
				add_alert('Email and password do not match.', Alert_Type::DANGER);
			}
			else if($model->get_id() == $_SESSION['user']->get_id()){
				add_alert('You can\'t be your own partner.', Alert_Type::DANGER);
			}
			else if(isset($_SESSION['partners']) && $_SESSION['partners'] != null && array_key_exists($model->get_id(), $_SESSION['partners'])){
				add_alert(htmlspecialchars($model->get_properties()['name']) . ' is already your partner.', Alert_Type::DANGER);
			}
			else{
				$_SESSION['partners'][$model->get_id()] = $model; // ???
				add_alert(htmlspecialchars($model->get_properties()['name']) . ' is now logged in as a partner.', Alert_Type::SUCCESS);
				session_write_close();
				redirect('user', 'manage_partners');
			}
		}
		$view_to_show = 'views/user/log_in.php';
		require_once('views/shared/layout.php');
	}

	public function manage_partners(){
		$view_to_show = 'views/user/manage_partners.php';
		require_once('views/shared/layout.php');
	}

	public function log_out_partner(){
		if (!isset($_GET['id'])){
			redirect('user', 'manage_partners');
		}
		else if(!isset($_SESSION['partners']) || $_SESSION['partners'] == null || count($_SESSION['partners']) == 0 || !array_key_exists($_GET['id'], $_SESSION['partners'])){
			add_alert('This user is not a partner.', Alert_Type::DANGER);
			session_write_close();
			redirect('user', 'manage_partners');
		}
		else{
			$name = $_SESSION['partners'][$_GET['id']]->get_properties()['name'];
			unset($_SESSION['partners'][$_GET['id']]);
			add_alert('Successfully logged out ' . htmlspecialchars($name) . '.', Alert_Type::SUCCESS);
			session_write_close();
			redirect('user', 'manage_partners');
		}

	}

	public function log_out(){
		//clear the session and stuff
		//$_SESSION['user'] = null;
		//$_SESSION['partners'] = null;
		//$_SESSION['sections_student'] = null;
		//$_SESSION['sections_ta'] = null;
		//$_SESSION['sections_owner'] = null;
		//$_SESSION['sections_is_study_participant'] = null;
		//$_SESSION['permissions'] = null;

		unset($_SESSION['user']);
		unset($_SESSION['partners']);
		unset($_SESSION['sections_student']);
		unset($_SESSION['sections_ta']);
		unset($_SESSION['sections_owner']);
		unset($_SESSION['sections_is_study_participant']);
		unset($_SESSION['permissions']);

		add_alert('Logged out!', Alert_Type::SUCCESS);
		redirect_to_index();
	}

	//Create a new user. A user created with this function will always be a student
	public function create(){
		//user won't have any sections yet, so no need to fill.
		//get from post.
		//validate, fill.

		$model = new $this->model_name();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){
				/*
				things to check
				-user not logged in
				-password/confirm password match
				-email is available
				-email is an email (added to model validation. don't need to check again)
				 */

				$is_valid = true;
				if(!isset($_POST['email']) || !isset($_POST['name']) || !isset($_POST['password']) || !isset($_POST['confirm_password']) || ($_POST['email'] == null) || ($_POST['name'] == null) || ($_POST['password'] == null) || ($_POST['confirm_password'] == null)){
					$is_valid = false;
					add_alert('Please complete all fields.', Alert_Type::DANGER);
				} else{
					//make email lower-case
					$_POST['email'] = strtolower($_POST['email']);

					//php built-in way of checking if a string is a valid email address
					if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
						$is_valid = false;
						add_alert('Please enter a valid email address.', Alert_Type::DANGER);
					}
					//email isnt already used
					if(!$model::email_is_available($_POST['email'])){
						$is_valid = false;
						add_alert('An account is already associated with that email address.', Alert_Type::DANGER);
					}
					//passwords match
					if($_POST['password'] != $_POST['confirm_password']){
						$is_valid = false;
						add_alert('The passwords entered do not match.', Alert_Type::DANGER);
					}
					//password complexity
					if(strlen($_POST['password']) < 8){
						$is_valid = false;
						add_alert('Please use at least 8 characters in your password.', Alert_Type::DANGER);

					}

					//create the new user with the validated properties
					$model = new $this->model_name();
					$model->set_properties($_POST); //i need to add the server salt to the password!
					$model->set_properties(array('role'=>3)); //HARD CODED STUDENT!!

					if($is_valid && !$model->is_valid()){
						$is_valid = false;
						add_alert('This user is not valid.', Alert_Type::DANGER);
					}
				}

				//This will pass the correct values to set_properties depending on if a salt exists or not
				if(defined(salt)){
					$model->set_properties(array('password'=>(salt . $model->get_properties()['password'])));
				}else{
					$model->set_properties(array('password'=> $_POST['password']));
				}

				if($is_valid){
					$model->create(); //this could fail on the email still...
					//the password has already been cleared
					$_SESSION['user'] = $model;
					require_once('models/role.php');
					$_SESSION['permissions'] = Role::get_permissions_for_role($model->get_properties()['role']);
					add_alert('Welcome to 3PIO, ' . htmlspecialchars($model->get_properties()['name']) . '!', Alert_Type::DANGER);
					redirect_to_index();
				}
			} else{
				add_alert('Please try again.', Alert_Type::DANGER);
			}
		}
		$view_to_show = 'views/user/create.php';
		$model_props = $model->get_properties();
		$properties = array('email' => $model_props['email'], 'name' => $model_props['name'], 'password' => '', 'confirm_password' => '');
		$types = array('email' => Type::EMAIL, 'name' => Type::STRING, 'password' => Type::PASSWORD, 'confirm_password' => Type::PASSWORD);
		require_once('views/shared/layout.php');
	}

	//Update an existing users email, name, or role
	//Only teachers and admins can access this
	//Cannot update password from here
	public function update() {
		//user id has to be in $_GET
		if (!isset($_GET['id'])){
			return call('pages', 'error');
		}

		//get user to update
		$model = User::get($_GET['id']);

		//validate that one existed to for the id
		if(!$model or is_null($model)){
			add_alert('The user you are trying to update does not exist.', Alert_Type::DANGER);
			return call('pages', 'error');
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){

				$is_valid = true;
				if(!isset($_POST['email']) || !isset($_POST['name']) || !isset($_POST['role'])){
					$is_valid = false;
					add_alert('Please complete all fields.', Alert_Type::DANGER);
				}else{
					//make email lower-case
					$_POST['email'] = strtolower($_POST['email']);

					//php built-in way of checking if a string is a valid email address
					if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
						$is_valid = false;
						add_alert('Please enter a valid email address.', Alert_Type::DANGER);
					}
					//email isnt already used
					if(!$model::email_is_available($_POST['email'], $_GET['id'])){
						$is_valid = false;
						add_alert('An account is already associated with that email address.', Alert_Type::DANGER);
					}

					//update the existing user with the values in $_POST
					if($is_valid){
						$model->set_properties($_POST);
						$model->set_properties(array('password' => ''));
					}

					if($is_valid and $model->is_valid()){
						$model->update();
						add_alert('Successfully updated!', Alert_Type::SUCCESS);
						return redirect($this->model_name, 'index');
					} else{
						add_alert('Please try again.', Alert_Type::DANGER);
					}
				}
			} else{
				add_alert('Please try again.', Alert_Type::DANGER);
			}
		}

		$view_to_show = 'views/shared/update.php';
		$types = $model::get_types();
		$properties = $model->get_properties();
		unset($properties['password']);
		unset($types['password']);
		require_once('views/shared/layout.php');
	}

	public function delete(){
		if (!isset($_GET['id'])){
			add_alert("No user defined to delete.", Alert_Type::DANGER);
			return call('pages', 'error');
		}

		if ($_SESSION['user']->get_id() == $_GET['id']){
			add_alert("You cannot delete youself.", Alert_Type::DANGER);
			return call('pages', 'error');
		}

		$model = ($this->model_name)::get($_GET['id']);
		if ($model == null){
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

	//Page for a user to see their account details
	public function profile(){
		//Make sure user is logged in
		$user_id = $_SESSION['user']->get_id();

		//Get user model for user that is logged in
		$user = User::get($user_id);
		$properties = $user->get_properties();

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$postedToken = filter_input(INPUT_POST, 'token');
			if(!empty($postedToken) && isTokenValid($postedToken)){

				//current password has to be correct
				//This will pass the correct values to get_for_login depending on if a salt exists or not
				if(defined(salt)){
					$model = User::get_for_login($properties['email'], salt . $_POST['cur_password']);
				}else{
					$model = User::get_for_login($properties['email'], $_POST['cur_password']);
				}

				if($model->get_id() == null){
					add_alert('The current password was not entered correctly.', Alert_Type::DANGER);
				}else{
					//new and conf password must be over 8 characters
					if(strlen($_POST['new_password']) < 8 or strlen($_POST['conf_password']) < 8){
						add_alert('The new password has to be at least 8 characters.', Alert_Type::DANGER);
					}else{
						//new and conf password must match
						if($_POST['new_password'] != $_POST['conf_password']){
							add_alert('The passwords entered do not match.', Alert_Type::DANGER);
						}else{
							//This will pass the correct values to get_for_login depending on if a salt exists or not
							if(defined(salt)){
								$model->update_password(salt . $_POST['new_password']);
							}else{
								$model->update_password($_POST['new_password']);
							}
							add_alert('Your password was successfully changed.', Alert_Type::SUCCESS);
						}
					}
				}
			}
		}

		$view_to_show = 'views/user/profile.php';
		$types = $user::get_types();
		unset($properties['password']);
		unset($types['password']);
		require_once('views/shared/layout.php');
	}

	//Ajax call to reset a users password to a randomly generated password
	public function reset_password(){
		require_once('enums/role.php');
		require_once('models/user.php');

		//make sure user is ta, teacher, or admin
		//make sure user_id is set
		$can_access = false;

		if($_SESSION['user']->get_properties()['role'] === Role::ADMIN or $_SESSION['user']->get_properties()['role'] === Role::TEACHER){
			$can_access = true;
		} else if($_SESSION['user']->get_properties()['role'] === Role::STUDENT and isset($_SESSION['sections_ta']) and count($_SESSION['sections_ta']) > 0){
			$can_access = true;
		}

		if(!isset($_POST['user_id'])){
			$can_access = false;
		}

		if($can_access){

			$user_model = User::get($_POST['user_id']);

			//Get a random password
			$password = $this->randomPassword();

			//This will pass the correct values to get_for_login depending on if a salt exists or not
			if(defined(salt)){
				$user_model->update_students_password($_POST['user_id'], salt . $password);
			}else{
				$user_model->update_students_password($_POST['user_id'], $password);
			}

			$json_data = array('message' => $user_model->get_properties()['name'] . ' password was reset to ' . $password, 'success' => $can_access);
			require_once('views/shared/json_wrapper.php');
		}else{
			$json_data = array('message' => $user_model->get_properties()['name'] . ' password was not able to be reset', 'success' => $can_access);
			require_once('views/shared/json_wrapper.php');
		}
	}

	public function randomPassword() {
		$alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
}
?>
