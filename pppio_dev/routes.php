<?php
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
			case 'participation_type':
				require_once('models/participation_type.php');
				$controller = new Participation_Type_Controller();
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
			case 'completion_status':
				require_once('models/completion_status.php');
				$controller = new Completion_Status_Controller();
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
		}

		$controller->{ $action }(); //i would guess that these brackets are unnecessary/optional
	}

	$controllers = array(
						'pages' => ['home', 'error'],
						'language' => ['index', 'read', 'create', 'update'],
						//'problem' => ['index', 'read', 'create', 'update', 'try_it'],
						'section' => ['index', 'read', 'create', 'update', 'read_student'],
						'participation_type' => ['index', 'read', 'create', 'update'],
						'course' => ['index', 'read', 'create', 'update'],
						'concept' => ['index', 'read', 'create', 'update'],
						'project' => ['index', 'read', 'create', 'update'],
						'lesson' => ['index', 'read', 'create', 'update', 'read_student'],
						'role' => ['index', 'read', 'create', 'update'],
						'tag' => ['index', 'read', 'create', 'update'],
						'completion_status' => ['index', 'read', 'create', 'update'],
						'exercise' => ['index', 'read', 'create', 'update', 'try_it'],
						'user' => ['index', 'read', 'create', 'update'],
						'function' => ['index', 'read', 'create', 'update']
						);

	if(array_key_exists($controller, $controllers))
	{
		if(in_array($action, $controllers[$controller]))
		{
			call($controller, $action);
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
