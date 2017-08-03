<!-- i need to know the name of the model... there isn't one created. how should i pass it? an empty one? for now i just put $model_name-->

<?php
	require_once('views/shared/html_helper.php');
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	if($success)
	{
		echo '<h2>Lessons and exercises created:</h2>';
		//these exercises don't have names, because you can't add name by file
		foreach($lessons as $lesson)
		{
			$lesson_props = $lesson->get_properties();
			echo '<div class="panel panel-default">';
			echo '<div class="panel-heading"><a href="/?controller=lesson&action=read&id=' . $lesson->get_id() .'" target="_blank">' . htmlspecialchars($lesson_props['name']) . '</a></div>';
			//validate...
			foreach($lesson_props['exercises'] as $exercise) //the getter is bad... :/
			{
				echo '<a href="/?controller=exercise&action=read&id=' . $exercise->get_id() .'" class="list-group-item" target="_blank">' . htmlspecialchars($exercise->get_properties()['description']) . '</a>';
			}
			echo '</div>';
		}
		echo '<a href="/?controller=' . $this->model_name . '&action=index" class="btn btn-primary">Return to index</a>';
	}
	else
	{
		echo $this->model_name;
		$properties = array('file' => null);
		$types = array('file' => TYPE::FILE);
		echo HtmlHelper::form($types, $properties);
	}
?>



