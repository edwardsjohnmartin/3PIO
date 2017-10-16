<?php

	//stuff that doesn't belong here
	function validate_date($date)
	{
		$d = DateTime::createFromFormat('m/d/Y g:i A', $date);
		return $d && $d->format('m/d/Y g:i A') === $date;
	}

	function add_alert($message, $alert_type)
	{
		$_SESSION['alerts'][] = new alert($message, $alert_type);
	}

	function has_permission($permission)
	{
		$has_permission = false;
		if(array_key_exists('permissions', $_SESSION) && $_SESSION['permissions'] != null && array_key_exists($permission->securable, $_SESSION['permissions']) && array_key_exists($permission->permission_type, $_SESSION['permissions'][$permission->securable]))
		{
			$has_permission = $_SESSION['permissions'][$permission->securable][$permission->permission_type];
		}
		return $has_permission; //probably need to check

	}

	//https://stackoverflow.com/questions/4614052/how-to-prevent-multiple-form-submission-on-multiple-clicks-in-php
	/**
	 * Creates a token usable in a form
	 * @return string
	 */
	function getToken(){
	  $token = sha1(mt_rand());
	  if(!isset($_SESSION['tokens'])){
		$_SESSION['tokens'] = array($token => 1);
	  }
	  else{
		$_SESSION['tokens'][$token] = 1;
	  }
	  return $token;
	}

	/**
	 * Check if a token is valid. Removes it from the valid tokens list
	 * @param string $token The token
	 * @return bool
	 */
	function isTokenValid($token){
	  if(!empty($_SESSION['tokens'][$token])){
		unset($_SESSION['tokens'][$token]);
		return true;
	  }
	  return false;
	}

	//end stuff that doesn't belong here


	function redirect($controller, $action) //i'm using 'return' when calling to be consistent with call but it's not necessary
	{
		header('Location: ' . '?controller=' . $controller . '&action=' . $action);
	}

	function redirect_to_index() //i'm using 'return' when calling to be consistent with call but it's not necessary
	{
		header('Location: ' . '?');
	}

	function call($controller, $action)
	{
		require_once('controllers/' . $controller . '_controller.php');
		//do with string. make it static.

		//if i want to keep plural in the routing, i could use the switch statement...
		switch($controller)
		{
			//why am i using this instead of just creating with the string? any benefits?
			//i could set the model name here... that way the controller will know...
			//i already check the array for what is valid before calling, so it should be safe?
			//i guess it could be possible for this to be called if i'm not careful?
			//but i mean i'm including it anyway.
			//which is faster? it's probably not a lot different?
			//would be a problem if i had a controller that didn't match the name.. then the model name wouldn't match either
			//it may be a good idea to set the model name here as well.
			//some people would probably call the view here. what to do?
			//i could create an actual model... but in some cases i don't actually need one, just the class. for static methods.
			//pass in the model name?? probably that is the cleanest? unless, again, if i just take it from the name...
			//no i don't want to pass it in, the controller should know its model.
			//todo: i should name controllers with underscores... more consistent.
			case 'pages':
				$controller = new PagesController();
				break;
			case 'language':
				require_once('models/language.php');
				$controller = new LanguageController();
				break;
			case 'problem':
				require_once('models/problem.php');
				$controller = new ProblemController();
				break;
			case 'section':
				require_once('models/section.php');
				$controller = new SectionController();
				break;
			case 'course':
				require_once('models/course.php');
				$controller = new CourseController();
				break;
			case 'concept':
				require_once('models/concept.php');
				$controller = new ConceptController();
				break;
			case 'project':
				require_once('models/project.php');
				$controller = new ProjectController();
				break;
			case 'lesson':
				require_once('models/lesson.php');
				$controller = new LessonController();
				break;
			case 'role':
				require_once('models/role.php');
				$controller = new RoleController();
				break;
			case 'tag':
				require_once('models/tag.php');
				$controller = new TagController();
				break;
			case 'exercise':
				require_once('models/exercise.php');
				$controller = new ExerciseController();
				break;
			case 'user':
				require_once('models/user.php');
				$controller = new UserController();
				break;
			case 'function':
				require_once('models/function.php');
				$controller = new FunctionController();
				break;
			case 'importer':
				require_once('importer.php');
				$controller = new ImporterController;
				break;
			case 'sandbox':
				$controller = new SandboxController;
				break;
			case 'exam':
				require_once('models/exam.php');
				$controller = new ExamController;
				break;
			case 'question':
				require_once('models/question.php');
				$controller = new QuestionController;
				break;
			case 'grades':
				require_once('models/grades.php');
				$controller = new GradesController;
				break;
		}
		$controller->$action();
	}

	require_once('models/permission.php');
	require_once('models/authorization_requirements.php');
	$controllers = [ //having all the permissions here is not optimal. also all but the page/index and page/error and importer/index should require login.
						'pages' => ['index'=>new Authorization_Requirements(null, []), 'error'=>new Authorization_Requirements(null, [])],
						'language' => [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::LANGUAGE, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::LANGUAGE, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::LANGUAGE, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::LANGUAGE, Permission_Type::EDIT)])
									],
						'section' => [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::SECTION, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::SECTION, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::SECTION, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::SECTION, Permission_Type::EDIT)]),
										'read_student'=>new Authorization_Requirements(true, [])
									],
						'course' =>  [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::COURSE, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::COURSE, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::COURSE, Permission_Type::CREATE)]),
										'update'=>[new Permission(Securable::COURSE, Permission_Type::EDIT)]
									],
						'concept' =>  [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::CONCEPT, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::CONCEPT, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::CONCEPT, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::CONCEPT, Permission_Type::EDIT)])
									],
						'project' =>  [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::PROJECT, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::PROJECT, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::PROJECT, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::PROJECT, Permission_Type::EDIT)]),
										'try_it'=>new Authorization_Requirements(true, []),
										'save_code'=>new Authorization_Requirements(true, []),
										'check'=>new Authorization_Requirements(true, [new Permission(Securable::PROJECT, Permission_Type::READ)])
									],
						'lesson' =>  [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::LESSON, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::LESSON, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::LESSON, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::LESSON, Permission_Type::EDIT)]),
										'create_file'=>new Authorization_Requirements(true, [new Permission(Securable::LESSON, Permission_Type::CREATE)]),
										//'read_student'=>new Authorization_Requirements(true, []),
										'read_for_concept_for_student'=>new Authorization_Requirements(true, []),
										'delete'=>new Authorization_Requirements(true, [new Permission(Securable::LESSON, Permission_Type::EDIT)])
									],
						'tag' =>  [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::TAG, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::TAG, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::TAG, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::TAG, Permission_Type::EDIT)])
									],
						'exercise' =>  [
										'index'=>new Authorization_Requirements(null, [new Permission(Securable::EXERCISE, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::EXERCISE, Permission_Type::READ)]),
										'create'=>new Authorization_Requirements(true, [new Permission(Securable::EXERCISE, Permission_Type::CREATE)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::EXERCISE, Permission_Type::EDIT)]),
										'try_it'=>new Authorization_Requirements(true, []),
										'mark_as_completed'=>new Authorization_Requirements(true, [])//,
									],
						'user' => [
										'index' => new Authorization_Requirements(true, [new Permission(Securable::USER, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::USER, Permission_Type::READ)]),
										'update'=>new Authorization_Requirements(true, [new Permission(Securable::USER, Permission_Type::READ)]),
										'delete'=>new Authorization_Requirements(true, [new Permission(Securable::USER, Permission_Type::EDIT)]),
										'log_in'=>new Authorization_Requirements(false, []),
										'log_in_partner'=>new Authorization_Requirements(true, []),
										'log_out'=>new Authorization_Requirements(true, []),
										'log_out_partner'=>new Authorization_Requirements(true, []),
										'manage_partners'=>new Authorization_Requirements(true, []),
										'create'=>new Authorization_Requirements(false, [])
									], //['index', 'read', 'create', 'update'],
						'role' => [
										'index' =>new Authorization_Requirements(null, [new Permission(Securable::ROLE, Permission_Type::LIST)]),
										'read'=>new Authorization_Requirements(true, [new Permission(Securable::ROLE, Permission_Type::READ)])
								],
						'importer' => ['index' =>new Authorization_Requirements(true, [new Permission(Securable::LESSON, Permission_Type::CREATE)])],
						'sandbox' => ['index' =>new Authorization_Requirements(null, [])],
						'exam' => ['index' =>new Authorization_Requirements(true, [new Permission(Securable::EXAM, Permission_Type::LIST)]),
								   'read'=>new Authorization_Requirements(true, [new Permission(Securable::EXAM, Permission_Type::LIST)]),
								   'update'=>new Authorization_Requirements(true, [new Permission(Securable::EXAM, Permission_Type::CREATE)]),
								   'update_times'=>new Authorization_Requirements(true, [new Permission(Securable::EXAM, Permission_Type::CREATE)]),
								   'create' =>new Authorization_Requirements(true, [new Permission(Securable::EXAM, Permission_Type::CREATE)]),
								   'create_file' =>new Authorization_Requirements(true, [new Permission(Securable::EXAM, Permission_Type::CREATE)])],
						'question' => ['index' =>new Authorization_Requirements(true, [new Permission(Securable::QUESTION, Permission_Type::LIST)]),
								       'read'=>new Authorization_Requirements(true, [new Permission(Securable::QUESTION, Permission_Type::LIST)]),
									   'update'=>new Authorization_Requirements(true, [new Permission(Securable::QUESTION, Permission_Type::CREATE)]),
								       'create' =>new Authorization_Requirements(true, [new Permission(Securable::QUESTION, Permission_Type::CREATE)]),
									   'read_for_student' =>new Authorization_Requirements(true, [new Permission(Securable::QUESTION, Permission_Type::READ)]),
									   'save_code' =>new Authorization_Requirements(true, [new Permission(Securable::QUESTION, Permission_Type::READ)])],
						'grades' => ['index' => new Authorization_Requirements(true, [new Permission(Securable::SECTION, Permission_Type::READ)]),
									 'get_section_grades' => new Authorization_Requirements(true, [new Permission(Securable::SECTION, Permission_Type::READ)]),
									 'get_exam_grade_for_student' =>new Authorization_Requirements(true, [new Permission(Securable::SECTION, Permission_Type::READ)])]
						//'function' => ['index'=>[], 'read'=>[], 'create'=>[], 'update'=>[]],
						//'role' => ['index', 'read', 'create', 'update']
						];

	if(array_key_exists($controller, $controllers))
	{
		if(array_key_exists($action, $controllers[$controller]))
		{
			$can_access = true;
			if($controllers[$controller][$action]->login_state === true)
			{
				if(!(isset($_SESSION['user']) && $_SESSION['user'] != null))
				{
					$can_access = false;
					add_alert("Please log in and try again.", Alert_Type::DANGER);
				}
			}
			else if($controllers[$controller][$action]->login_state === false)
			{
				if(isset($_SESSION['user']) && $_SESSION['user'] != null)
				{
					$can_access = false;
					add_alert("Please log out and try again.", Alert_Type::DANGER);
				}
			}

			if($can_access)
			{
				foreach($controllers[$controller][$action]->permissions as $permission)
				{
					if(!has_permission($permission))
					{
						$can_access = false;
						add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
						break;
					}
				}
			}

			if($can_access)
			{
				call($controller, $action);
			}
			else
			{
				call('pages', 'error'); //todo: i should set the status code
			}

		}
		else
		{
			call('pages', 'error');
		}
	}
	else
	{
		call('pages', 'error');
	}

?>
