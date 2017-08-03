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
		header('Location: ' . '/?controller=' . $controller . '&action=' . $action);
	}

	function redirect_to_index() //i'm using 'return' when calling to be consistent with call but it's not necessary
	{
		header('Location: ' . '/');
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
		}
		$controller->$action();
	}

	require_once('models/permission.php');
	$controllers = [ //having all the permissions here is not optimal. also all but the page/index and page/error and importer/index should require login.
						'pages' => ['index'=>[], 'error'=>[]],
						'language' => [
										'index'=>[new Permission(Securable::LANGUAGE, Permission_Type::READ)],
										'read'=>[new Permission(Securable::LANGUAGE, Permission_Type::READ)],
										'create'=>[new Permission(Securable::LANGUAGE, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::LANGUAGE, Permission_Type::EDIT)]
									],
						'section' => [
										'index'=>[new Permission(Securable::SECTION, Permission_Type::READ)],
										'read'=>[new Permission(Securable::SECTION, Permission_Type::READ)],
										'create'=>[new Permission(Securable::SECTION, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::SECTION, Permission_Type::EDIT)],
										'read_student'=>[]
									],
						'course' =>  [
										'index'=>[new Permission(Securable::COURSE, Permission_Type::READ)],
										'read'=>[new Permission(Securable::COURSE, Permission_Type::READ)],
										'create'=>[new Permission(Securable::COURSE, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::COURSE, Permission_Type::EDIT)]
									],
						'concept' =>  [
										'index'=>[new Permission(Securable::CONCEPT, Permission_Type::READ)],
										'read'=>[new Permission(Securable::CONCEPT, Permission_Type::READ)],
										'create'=>[new Permission(Securable::CONCEPT, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::CONCEPT, Permission_Type::EDIT)]
									],
						'project' =>  [
										'index'=>[new Permission(Securable::PROJECT, Permission_Type::READ)],
										'read'=>[new Permission(Securable::PROJECT, Permission_Type::READ)],
										'create'=>[new Permission(Securable::PROJECT, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::PROJECT, Permission_Type::EDIT)]
									],
						'lesson' =>  [
										'index'=>[new Permission(Securable::LESSON, Permission_Type::READ)],
										'read'=>[new Permission(Securable::LESSON, Permission_Type::READ)],
										'create'=>[new Permission(Securable::LESSON, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::LESSON, Permission_Type::EDIT)],
										'create_file'=>[new Permission(Securable::LESSON, Permission_Type::CREATE)],
										'read_student'=>[]
									],
						'tag' =>  [
										'index'=>[new Permission(Securable::TAG, Permission_Type::READ)],
										'read'=>[new Permission(Securable::TAG, Permission_Type::READ)],
										'create'=>[new Permission(Securable::TAG, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::TAG, Permission_Type::EDIT)]
									],
						'exercise' =>  [
										'index'=>[new Permission(Securable::EXERCISE, Permission_Type::READ)],
										'read'=>[new Permission(Securable::EXERCISE, Permission_Type::READ)],
										'create'=>[new Permission(Securable::EXERCISE, Permission_Type::CREATE)],
										'update'=>[new Permission(Securable::EXERCISE, Permission_Type::EDIT)],
										'try_it'=>[],
										'mark_as_completed'=>[],
									],
						'user' => ['log_in'=>[], 'log_out'=>[], 'create'=>[]], //['index', 'read', 'create', 'update'],
						'importer' => ['index' =>[]]
						//'function' => ['index'=>[], 'read'=>[], 'create'=>[], 'update'=>[]],
						//'role' => ['index', 'read', 'create', 'update']
						];

	if(array_key_exists($controller, $controllers))
	{
		if(array_key_exists($action, $controllers[$controller]))
		{
			$can_access = true;
			foreach($controllers[$controller][$action] as $permission)
			{
				if(!has_permission($permission))
				{
					$can_access = false;
					break;
				}
			}

			if($can_access)
			{
				call($controller, $action);
			}
			else
			{
				add_alert("Sorry, you don't have permission to access this page.", Alert_Type::DANGER);
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
