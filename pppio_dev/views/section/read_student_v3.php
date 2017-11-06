<?php
	require_once('views/shared/html_helper.php');
	require_once('enums/completion_status.php');

	$concept_complete_count = 0;
	$found_active_concept = false;
	$concept_arr = array();
	$now = intval(date_format(new DateTime(), 'U'));

	foreach($concepts as $concept)
	{
		$concept_props = $concept->get_properties();

		$concept_ret = array(
			'name' => $concept_props['name'],
			'id' => $concept->get_id(),
			'concept_open_date' => $concept_props['open_date'],
			'proj_open_date' => $concept_props['project_open_date'],
			'proj_due_date' => $concept_props['project_due_date'],
			'concept_open' => (intval(strtotime($concept_props['open_date'])) <= $now),
			'proj_open' => (intval(strtotime($concept_props['project_open_date'])) <= $now and $now <= intval(strtotime($concept_props['project_due_date']))),
			'proj_completed' => $concept_props['project']->status == Completion_Status::COMPLETED,
			'has_lessons' => count($concept_props['lessons']) > 0,
			'exercises_completed' => false,
			'is_active_concept' => false,
			'concept_disabled' => false
		);

		// If the current time is not past the time the concept opens, disable it
		if(!$concept_ret['concept_open'])
		{
			$concept_ret['concept_disabled'] = true;
		}
		else
		{
			//If this concept is past the currently active concept, disable it
			$concept_ret['concept_disabled'] = $found_active_concept;

			//If the concept doesn't have lessons, it doesn't have exercises, so we count it as complete to keep the percentage of concepts completed correct
			if(!$concept_ret['has_lessons'])
			{
				$concept_complete_count++;
				$concept_ret['exercises_completed'] = !$found_active_concept;
			}
			else
			{
				$lesson_complete_count = 0;

				foreach($concept_props['lessons'] as $lesson)
				{
					//Lesson is complete or the concept still needs to be completed
					if($lesson->status == Completion_Status::COMPLETED)
					{
						$lesson_complete_count++;
					}
					else if(!$found_active_concept)
					{
						$found_active_concept = true;
						$concept_ret['is_active_concept'] = true;
					}
				}

				//If all lessons are complete
				if($lesson_complete_count == count($concept_props['lessons']))
				{
					$concept_complete_count++;
					$concept_ret['exercises_completed'] = true;
				}
			}
		}
		$concept_arr[$concept_ret['id']] = $concept_ret;
	}
	//end foreach

	if(!$found_active_concept)
	{
		$concept_arr[end($concept_arr)['id']]['is_active_concept'] = true;
	}

	//Check % if at least one concept exists, don't want to divide by 0
	if(count($concepts) > 0)
	{
		$section_percent_complete = $concept_complete_count / count($concepts) * 100;
?>

<!--Title(Section Name)-->
<h1>
	<?php echo $section->get_properties()['name'];?>
</h1>

<!--Progress Bar-->
<div class="progress">
	<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $section_percent_complete;?>%">
		<span class="sr-only">
			<?php echo $section_percent_complete;?>% Complete (success)
		</span>
	</div>
</div>

<!--Concept Panels-->
<div class="panel-group" id="accordion">
	<?php foreach($concept_arr as $c_ele)
		  {
    ?>
	<!--Single Concept Panel-->
	<div class="panel
	<?php if($c_ele['is_active_concept'])
		  {
			  echo ' panel-primary';
		  }
		  else if($c_ele['concept_open'] and !$c_ele['concept_disabled'] and $c_ele['exercises_completed'])
		  {
			  echo ' panel-success';
		  }
		  else
		  {
			  echo ' panel-default';
		  }?>">
		
		<!--Panel Header-->
		<div class="panel-heading">
			<!--Collapse Panel Link(Concept Name)-->
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseConcept<?php echo $c_ele['id'] . '">' . $c_ele['name'];?></a>
			</h4>
		</div>

		<!--Panel Body-->
		<div id="collapseConcept<?php echo $c_ele['id'];?>" class="panel-collapse collapse<?php if($c_ele['is_active_concept']){echo ' in';}?>">
			<div class="panel-body">

				<!--Exercises and Project Links-->
				<ul class="list-group concept-list-group">
					<!--Exercise Link section-->
					<?php if($c_ele['has_lessons'])
						  {
							  echo '<a class="list-group-item';
							  
							  //Sets css class(controls color) of the exercise element or disabled it if required.
							  if($c_ele['concept_disabled'])
							  {
								  echo ' disabled"';
							  }
							  else if($c_ele['exercises_completed'])
							  {
								  echo ' list-group-item-success" href="?controller=lesson&action=read_for_concept_for_student&concept_id=' . $c_ele['id'] . '"';
							  }
							  else if(!$c_ele['exercises_completed'])
							  {
								  echo '" href="?controller=lesson&action=read_for_concept_for_student&concept_id=' . $c_ele['id'] . '"';
							  }

							  //Check for AM or PM and convert date to a more readable format
							  if(date('G', strtotime($c_ele['concept_open_date'])) >= 12)
							  {
								  $c_ele['concept_open_date'] = date('g:iA m-d-y', strtotime($c_ele['concept_open_date']));
							  }
							  else
							  {
								  $c_ele['concept_open_date'] = date('g:ia m-d-y', strtotime($c_ele['concept_open_date']));
							  }
							  echo '>Exercises<span class="pull-right">Opens at [' . $c_ele['concept_open_date'] . ']</span></a>';
						  }

						  //Project Link section
						  echo '<a class="list-group-item';

						  //If the proj is not open or the exercise set for this concept or a previous one isn't completed, disable the link to the project
						  if($c_ele['concept_disabled'] or !$c_ele['concept_open'] or ($c_ele['has_lessons'] and !$c_ele['exercises_completed']))
						  {
							  echo ' disabled"';
						  }
						  else
						  {
							  //Sets css class(controls color) of the project element
							  if($c_ele['proj_completed'] and !$c_ele['proj_open'])
							  {
								  echo ' list-group-item-success';
							  }
							  else if($c_ele['proj_completed'] and $c_ele['proj_open'])
							  {
								  echo ' list-group-item-info';
							  }
							  else if(!$c_ele['proj_completed'] and !$c_ele['proj_open'])
							  {
								  echo ' list-group-item-danger';
							  }
							  echo '" href="?controller=project&action=try_it&concept_id=' . $c_ele['id'] . '"';
						  }

						  //Check for AM or PM and convert dates to a more readable format
						  if(date('G', strtotime($c_ele['proj_open_date'])) >= 12)
						  {
						      $c_ele['proj_open_date'] = date('g:iA m-d-y', strtotime($c_ele['proj_open_date']));
						  }
						  else
						  {
						      $c_ele['proj_open_date'] = date('g:ia m-d-y', strtotime($c_ele['proj_open_date']));
						  }

						  //Check for AM or PM and convert date to a more readable format
						  if(date('G', strtotime($c_ele['proj_due_date'])) >= 12)
						  {
						      $c_ele['proj_due_date'] = date('g:iA m-d-y', strtotime($c_ele['proj_due_date']));
						  }
						  else
						  {
						      $c_ele['proj_due_date'] = date('g:ia m-d-y', strtotime($c_ele['proj_due_date']));
						  }
						  echo '>Project<span class="pull-right">Open from [' . $c_ele['proj_open_date'] . '] to [' . $c_ele['proj_due_date'] . ']</span></a>';
                    ?>
				</ul>
			</div>
		</div>
	</div>
    <?php }require_once('views/exam/exam_table.php');
	}
	else
	{
		echo '<h1>' . $section->get_properties()['name'] . '</h1>';
		echo '<h3>No concepts exist in this section</h3>';
	}?>
</div>
