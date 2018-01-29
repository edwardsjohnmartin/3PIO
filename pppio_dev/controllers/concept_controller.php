<?php
	require_once('controllers/base_controller.php');
	class ConceptController extends BaseController{
		public function index(){
			$models = ($this->model_name)::get_pairs_for_owner($_SESSION['user']->get_id());
			$view_to_show = 'views/shared/index.php';
			require_once('views/shared/layout.php');
		}

		public function create(){
			require_once('models/section.php');
			require_once('models/lesson.php');
			$sections = section::get_pairs_for_owner($_SESSION['user']->get_id());
			$lessons = lesson::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('section' => $sections, 'lessons' => $lessons);
			if(count($sections) > 0){
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					$postedToken = filter_input(INPUT_POST, 'token');
					if(!empty($postedToken) && isTokenValid($postedToken)){
						$model = new $this->model_name();
						$model->set_properties($_POST);
						if($model->is_valid() && array_key_exists($model->get_properties()['section'], $sections)){
							$lessons_belong_to_user = true;
							foreach($model->get_properties()['lessons'] as $lesson){
								if(!array_key_exists($lesson, $lessons)){
									$lessons_belong_to_user = false;
									break;
								}
							}
							if($lessons_belong_to_user){
								//add alerts to session or something
								//http://getbootstrap.com/components/#alerts
								//redirect header("Location: ...");
								$model->create();
								//$_SESSION['alerts'][] = 'Successfully created!';
								add_alert('Successfully created!', Alert_Type::SUCCESS);
								//session_write_close();
								return redirect($this->model_name, 'index');
							} else {
								add_alert('Please try again. 1', Alert_Type::DANGER);
							}
						} else {
							add_alert('Please try again. 2', Alert_Type::DANGER);
						}
					} else {
						add_alert('Please try again. 3', Alert_Type::DANGER);
					}
				}
				$view_to_show = 'views/' . strtolower($this->model_name) . '/create.php';
				if(!file_exists($view_to_show)){
					$view_to_show = 'views/shared/create.php';
				}

			$properties = $this->model_name::get_available_properties();
			$types = $this->model_name::get_types();
				require_once('views/shared/layout.php');
			} else {
					add_alert('Oops, you don\'t have any sections. Concepts must be added to section. Please <a href="?controller=section&action=create">create a section</a> before creating an exercise!', Alert_Type::DANGER);
					redirect('concept', 'index');
			}
		}

		public function update() { //only differences: validation + get lessons
			require_once('models/section.php');
			require_once('models/lesson.php');
			if (!isset($_GET['id']) || !concept::is_owner($_GET['id'], $_SESSION['user']->get_id())){
				return call('pages', 'error');
			}
			$sections = section::get_pairs_for_owner($_SESSION['user']->get_id());
			$lessons = lesson::get_pairs_for_owner($_SESSION['user']->get_id());
			$options = array('section' => $sections, 'lessons' => $lessons);
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken) && isTokenValid($postedToken)){
					$model = new $this->model_name();
					$model->set_id($_GET['id']); //i should not trust that...
					$model->set_properties($_POST);
					if($model->is_valid() && array_key_exists($model->get_properties()['section'], $sections)){
						$lessons_belong_to_user = true;
						foreach($model->get_properties()['lessons'] as $lesson){
							if(!array_key_exists($lesson, $lessons)){
								$lessons_belong_to_user = false;
								break;
							}
						}
						if($lessons_belong_to_user){
							$model->update();
							add_alert('Successfully updated!', Alert_Type::SUCCESS);
							return redirect($this->model_name, 'index');
						}else{
							add_alert('Please try again.', Alert_Type::DANGER);
						}
					}else{
						add_alert('Please try again.', Alert_Type::DANGER);
					}
				}else{
					add_alert('Please try again.', Alert_Type::DANGER);
				}
			}

			$model = ($this->model_name)::get($_GET['id']);
			if($model == null){
				return call('pages', 'error');
			}else{
				$view_to_show = 'views/' . strtolower($this->model_name) . '/update.php';
				if(!file_exists($view_to_show)){
					$view_to_show = 'views/shared/update.php';
				}

				$types = $model::get_types();
				$properties = $model->get_properties();
				require_once('views/shared/layout.php');
			}
		}

		public function read(){
			if (!isset($_GET['id'])){
				return call('pages', 'error');
			}else{
				$model = ($this->model_name)::get($_GET['id']);
				if($model == null){
					add_alert('The item you are trying to access doesn\'t exist.', Alert_Type::DANGER);
					return call('pages', 'error');
				}else{
					$is_owner = concept::is_owner($model->get_id(), $_SESSION['user']->get_id());
					$is_ta = concept::is_teaching_assistant($model->get_id(), $_SESSION['user']->get_id());

					//Because these return arrays, they are destroyed before the program gets to the view
					//By storing it in the session, we can make sure it gets to the view
					$_SESSION['progress'] = concept::get_progress($model->get_id());
					$_SESSION['project_completion'] = Concept::get_project_completion($model->get_id());

					$view_to_show = 'views/' . strtolower($this->model_name) . '/read.php';
					if(!file_exists($view_to_show)){
						$view_to_show = 'views/shared/read.php';
					}
					$types = $model::get_types();
					$properties = $model->get_properties();
					require_once('views/shared/layout.php');
				}
			}
		}

		//Action for the page where an admin or teacher will be able to set the completion status of exercises for a student
		//The student's user_id and the concept_id will be passed into $_GET
		public function complete_exercises(){
			require_once('models/lesson.php');

			if(!isset($_GET['concept_id'])){
				return call('pages', 'error');
			}

			if(!isset($_GET['user_id'])){
				return call('pages', 'error');
			}

			$concept = Concept::get($_GET['concept_id']);
			$student = User::get($_GET['user_id']);

			$lessons = lesson::get_all_for_concept_and_student($_GET['concept_id'], $_GET['user_id']);

			$concept_props = $concept->get_properties();
			$student_props = $student->get_properties();

			$view_to_show = 'views/concept/complete_exercises.php';
			require_once('views/shared/layout.php');
		}

		//Ajax call for completing exercises for a student based on the tile selected
		public function complete_exercises_ajax(){
			if(!isset($_POST['exercise_id']) or !isset($_POST['lesson_id']) or !isset($_POST['concept_id']) or !isset($_POST['user_id'])){
				return call('pages', 'error');
			}else{
				require_once('models/section.php');

				$post_exercise_id = intval($_POST['exercise_id']);
				$post_lesson_id = intval($_POST['lesson_id']);
				$post_concept_id = intval($_POST['concept_id']);
				$post_user_id = intval($_POST['user_id']);

				$concept = Concept::get($post_concept_id);
				$concept_props = $concept->get_properties();

				$section = Section::get($concept_props['section']->key);
				$section_props = $section->get_properties();

				$concepts = $section_props['concepts'];
				$lessons = $concept_props['lessons'];

				//Get the id of the first and last lesson in the concept
				$first_lesson_id = array_pop(array_reverse($lessons))->key;
				$last_lesson_id = end(array_keys($lessons));

				//Get the id of the first concept in the section
				$first_concept_id = array_pop(array_reverse($section_props['concepts']))->key;

				//Lesson in POST is the first lesson in the concept
				if($post_lesson_id == $first_lesson_id){
					Concept::complete_prior_exercises($post_exercise_id, $post_lesson_id, $post_concept_id, $post_user_id);
				}else{ //Lesson in POST is a middle or last lesson in the concept
					Concept::complete_prior_exercises($post_exercise_id, $post_lesson_id, $post_concept_id, $post_user_id);

					Concept::complete_prior_lessons($post_lesson_id, $post_concept_id, $post_user_id);
				}

				//If the concept in post is not the first concept in the section
				if($post_concept_id != $first_concept_id){
					foreach($concepts as $concept_id => $concept){
						//Stop looping when we get to the concept that is in post
						if($concept_id == $post_concept_id){
							break;
						}else{
							Concept::complete_entire_concept($concept_id, $post_user_id); //needs parameters passed in
						}
					}
				}
			}
		}
	}
?>
