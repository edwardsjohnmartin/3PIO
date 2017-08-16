
<?php
	require_once('views/shared/html_helper.php'); 
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo '<h2>' . $this->model_name . '</h2>';
	echo HtmlHelper::view($types, $properties);

//this check and get should go in the controller...
if(concept::is_owner($model->get_id(), $_SESSION['user']->get_id()))
{
	$progress = concept::get_progress($model->get_id());
	if(count($progress) > 0)
	{
		$current_date = new DateTime();
		$project_due_date = new DateTime($properties['project_due_date']);
		$project_open_date = new DateTime($properties['project_open_date']);
		echo '<label>Progress</label>';
		echo '<table class="table table-striped table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<th>Student</th>';
		foreach($progress[0]['lesson_completion'] as $lesson_completion)
		{
			echo '<th><a href="/?controller=lesson&action=read&id=' . $lesson_completion->key. '">' . htmlspecialchars($properties['lessons'][$lesson_completion->key]->value) . '</a></th>';
		}
		echo '<th><a href="/?controller=project&action=read&id=' . $properties['project']->key . '">Project</a></th>';
		echo '</tr>';

		echo '</thead>';
		echo '<tbody>';

		foreach($progress as $student_progress)
		{
			echo '<tr>';
			echo '<th><a href="/?controller=user&action=read&id=' . $student_progress['user_id'] . '">' . htmlspecialchars($student_progress['user_name']) . '</a></th>';
			foreach($student_progress['lesson_completion'] as $lesson_completion)
			{
				echo '<td';
				if($lesson_completion->value == 1) echo ' class="success"';
				else if($current_date > $project_open_date) echo ' class="warning"';
				echo '>' . number_format((float)$lesson_completion->value, 4, '.', '') * 100 . '%</td>';
			}
			if($student_progress['project_completed'])
			{
				echo '<td class="success"><span class="glyphicon glyphicon-star" aria-hidden="true"></span> <a href="/?controller=project&action=check&concept_id=' . $model->get_id() . '&user_id=' . $student_progress['user_id'] . '">View code</a></td>';
			}
			else
			{
				echo '<td ';
				if($current_date > $project_due_date) echo ' class="danger"';
				echo '><span class="glyphicon glyphicon-star-empty" aria-hidden="true"></td>';
			}
		}
		echo '</tbody>';
		echo '</table>';

	}

	if(has_permission(new Permission(Securable::CONCEPT, Permission_Type::EDIT)))
	{
		echo '<a href="/?controller=' . $this->model_name . '&action=update&id=' . $model->get_id() . '" class="btn btn-primary">Update</a><br>';
	}
}
?>

