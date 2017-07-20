
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
    <link href="css/site.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
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
		  <a class="navbar-brand" href="/">3PIO</a>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		  <ul class="nav navbar-nav">
		    <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
		    <li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Classes <span class="caret"></span></a>
		      <ul class="dropdown-menu">
		        <li><a href="/?controller=Section&action=read_student&id=2">CS1181-01</a></li>
		      </ul>
		    </li>
		    <li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Materials <span class="caret"></span></a>
		      <ul class="dropdown-menu">
				<li><a href="/?controller=project&action=index">Projects</a></li>
				<li><a href="/?controller=lesson&action=index">Lessons</a></li>
				<li><a href="/?controller=exercise&action=index">Exercises</a></li>
		      </ul>
			</li>

		    <li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manage <span class="caret"></span></a>
		      <ul class="dropdown-menu">
				<li><a href="/?controller=course&action=index">Courses</a></li>
				<li><a href="/?controller=section&action=index">Sections</a></li>
				<li><a href="/?controller=concept&action=index">Concepts</a></li>
				<li><a href="/?controller=language&action=index">Languages</a></li>
		      </ul>
			</li>



		    <li><a href="#">Users</a></li>

			<li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin stuff <span class="caret"></span></a>
		      <ul class="dropdown-menu">
		        <li><a href="#">Participation Types</a></li>
		        <li><a href="#">Roles</a></li>
		      </ul>
		    </li>
		  </ul>

		  <ul class="nav navbar-nav navbar-right">
		    <li><a href="#">Settings</a></li>
		    <li><a href="#">User</a></li>
		    <li><a href="#">Log out</a></li>
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
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php echo $alert; ?>
		</div>
		<?php
		//get rid of alert...
		unset($_SESSION['alerts']);
		}
		}
		?>
		<?php require_once('routes.php'); ?>
	</div>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
