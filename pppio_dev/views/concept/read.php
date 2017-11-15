<?php
	//This view is used for seeing the information about a concept
	//If the user is a ta or the owner then they will see additional information
	//including a list of students, each students progress for each lesson in the concept,
	//and a link to the students project if the student has saved code on it.

	require_once('views/shared/html_helper.php');
	echo '<h2>' . $this->model_name . '</h2>';
	echo HtmlHelper::view($types, $properties);

	//Get the data out of the session and remove it from the session
	if(isset($_SESSION['progress']))
	{
		$progress = $_SESSION['progress'];
		unset($_SESSION['progress']);
	}

	if(isset($_SESSION['project_completion']))
	{
		$project_completion = $_SESSION['project_completion'];
		unset($_SESSION['project_completion']);
	}

	//Nothing below will be seen for students
	if($is_ta || $is_owner)
		echo '<div><a class="btn btn-primary" href="?controller=lesson&action=read_for_concept_for_student&concept_id='.$model->get_id().'">Preview</a></div>';
	{
?>

<label>Progress</label>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Students</th>
				<?php
					//Create a column header for each lesson in the concept
					foreach ($properties['lessons'] as $lesson)
					{
						echo '<th><a href="?controller=lesson&action=read&id=' . $lesson->key . '">' . $lesson->value . '</a></th>';
					}

					//Create a column header for the project in the concept
					echo '<th><a href="?controller=project&action=read&id=' . $properties['project']->key . '">' . $properties['project']->value . '</a></th>';
                ?>
			</tr>
		</thead>
		<tbody>
			<?php
				//Each element of $project_completion will be for a unique student in the section the concept belongs to
				foreach ($project_completion as $student_id => $students_project_completion)
				{
					echo '<tr>';
					echo '<td>' . $students_project_completion['user_name'] . '</td>';
					if(count($progress) > 0)
					{
						//Each element of $progress[$student_id]['lesson_completion'] will be a lesson in the concept
						foreach ($progress[$student_id]['lesson_completion'] as $lesson_completion)
						{
							echo '<td';
							if($lesson_completion->value == 1)
							{
								echo ' class="success">';
							}
							else
							{
								echo ' class="danger">';
							}
							//$lesson_completion->value is the amount of the lesson completed in the range of 0-1. Multiply by 100 to get it as a percentage
							echo number_format((float)$lesson_completion->value, 4, '.', '') * 100 . '%';
							echo '</td>';
						}
					}

					if($students_project_completion['project_completed'])
					{
						echo '<td class="success"><span class="glyphicon glyphicon-star" aria-hidden="true"></span><a href="?controller=project&action=check&concept_id=' . $model->get_id() . '&user_id=' . $students_project_completion['user_id'] . '">View code</a></td>';
					}
					else
					{
						echo '<td class="danger"><span class="glyphicon glyphicon-star-empty" aria-hidden="true"></td>';
					}
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
</div>
<?php 
	}
?>
