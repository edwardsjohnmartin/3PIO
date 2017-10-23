<?php
	require_once('views/shared/html_helper.php');
	require_once('enums/completion_status.php');
	$section_props = $section->get_properties();

	$completed_concept_count = 0;
	$total_concept_count = count($concepts);
	if($total_concept_count > 0)
	{
		foreach($concepts as $concept)
		{
			//echo '<pre>';
			//print_r($concept);
			//echo '</pre>';
			if($concept->get_properties()['project']->status == Completion_Status::COMPLETED) $completed_concept_count++;
		}
		$section_completion_percentage = $completed_concept_count/(float)$total_concept_count * 100;
	}
	else
	{
		$section_completion_percentage = 0;
	}

	echo '<h1>' . $section_props['name'] . '</h1>';

	echo '<div class="progress">
		  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: ' . $section_completion_percentage . '%">
			<span class="sr-only">' . $section_completion_percentage . '% Complete (success)</span>
		  </div>
		</div>';

	//...this is a mess
	//todo: if they skipped a project, it will still show up as the current one :/
	echo '<div class="panel-group" id="accordion">';
	$is_current = $completed_concept_count == 0;
	$found_current = false;
	$lesson_complete = true;
	foreach($concepts as $concept)
	{
	$concept_props = $concept->get_properties();
    echo '<div class="panel ';
	if ($concept_props['project']->status == Completion_Status::COMPLETED)
	{
		echo 'panel-success';
		$completed_last = true;
	}
	elseif(!$found_current)
	{
		echo 'panel-primary';
		$found_current = true;
		$is_current = true;
		$completed_last = false;
	}
	else
	{
		echo 'panel-default';
	}
	echo '">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapseConcept' . $concept->get_id() . '">' . $concept_props['name'] . '</a>
			<!-- <a href="concept.html" class="pull-right">6/5/2017-6/9/2017</a> this date could have a link to the concept page, which shows the same info as here -->
        </h4>
      </div>
      <div id="collapseConcept' . $concept->get_id() . '" class="panel-collapse collapse';
		if($is_current) echo ' in';
		echo '">
        <div class="panel-body">';


		echo '<ul class="list-group concept-list-group">';
			if($lesson_complete && new Datetime($concept_props['open_date']) < new Datetime()) //previous lesson was complete
			{
				if(count($concept_props['lessons']) > 0)
				{
					foreach($concept_props['lessons'] as $lesson_key => $lesson_obj)
					{
						$lesson_complete = ($lesson_obj->status == Completion_Status::COMPLETED);
						if(!$lesson_complete) break;
					}

					echo '<a href="?controller=lesson&action=read_for_concept_for_student&concept_id=' . $concept->get_id() . '" class="list-group-item';
					if($lesson_complete) echo ' list-group-item-success';
					echo '">Exercises<span class="pull-right">' . $concept_props['open_date'] . '</span></a>';
				}
			}
			else
			{
				$lesson_complete = false;
				if(count($concept_props['lessons']) > 0)
				{
					echo '<a class="list-group-item disabled">Exercises<span class="pull-right">' . $concept_props['open_date'] . '</span></a>';
				}
			}


			if($lesson_complete && new Datetime($concept_props['project_open_date']) < new Datetime()) //check open date
			{
			echo '<a href="?controller=project&action=try_it&concept_id=' . $concept->get_id() . '" class="list-group-item'; //?controller=project&action=read&id=' . $concept_props['project']->key .' //this should actually be the concept id
					if($concept_props['project']->status /*== Completion_Status::COMPLETED*/) echo ' list-group-item-success';
					echo '">Project<span class="pull-right">' . $concept_props['project_open_date'] . ' to ' . $concept_props['project_due_date'] . '</span></a>';
			}
			else
			{
			echo '<a class="list-group-item disabled">Project<span class="pull-right">' . $concept_props['project_open_date'] . ' to ' . $concept_props['project_due_date'] . '</span></a>';
			}


		echo '</ul>';
	echo '</div>
      </div>
    </div>';
	$is_current = false;
	}
	echo '</div>';
	require_once('views/exam/exam_table.php');
?>

