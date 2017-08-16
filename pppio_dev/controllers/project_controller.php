<?php
	require_once('controllers/base_controller.php');
	class ProjectController extends BaseController
	{

		public function create()
		{
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken)){
					$model = new $this->model_name();
					$model->set_properties($_POST);
					$model->set_properties(array('owner' => $_SESSION['user']->get_id()));
					if($model->is_valid())
					{
						//add alerts to session or something
						//http://getbootstrap.com/components/#alerts
						//redirect header("Location: ...");
						$model->create();
						add_alert('Successfully created!', Alert_Type::SUCCESS);
						return redirect('project', 'index');
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
			//require_once('views/shared/create.php'); //will this be a problem? i think i will know what model by what controller is called...
			$view_to_show = 'views/shared/create.php';
			$properties = project::get_available_properties();
			$types = project::get_types();
			unset($properties['owner']);
			unset($types['owner']);
			require_once('views/shared/layout.php');
		}

		public function update() {
			//check if this lesson belongs to me
			//check the owner!!!!!!!

			//must set id and the rest too. id is separate.
			//for users especially, i need to be more careful.
			//this is a basic one without permissions.

			if (!isset($_GET['id']) || !project::is_owner($_GET['id'], $_SESSION['user']->get_id()))
			{
				return call('pages', 'error');
			}

			//if there is post data...
			//todo: i need to check if the model actually exists on post, too!!!!
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken)){
					$model = new $this->model_name();
						$model->set_id($_GET['id']);
						$model->set_properties($_POST);
						$model->set_properties(array('owner' => $_SESSION['user']->get_id()));
						if($model->is_valid())
						{
							$model->update();
							add_alert('Successfully updated!', Alert_Type::SUCCESS);
							return redirect('project', 'index');
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
				$properties = $model->get_properties();
				$types = $model::get_types();
				unset($properties['owner']);
				unset($types['owner']);
				$view_to_show = 'views/shared/update.php';
				require_once('views/shared/layout.php');
			}
			//i need to be better about the order of things.

		}

		public function try_it()
		{
			//requires exercise id, lesson id, concept id in query sting
			//at this point, i don't need to pass in the lesson id because it's on the exercise, but it will be needed if we decide to go back to having a pool of exercises, so i'm keeping it here
			if (!isset($_GET['concept_id']) || !project::can_access($_GET['concept_id'], $_SESSION['user']->get_id()))
			{
				return call('pages', 'error');
			}
			require_once('models/concept.php');
			$concept = concept::get($_GET['concept_id']); //what if it's null? don't want that.. need to be careful of that in base, too
			if($concept == null)
			{
				return call('pages', 'error');
			}
			$concept_props = $concept->get_properties();
			$project = project::get($concept_props['project']->key);
			if($project == null)
			{
				return call('pages', 'error');
			}

			if(new Datetime($concept_props['project_due_date']) < new Datetime())
			{
				$readonly = true;
				add_alert('This project is now read-only because the due date has passed. Any changes made now will not be saved.', Alert_Type::INFO);
			}
			else
			{
				$readonly = false;
			}
			//get user's code, too
			//if user doesn't have code, use the project starter code
			$code = project::get_code_file($concept->get_id(), $_SESSION['user']->get_id());

			$view_to_show = 'views/project/editor.php';
			require_once('views/shared/layout.php');
		}

		public function check() //todo: the teacher/ta should be allowed to grade from here.
		{
			//must be the teacher... or be a ta...
			//pass in concept and user

			require_once('models/concept.php');
			if(!isset($_GET['concept_id']) || !isset($_GET['user_id']) || !concept::is_owner($_GET['concept_id'], $_SESSION['user']->get_id())) //i need to check for tas
			{
				return call('pages', 'error');
			}

			$concept = concept::get($_GET['concept_id']);
			if($concept == null)
			{
				return call('pages', 'error');
			}

			$project = project::get($concept->get_properties()['project']->key);
			if($project == null)
			{
				return call('pages', 'error');
			}

			$user = user::get($_GET['user_id']);
			if($user == null)
			{
				return call('pages', 'error');
			}

			$code = project::get_code_file($concept->get_id(), $_GET['user_id']);

			$view_to_show = 'views/project/check.php';
			require_once('views/shared/layout.php');
		}


		public function save_code() //
		{
			$success = false;
			//return success true/false
			//user ids come from the session
			if (isset($_POST['concept_id']) && isset($_POST['contents']))
			{			
				require_once('models/concept.php');
				$concept = concept::get($_POST['concept_id']);
				if($concept != null && new Datetime() < new Datetime($concept->get_properties()['project_due_date']) && project::can_access($_POST['concept_id'], $_SESSION['user']->get_id())) //should i check if all of the partners can access?
				{
					if(isset($_SESSION['partners']) && $_SESSION['partners'] != null)
					{
						$user_ids = array_keys($_SESSION['partners']);
					}
					$user_ids[] = $_SESSION['user']->get_id();
					project::update_code_file($_POST['concept_id'], $user_ids, $_POST['contents']);
					$success = true;
				}
			}
			
			$json_data = array('success' => $success);
			require_once('views/shared/json_wrapper.php');

			//should i check the referrer? $_SERVER['HTTP_REFERER'];
			//echo $_SERVER['HTTP_REFERER'];
			//it's still really easy to cheat, but it should help a little...
		}



	}
?>
