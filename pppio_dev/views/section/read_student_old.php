<?php
	//you'd better escape everything properly jac!
	require_once('views/shared/html_helper.php');
	require_once('enums/completion_status.php');
	//print_r($section);
	$section_props = $section->get_properties();

	//print_r(count($concepts));
	$completed_concept_count = 0;
	$total_concept_count = count($concepts);
	if($total_concept_count > 0)
	{
		foreach($concepts as $concept)
		{
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

	echo '<div class="panel-group" id="accordion">';
	//this is a mess
	$is_current = $completed_concept_count == 0;
	$found_current = false;
	foreach($concepts as $concept)
	{
	$concept_props = $concept->get_properties();
    echo '<div class="panel ';
	if($found_current) //i shouldn't allow a link if already found current. this makes the assumption that they'll be completed in order
	{
		echo 'panel-default';
		$is_current = false;
	}
	elseif($is_current)
	{
		echo 'panel-primary';
		$found_current = true;
	}
	elseif ($concept_props['project']->status == Completion_Status::COMPLETED)
	{
		echo 'panel-success';
		$is_current = true;
	}
	echo '">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapseConcept' . $concept->get_id() . '">' . $concept_props['name'] . '</a>
			<!-- <a href="concept.html" class="pull-right">6/5/2017-6/9/2017</a> this date could have a link to the concept page, which shows the same info as here -->
        </h4>
      </div>
      <div id="collapseConcept' . $concept->get_id() . '" class="panel-collapse collapse';
		if($is_current && $found_current) echo ' in'; // this is unclear
		echo '">
        <div class="panel-body">';
			if(count($concept_props['lessons']) > 0)
			{
			  	echo '<div class="panel panel-default">
				  <!-- Default panel contents -->
				<div class="panel-heading">Lessons</div>
			  	<ul class="list-group">';
				foreach($concept_props['lessons'] as $lesson_key => $lesson_obj) //does php do this smartly? i assume they do
				{
					echo '<a href="/?controller=lesson&action=read_student&id=' . $lesson_key . '&concept_id=' . $concept->get_id() . '" class="list-group-item';
					if($lesson_obj->status == Completion_Status::COMPLETED) echo ' list-group-item-success';
					echo '">' . htmlspecialchars($lesson_obj->value) .'<span class="pull-right"></span></a>'; //use the due date of the project... need to display?

				}
				/*<a href="lesson.html" class="list-group-item">Logic Statements <span class="pull-right">6/7/2017 11:00 PM</span></a>
				<a href="#" class="list-group-item">If Statements <span class="pull-right">6/7/2017 11:00 PM</span></a>
				<a href="#" class="list-group-item">If-elseif Statements <span class="pull-right">6/7/2017 11:00 PM</span></a>
				<a href="#" class="list-group-item">Switch Statements <span class="pull-right">6/7/2017 11:00 PM</span></a>*/
		  	echo '</ul>
			</div>';
			}
			echo '<div class="panel panel-default">
			  <!-- Default panel contents -->
			  <div class="panel-heading">Projects</div>
			  	<ul class="list-group">
					<a href="/?controller=project&action=read&id=' . $concept_props['project']->key .'" class="list-group-item';
					if($concept_props['project']->status == Completion_Status::COMPLETED) echo ' list-group-item-success';
					echo '">' . htmlspecialchars($concept_props['project']->value) . '</a> <!-- put either the open date or the due date?-->
		  		</ul>
			</div>
		</div>
      </div>
    </div>';

	}
	echo '</div>';

?>

