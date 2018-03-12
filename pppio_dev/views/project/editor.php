<?php
$project_props = $project->get_properties();

echo '<link rel="stylesheet" href="css/editor.css">';
require_once('views/shared/CodeMirror.php');
require_once('views/shared/Skulpt.php');
?>

<div class="col-xs-12 height-100 flex-columns">
    <div class="row no-shrink">
        <div class="col-xs-9">
            <h3><?php echo $project_props['name'];?></h3>
            <h4 class="panel-title"><a data-toggle="collapse" data-target="#description" href="#prompt">Description</a></h4>
            <div id="description" class="collapse in">
                <p id="prompt"><?php echo $project_props['description'];?></p>
			</div>
        </div>
		<?php
		//Create a div for the survey buttons if either survey exists
        if($pre_survey or $post_survey){
			echo '<div class="col-xs-3">';
			echo '<div class="row height-100">';

			if($pre_survey and !$post_survey){
				if(is_null($pre_survey['date_completed'])){
					echo '<a id="btn_pre_survey" class="btn btn-default" role="button" href="?controller=survey&action=do_survey&survey_id=' . $pre_survey['assigned_survey_id'] . '">Pre-Project Survey</a>';
				} else{
					echo '<a id="btn_pre_survey" class="btn btn-default" role="button" disabled="disabled">Pre-Project Survey Completed</a>';
				}
			} else if(!$pre_survey and $post_survey){
				if(is_null($post_survey['date_completed'])){
					echo '<a id="btn_post_survey" class="btn btn-default" role="button" href="?controller=survey&action=do_survey&survey_id=' . $post_survey['assigned_survey_id'] . '">Post-Project Survey</a>';
				} else{
					echo '<a id="btn_post_survey" class="btn btn-default" role="button" disabled="disabled">Post-Project Survey Completed</a>';
				}
			} else if($pre_survey and $post_survey){
				if(is_null($pre_survey['date_completed'])){
					echo '<a id="btn_pre_survey" class="btn btn-default" role="button" href="?controller=survey&action=do_survey&survey_id=' . $pre_survey['assigned_survey_id'] . '">Pre-Project Survey</a>';
					echo '<a id="btn_post_survey" class="btn btn-default" role="button" disabled="disabled">Complete Pre-Project Survey First</a>';
				} else if(!is_null($pre_survey['date_completed']) and is_null($post_survey['date_completed'])){
					echo '<a id="btn_pre_survey" class="btn btn-default" role="button" disabled="disabled">Pre-Project Survey Completed</a>';
					echo '<a id="btn_post_survey" class="btn btn-default" role="button" href="?controller=survey&action=do_survey&survey_id=' . $post_survey['assigned_survey_id'] . '">Post-Project Survey</a>';
				} else if(!is_null($pre_survey['date_completed']) and !is_null($post_survey['date_completed'])){
					echo '<a id="btn_pre_survey" class="btn btn-default" role="button" disabled="disabled">Pre-Project Survey Completed</a>';
					echo '<a id="btn_post_survey" class="btn btn-default" role="button" disabled="disabled">Post-Project Survey Completed</a>';
				}
			}

			echo '</div>';
			echo '</div>';
		}
		?>
    </div>
	<div class="row no-shrink navbar-default navbar-form navbar-left">
		<button type="button" class="btn btn-default" id="runButton">
			<span class="glyphicon glyphicon-play" aria-hidden="true"></span>
			<span class="sr-only">Run</span>
		</button>
	</div>
	<div class="row no-shrink">
		<div class="col-xs-12 pad-0">
			<div id="codeAlerts"></div>
		</div>
	</div>
	<div class="row overflow-hidden height-100">
		<div class="col-xs-6 height-100 overflow-hidden pad-0">
			<textarea id="code" name="code"><?php 
			if($code != null){
				echo $code;
			}
			else{
				echo $project_props['starter_code'];
			}?></textarea>
		</div>
		<div class="col-xs-6 height-100">
			<div id="mycanvas" class="graphicalOutput"></div>
            	<div class="textOutput">
      				<pre id="output"></pre>
            	</div>
			</div>
		</div>
	</div>
</div>

<?php
echo '<script>var project_id = ' . $project->get_id() . ';</script>';
echo '<script>var concept_id = ' . $concept->get_id() . '; var readonly = ' . ($readonly ? 'true' : 'false') . ';</script>';
echo '<script src="js/project_editor.js"></script>';

if(array_key_exists($concept->get_properties()['section']->key, $_SESSION['sections_is_study_participant'])){
	echo '<script src="js/sessions_handler.js"></script>';
}
?>
