<?php
	//This is the main page the student will use to navigate through concepts in the section. 
	//A concept will have links to the exercises(if any exist) and it's project.
	require_once('views/shared/html_helper.php');
	require_once('enums/completion_status.php');

	$concept_complete_count = 0;
	$found_active_concept = false;

	$concept_arr = array();

	foreach($concepts as $concept)
	{
		$concept_props = $concept->get_properties();

		$concept_ret = array(
			'name' => $concept_props['name'],
			'id' => $concept->get_id(),
			'open_date' => $concept_props['open_date'],
			'proj_open_date' => $concept_props['project_open_date'],
			'proj_due_date' => $concept_props['project_due_date'],
			'proj_completed' => $concept_props['project']->status == Completion_Status::COMPLETED,
			'has_exercises' => false,
			'completed' => false,
			'is_active' => false,
			'disabled' => false
		);

		$open_time = strtotime($concept_props["open_date"]);
		$now = intval(date_format(new DateTime(), 'U'));

		if($now > $open_time)
		{
			$num_of_lessons = count($concept_props['lessons']);

			if(isset($concept_props['lessons']) and $num_of_lessons > 0)
			{
				$concept_ret['has_exercises'] = true;
				$lesson_complete_count = 0;

				if($found_active_concept)
				{
					$concept_ret['disabled'] = true;
				}

				foreach($concept_props['lessons'] as $lesson)
				{
					if($lesson->status == Completion_Status::COMPLETED)
					{
						$lesson_complete_count++;
					}
					else
					{
						if(!$found_active_concept)
						{
							$found_active_concept = true;
							$concept_ret['is_active'] = true;
						}
					}
				}

				if($lesson_complete_count == $num_of_lessons)
				{
					$concept_complete_count++;
					$concept_ret['completed'] = true;
				}
			}
			else
			{
				$concept_complete_count++;
			}
		}
		else
		{
			$concept_ret['disabled'] = true;

			if(isset($concept_props['lessons']) and count($concept_props['lessons'] > 0))
			{
				$concept_ret['has_exercises'] = true;
			}
		}
		$concept_arr[$concept_ret['id']] = $concept_ret;
	}

	if(count($concepts) > 0)
	{
		$section_percent_complete = $concept_complete_count / count($concepts) * 100;
	}
?>

<!--Title(Section Name)-->
<h1>
	<?php echo $section->get_properties()['name'];?>
</h1>

<!--Progress Bar-->
<div class="progress">
	<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $section_percent_complete;?>%">
		<span class="sr-only"><?php echo $section_percent_complete;?>% Complete (success)</span>
	</div>
</div>

<!--Concept Panels-->
<div class="panel-group" id="accordion">
	<?php foreach($concept_arr as $c_ele)
		  {
	?>
	<!--Single Concept Panel-->
	<div class="panel
	<?php if($c_ele['is_active']){echo ' panel-primary';}else if($c_ele['completed'] or (!$c_ele['has_exercises'] and $c_ele['proj_completed'])){echo ' panel-success';}else{echo ' panel-default';}?>">
		
		<!--Panel Header-->
		<div class="panel-heading">
			<!--Collapse Panel Link(Concept Name)-->
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseConcept<?php echo $c_ele['id'] . '">' . $c_ele['name'];?></a>
			</h4>
		</div>

		<!--Panel Body-->
		<div id="collapseConcept<?php echo $c_ele['id'];?>" class="panel-collapse collapse<?php if($c_ele['is_active']){echo ' in';}?>">
			<div class="panel-body">

				<!--Exercises and Project Links-->
				<ul class="list-group concept-list-group">
					<!--Exercise Link section-->
					<?php if($c_ele['has_exercises'])
						  {
							  echo '<a class="list-group-item';
							  
							  //Sets css class(controls color) of the exercise element or disabled it if required.
							  if($c_ele['disabled'])
							  {
								  echo ' disabled"';
							  }
							  else if($c_ele['completed'] or (!$c_ele['has_exercises'] and $c_ele['proj_completed']))
							  {
								  echo ' list-group-item-success" href="?controller=lesson&action=read_for_concept_for_student&concept_id=' . $c_ele['id'] . '"';
							  }
							  else if(!$c_ele['completed'] and !$c_ele['disabled'])
							  {
								  echo '" href="?controller=lesson&action=read_for_concept_for_student&concept_id=' . $c_ele['id'] . '"';
							  }

							  //Check for AM or PM and convert date to a more readable format
							  if(date('G', strtotime($c_ele['open_date'])) >= 12)
							  {
								  $c_ele['open_date'] = date('g:ia m-d-y', strtotime($c_ele['open_date']));
							  }
							  else
							  {
								  $c_ele['open_date'] = date('g:ia m-d-y', strtotime($c_ele['open_date']));
							  }
							  echo '>Exercises<span class="pull-right">Opens at [' . $c_ele['open_date'] . ']</span></a>';
						  }

						  //Project Link section
						  echo '<a class="list-group-item';

						  //If the proj is not open or the exercise set for this concept or a previous one isn't completed, disable the link to the project
						  if($c_ele['disabled'] or !$c_ele['completed'] or $c_ele['open_date'] > $now)
						  {
							  echo ' disabled"';
						  }
						  else
						  {
							  //Sets css class(controls color) of the project element
							  if($c_ele['proj_completed'] and intval(strtotime($c_ele['proj_due_date'])) < $now)
							  {
								  echo ' list-group-item-success';
							  }
							  else if($c_ele['proj_completed'] and intval(strtotime($c_ele['proj_due_date'])) > $now)
							  {
								  echo ' list-group-item-info';
							  }
							  else if(!$c_ele['proj_completed'] and intval(strtotime($c_ele['proj_due_date'])) < $now)
							  {
								  echo ' list-group-item-danger';
							  }
							  echo '" href="?controller=project&action=try_it&concept_id=' . $c_ele['id'] . '"';
						  }

						  //Check for AM or PM and convert dates to a more readable format
						  if(date('G', strtotime($c_ele['proj_open_date'])) >= 12)
						  {
						      $c_ele['proj_open_date'] = date('g:ia m-d-y', strtotime($c_ele['proj_open_date']));
						  }
						  else
						  {
						      $c_ele['proj_open_date'] = date('g:ia m-d-y', strtotime($c_ele['proj_open_date']));
						  }

						  //Check for AM or PM and convert date to a more readable format
						  if(date('G', strtotime($c_ele['proj_due_date'])) >= 12)
						  {
						      $c_ele['proj_due_date'] = date('g:ia m-d-y', strtotime($c_ele['proj_due_date']));
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
    <?php }?>
</div>
