
<!DOCTYPE html>
<!--where do i get the user and stuff to fill the layout correctly...-->

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>3PIO</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="css/site.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/moment.js"></script>
    <script src="js/bootstrap-datetimepicker.js"></script>
  </head>
  <body>
	<nav class="navbar navbar-default">
	  <div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
		    <span class="sr-only">Toggle navigation</span>
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand" href="?">3PIO</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		  <ul class="nav navbar-nav">
		    <!--<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>-->
		    

	  	<?php
		if(isset($_SESSION['user']) && $_SESSION['user'] != null)
		{


			if(isset($_SESSION['sections_student']) && $_SESSION['sections_student'] != null && count($_SESSION['sections_student']) >0)
			{

				echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Classes (Student) <span class="caret"></span></a><ul class="dropdown-menu">';
				foreach($_SESSION['sections_student'] as $kvp)
				{
					echo '<li><a href="?controller=Section&action=read_student&id=' . $kvp->key . '">' . htmlspecialchars($kvp->value) . '</a></li>';
				}
				echo '</ul></li>';
			}

            $is_ta = false;
			if(isset($_SESSION['sections_ta']) && $_SESSION['sections_ta'] != null && count($_SESSION['sections_ta']) >0)
			{
				$is_ta = true;
				echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Classes (TA) <span class="caret"></span></a><ul class="dropdown-menu">';
				foreach($_SESSION['sections_ta'] as $kvp)
				{
					echo '<li><a href="?controller=Section&action=read&id=' . $kvp->key . '">' . htmlspecialchars($kvp->value) . '</a></li>';
				}
				echo '</ul></li>';
			}

				$can_list_section = has_permission(new Permission(Securable::SECTION, Permission_Type::LIST));
				$can_list_project = has_permission(new Permission(Securable::PROJECT, Permission_Type::LIST));
				$can_list_lesson = has_permission(new Permission(Securable::LESSON, Permission_Type::LIST));
				$can_list_exercise = has_permission(new Permission(Securable::EXERCISE, Permission_Type::LIST));
				$can_list_concept = has_permission(new Permission(Securable::CONCEPT, Permission_Type::LIST));
				$can_list_exam = has_permission(new Permission(Securable::EXAM, Permission_Type::LIST));
				$can_list_question = has_permission(new Permission(Securable::QUESTION, Permission_Type::LIST));

				if ($can_list_section || $can_list_project || $can_list_lesson || $can_list_exercise || $can_list_concept || $can_list_exam || $can_list_question)
			{
				echo '<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Materials <span class="caret"></span></a>
				  <ul class="dropdown-menu">';
					if ($can_list_section) echo '<li><a href="?controller=section&action=index">Sections</a></li>';
					if ($can_list_concept) echo '<li><a href="?controller=concept&action=index">Concepts</a></li>';
					if ($can_list_project) echo '<li><a href="?controller=project&action=index">Projects</a></li>';
					if ($can_list_lesson) echo '<li><a href="?controller=lesson&action=index">Lessons</a></li>';
					if ($can_list_exercise) echo '<li><a href="?controller=exercise&action=index">Exercises</a></li>';
					if ($can_list_exam) echo '<li><a href="?controller=exam&action=index">Exams</a></li>';
					if ($can_list_question) echo '<li><a href="?controller=question&action=index">Questions</a></li>';
				  echo '</ul>
				</li>';
			}

			$can_list_user = has_permission(new Permission(Securable::USER, Permission_Type::LIST));
			$can_list_role = has_permission(new Permission(Securable::ROLE, Permission_Type::LIST));
			$can_list_course = has_permission(new Permission(Securable::COURSE, Permission_Type::LIST));
			$can_list_language = has_permission(new Permission(Securable::LANGUAGE, Permission_Type::LIST));

			if($can_list_user || $can_list_role || $can_list_course || $can_list_language || $is_ta)
			{
				echo '<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage <span class="caret"></span></a>
				  <ul class="dropdown-menu">';
					if ($can_list_user || $is_ta) echo '<li><a href="?controller=grades&action=index">Grades</a></li>';
					if ($can_list_user || $is_ta) echo '<li><a href="?controller=question&action=read_occurrences">Occurrences</a></li>';
					if ($can_list_user) echo '<li><a href="?controller=user&action=index">Users</a></li>';
					if ($can_list_role) echo '<li><a href="?controller=role&action=index">Roles</a></li>';
					if ($can_list_course) echo '<li><a href="?controller=course&action=index">Courses</a></li>';
					if ($can_list_language) echo '<li><a href="?controller=language&action=index">Languages</a></li>';
				  echo '</ul>
				</li>';
			}
			$can_create_lesson = has_permission(new Permission(Securable::LESSON, Permission_Type::CREATE));
			$can_create_exam = has_permission(new Permission(Securable::EXAM, Permission_Type::CREATE));
			if($can_create_lesson || $can_create_exam)
			{
				echo '<li><a href="?controller=importer&action=index">Importer</a></li>';
			}
		}
		echo '<li><a href="?controller=sandbox&action=index">Sandbox</a></li>';
	  	?>

		    <!-- <li><a href="#">Users</a></li>

			<li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin stuff <span class="caret"></span></a>
		      <ul class="dropdown-menu">
		        <li><a href="#">Participation Types</a></li>
		        <li><a href="#">Roles</a></li>
		      </ul>
		    </li>-->
		  </ul>

		  <ul class="nav navbar-nav navbar-right">
			<?php
			if(isset($_SESSION['user']) && $_SESSION['user'] != null)
			{
				//echo '<li><a href="#">Settings</a></li>
				echo '<li><a href="mailto:edwajohn@isu.edu,valejose@isu.edu?Subject=3PIO Website Bug" target="_top">Report a Bug</a></li>';
				echo '<li><a>' . htmlspecialchars($_SESSION['user']->get_properties()['name']) . '</a></li>';
			echo '<li class="dropdown">
		  <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Partners <span class="caret"></span></a>
		  <ul class="dropdown-menu">';
			if(isset($_SESSION['partners']) && $_SESSION['partners'] != null && count($_SESSION['partners']) > 0)
			{
				foreach($_SESSION['partners'] as $partner)
				{
					echo '<li><a>' . $partner->get_properties()['name'] . '</a></li>';
				}
			echo '<li role="separator" class="divider"></li>';
			}
			echo '<li><a href="?controller=user&action=log_in_partner">Add a partner</a></li>';
			echo '<li><a href="?controller=user&action=manage_partners">Manage partners</a></li>';
		  echo '</ul>
		</li>';

		    	echo '<li><a href="?controller=user&action=log_out">Log out</a></li>';

			}
			else
			{
				echo '<li><a href="?controller=user&action=create">Create account</a></li>
					<li><a href="?controller=user&action=log_in">Log in</a></li>';
			}

	  	?>
		  </ul>
		</div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>

	<div class="container" role="main">

		<?php
		if(isset($_SESSION['alerts']))
		{
			foreach($_SESSION['alerts'] as $alert)
			{
				$alert_type_class = '';
				if ($alert->type == Alert_Type::SUCCESS) $alert_type_class = 'alert-success';
				elseif($alert->type == Alert_Type::INFO) $alert_type_class = 'alert-info';
				elseif($alert->type == Alert_Type::WARNING) $alert_type_class = 'alert-warning';
				elseif($alert->type == Alert_Type::DANGER) $alert_type_class = 'alert-danger';
			?>
			<div class="alert <?php echo $alert_type_class; ?> alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<?php echo $alert->message; ?>
			</div>
		<?php
			//get rid of alert...
			}
		unset($_SESSION['alerts']);
		}
		unset($kvp);
		unset($can_create_exam);
		unset($can_create_lesson);
		unset($can_list_concept);
		unset($can_list_course);
		unset($can_list_exam);
		unset($can_list_exercise);
		unset($can_list_language);
		unset($can_list_lesson);
		unset($can_list_project);
		unset($can_list_question);
		unset($can_list_role);
		unset($can_list_section);
		unset($can_list_user);
		require_once($view_to_show);
		?>
	</div>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
