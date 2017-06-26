<?php
	//you'd better escape everything properly jac!
	require_once('views/shared/html_helper.php');
	//print_r($section);
	$section_props = $section->get_properties();
	//print_r($concepts);
	echo '<h1>' . $section_props['name'] . '</h1>';

	echo '<div class="progress">
		  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only">0% Complete (success)</span>
		  </div>
		</div>';

	echo '<div class="panel-group" id="accordion">';
	foreach($concepts as $concept)
	{
	$concept_props = $concept->get_properties();
    echo '<div class="panel panel-primary">
      <div class="panel-heading">
        <h4 class="panel-title">
          <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">' . $concept_props['name'] . '</a>
			<!-- <a href="concept.html" class="pull-right">6/5/2017-6/9/2017</a> this date could have a link to the concept page, which shows the same info as here -->
        </h4>
      </div>
      <div id="collapse3" class="panel-collapse collapse in">
        <div class="panel-body">';
			if(count($concept_props['lessons']) > 0)
			{
			  	echo '<div class="panel panel-default">
				  <!-- Default panel contents -->
				<div class="panel-heading">Lessons</div>
			  	<ul class="list-group">';
				foreach($concept_props['lessons'] as $lesson_key => $lesson_name) //does php do this smartly? i assume they do
				{
					echo '<a href="/?controller=lesson&action=read_student&id=' . htmlspecialchars($lesson_key) . '" class="list-group-item">' . $lesson_name .'<span class="pull-right"></span></a>'; //use the due date of the project... need to display?

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
					<a href="/?controller=project&action=read&id=' . $concept_props['project']->key .'" class="list-group-item">' . htmlspecialchars($concept_props['project']->value) . '</a> <!-- put either the open date or the due date?-->
		  		</ul>
			</div>
		</div>
      </div>
    </div>';

	}
	echo '</div>';

?>

