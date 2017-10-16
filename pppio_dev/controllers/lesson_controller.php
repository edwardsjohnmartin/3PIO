<?php
	require_once('controllers/base_controller.php');
	class LessonController extends BaseController
	{
		public function index()
		{

			$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/lesson/index.php';
			require_once('views/shared/layout.php');
		}

		public function read_student() //the correct one should be called based on who is logged in. they shouldn't be different actions to the user.
		{
			if (!isset($_GET['id']) || !isset($_GET['concept_id']) || !lesson::can_access($_GET['id'], $_GET['concept_id'], $_SESSION['user']->get_id())) //should show a permission error possibly
			{
				return call('pages', 'error');
			}
			$lesson = lesson::get_for_concept_and_student($_GET['id'], $_GET['concept_id'], $_SESSION['user']->get_id());
			$view_to_show = 'views/lesson/read_student.php';
			require_once('views/shared/layout.php');

		}

		public function read_for_concept_for_student() //the correct one should be called based on who is logged in. they shouldn't be different actions to the user.
		{
			require_once('models/concept.php');

			if (!isset($_GET['concept_id'])) {
				return call('pages', 'error');				
			} 
			$can_preview = concept::can_preview($_GET['concept_id'], $_SESSION['user']->get_id());
			
			if(!(lesson::can_access_for_concept($_GET['concept_id'], $_SESSION['user']->get_id()) || $can_preview))
			{
				return call('pages', 'error');
			}
			$concept = concept::get($_GET['concept_id']);
			$lessons = lesson::get_all_for_concept_and_student($_GET['concept_id'], $_SESSION['user']->get_id());
			$view_to_show = 'views/lesson/read_for_concept_for_student.php';
			require_once('views/shared/layout.php');

		}

		public function create()
		{
			//get from post.
			//validate, fill.
			//$model_name = $this->model_name; //not the best way to do this.
			//if there isn't post data, or if the data is not valid, i need to show the form.
			//i should show errors somehow. how?

			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				//probably i should do that isset stuff
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
						return redirect('lesson', 'index');
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
			$properties = lesson::get_available_properties();
			$types = lesson::get_types();
			unset($properties['exercises']);
			unset($types['exercises']);
			unset($properties['owner']);
			unset($types['owner']);
			require_once('views/shared/layout.php');
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
								array('text/plain', 'text/x-fortran'),
								true
							)) {
								add_alert('Invalid file format.', Alert_Type::DANGER);
								$failed = true;
							}
						}

						if(!$failed)
						{
							require_once('importer.php');
                            //header('Content-Type: text/plain; charset=utf-8');
							$lessons = Importer::get_lessons(file_get_contents($_FILES['file']['tmp_name']));

							foreach($lessons as $lesson)
							{
								//validate...
								$lesson->set_properties(array('owner' => $_SESSION['user']->get_id()));
								$lesson->create(); //this will set the id
								foreach($lesson->get_properties()['exercises'] as $exercise) //the getter is bad... :/
								{
									$exercise->set_properties(array('lesson' => $lesson->get_id(), 'language' => 1)); //python hard coded
									$exercise->create();
								}
							}
							$success = true;
							add_alert('Successfully created!', Alert_Type::SUCCESS);
							//return redirect('lesson', 'index');
						}
					}
					else
					{
						add_alert('Please try again.', Alert_Type::DANGER);
					}
			}
			$view_to_show = 'views/lesson/create_file.php';
			require_once('views/shared/layout.php');
		}

		public function update() {
			//check if this lesson belongs to me
			//check the owner!!!!!!!

			//must set id and the rest too. id is separate.
			//for users especially, i need to be more careful.
			//this is a basic one without permissions.

			if (!isset($_GET['id']) || !lesson::is_owner($_GET['id'], $_SESSION['user']->get_id()))
			{
				return call('pages', 'error');
			}

			//if there is post data...
			//todo: i need to check if the model actually exists on post, too!!!!
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken)){
					//probably i should do that isset stuff
					$model = new $this->model_name();
						$model->set_id($_GET['id']); //i should not trust that...
						$model->set_properties($_POST);
						$model->set_properties(array('owner' => $_SESSION['user']->get_id()));
						if($model->is_valid())
						{
							$model->update();
							add_alert('Successfully updated!', Alert_Type::SUCCESS);
							return redirect('lesson', 'index');
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
				// Figure out what is happening here
				$properties = $model->get_properties();
				$types = $model::get_types();
				unset($properties['exercises']);
				unset($types['exercises']);
				unset($properties['owner']);
				unset($types['owner']);
				$view_to_show = 'views/shared/update.php';
				require_once('views/shared/layout.php');
			}
			//i need to be better about the order of things.

		}

		public function delete() {
			//check if this lesson belongs to me
			//check the owner!!!!!!!

			//must set id and the rest too. id is separate.
			//for users especially, i need to be more careful.
			//this is a basic one without permissions.

			if (!isset($_GET['id']) || !lesson::is_owner($_GET['id'], $_SESSION['user']->get_id()))
			{
				add_alert("You are not the owner to this lesson.", Alert_Type::DANGER);
				return call('pages', 'error');
			}

			//if there is post data...
			//todo: i need to check if the model actually exists on post, too!!!!
			// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// $postedToken = filter_input(INPUT_POST, 'token');
				// if(!empty($postedToken) && isTokenValid($postedToken)){
					// //probably i should do that isset stuff
					// $model = new $this->model_name();
						// $model->set_id($_GET['id']); //i should not trust that...
						// $model->set_properties($_POST);
						// $model->set_properties(array('owner' => $_SESSION['user']->get_id()));
						// if($model->is_valid())
						// {
							// $model->update();
							// add_alert('Successfully updated!', Alert_Type::SUCCESS);
							// return redirect('lesson', 'index');
						// }
						// else
						// {
							// add_alert('Please try again.', Alert_Type::DANGER);
						// }
				// }
				// else
				// {
					// add_alert('Please try again.', Alert_Type::DANGER);
				// }
			// }
			
			$model = ($this->model_name)::get($_GET['id']);
			if($model == null)
			{
				add_alert("The lesson you are trying to access does not exist.", Alert_Type::DANGER);
				return call('pages', 'error');
			}
			else
			{
				$model->delete($_GET['id']);
				return redirect($this->model_name, 'index');
			}
		}
	}
?>
