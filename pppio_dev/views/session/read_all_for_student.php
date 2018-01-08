<?php
//Creates a table of all sessions belonging to a single student
//TODO: Clean up this mess so it isn't repeating the same thing over and over

$total_mouse_clicks = 0;
$total_key_presses = 0;
$total_times_ran = 0;
$total_session_length = 0;
$total_error_count = 0;

$grand_total_mouse_clicks = 0;
$grand_total_key_presses = 0;
$grand_total_times_ran = 0;
$grand_total_session_length = 0;
$grand_total_error_count = 0;

//Used to iterate over all the properties and get the information out of the sessions array
$keys = array(
	"securable_id",
	"activity_id",
	"start_time",
	"end_time",
	"session_length",
	"mouse_clicks",
	"key_presses",
	"times_ran",
	"error_count"
);

if(isset($name) and $name != null)
{
	echo '<h2>' . $name . '</h2>';
}

if(isset($exercise_sessions) and count($exercise_sessions) > 0)
{
	require_once("models/exercise.php");
	echo '<h3>Exercises</h3>';
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th title="Name of the activity. Defaults to <activity_type> <activity_id> if no name exists.">Activity</th>
				<th title="The time on the server when the session started.">Session Start</th>
				<th title="The time on the server when the session closed.">Session End</th>
				<th title="The amount of time the session was open for.">Session Length</th>
				<th title="The amount of any mouse button clicks during the session.">Mouse Clicks</th>
				<th title="The amount of any key presses during the session.">Key Presses</th>
				<th title="The amount of times the code was ran during the session.">Times Ran</th>
				<th title="The amount of times ran where an error existed in the code.">Error Count</th>
			</tr>
		</thead>
		<tbody>
			<?php
	foreach($exercise_sessions as $session)
	{
		echo "<tr>";
		foreach($keys as $session_key)
		{
			if($session_key == "mouse_clicks")
			{
				$total_mouse_clicks += $session->get_prop($session_key);
			}
			else if($session_key == "key_presses")
			{
				$total_key_presses += $session->get_prop($session_key);
			}
			else if($session_key == "times_ran")
			{
				$total_times_ran += $session->get_prop($session_key);
			}
			else if($session_key == "session_length")
			{
				$total_session_length += $session->get_prop("elapsed");
			}
			else if($session_key == "error_count")
			{
				if($session->get_prop("error_count") > 0)
				{
					$total_error_count += $session->get_prop("error_count");
				}
			}


			if($session_key == 'securable_id')
			{
				//echo Securable::get_string_from_id($session->get_prop($session_key));
			}
			else if($session_key == 'activity_id')
			{
				echo "<td>";
				$activity_id = $session->get_prop($session_key);
				$exercise_name = Exercise::get($activity_id)->get_properties()['name'];
				if($exercise_name == "" or $exercise_name == null)
				{
					$exercise_name = "Exercise " . $activity_id;
				}
				echo '<a href="?controller=exercise&action=read&id=' . $activity_id . '">' . $exercise_name . '</a>';
				echo "</td>";
			}
			else
			{
				echo "<td>";
				echo $session->get_prop($session_key);
				echo "</td>";
			}
		}
		echo "</tr>";
	}
			?>
			<tr>
				<td></td>
				<td></td>
				<td>Total</td>
				<td>
					<?php echo Session::get_length($total_session_length);?>
				</td>
				<td>
					<?php echo $total_mouse_clicks; ?>
				</td>
				<td>
					<?php echo $total_key_presses; ?>
				</td>
				<td>
					<?php echo $total_times_ran; ?>
				</td>
				<td>
					<?php echo $total_error_count; ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Average</td>
				<?php
	if (count($exercise_sessions) > 0)
	{
		echo '<td>' .  Session::get_length(round($total_session_length / count($exercise_sessions))) . '</td>';
		echo '<td>' .  round($total_mouse_clicks / count($exercise_sessions)) . '</td>';
		echo '<td>' .  round($total_key_presses / count($exercise_sessions)) . '</td>';
		echo '<td>' .  round($total_times_ran / count($exercise_sessions)) . '</td>';
		echo '<td>' .  round($total_error_count / count($exercise_sessions)) . '</td>';
	}
	else //Prevents division by 0 error. There might be a better way of doing this.
	{
		echo '<td>00:00:00</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
	}
                ?>
			</tr>
		</tbody>
	</table>
</div>
<?php
	$grand_total_mouse_clicks += $total_mouse_clicks;
	$grand_total_key_presses += $total_key_presses;
	$grand_total_times_ran += $total_times_ran;
	$grand_total_session_length += $total_session_length;
	$grand_total_error_count += $total_error_count;

	$total_mouse_clicks = 0;
	$total_key_presses = 0;
	$total_times_ran = 0;
	$total_session_length = 0;
	$total_error_count = 0;
}
if(isset($project_sessions) and count($project_sessions) > 0)
{
	require_once("models/project.php");
	echo '<h3>Projects</h3>';
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th title="Name of the activity. Defaults to <activity_type> <activity_id> if no name exists.">Activity</th>
				<th title="The time on the server when the session started.">Session Start</th>
				<th title="The time on the server when the session closed.">Session End</th>
				<th title="The amount of time the session was open for.">Session Length</th>
				<th title="The amount of any mouse button clicks during the session.">Mouse Clicks</th>
				<th title="The amount of any key presses during the session.">Key Presses</th>
				<th title="The amount of times the code was ran during the session.">Times Ran</th>
				<th title="The amount of times ran where an error existed in the code.">Error Count</th>
			</tr>
		</thead>
		<tbody>
			<?php
	foreach($project_sessions as $session)
	{
		echo "<tr>";
		foreach($keys as $session_key)
		{
			if($session_key == "mouse_clicks")
			{
				$total_mouse_clicks += $session->get_prop($session_key);
			}
			else if($session_key == "key_presses")
			{
				$total_key_presses += $session->get_prop($session_key);
			}
			else if($session_key == "times_ran")
			{
				$total_times_ran += $session->get_prop($session_key);
			}
			else if($session_key == "session_length")
			{
				$total_session_length += $session->get_prop("elapsed");
			}
			else if($session_key == "error_count")
			{
				if($session->get_prop("error_count") > 0)
				{
					$total_error_count += $session->get_prop("error_count");
				}
			}

			if($session_key == 'securable_id')
			{
				//echo Securable::get_string_from_id($session->get_prop($session_key));
			}
			else if($session_key == 'activity_id')
			{
				echo "<td>";
				$activity_id = $session->get_prop($session_key);
				$project_name = Project::get($activity_id)->get_properties()['name'];
				if($project_name == "" or $project_name == null)
				{
					$project_name = "Project " . $activity_id;
				}
				echo '<a href="?controller=project&action=read&id=' . $activity_id . '">' . $project_name . '</a>';
				echo "</td>";
			}
			else
			{
				echo "<td>";
				echo $session->get_prop($session_key);
				echo "</td>";
			}
		}
		echo "</tr>";
	}
			?>
			<tr>
				<td></td>
				<td></td>
				<td>Total</td>
				<td>
					<?php echo Session::get_length($total_session_length);?>
				</td>
				<td>
					<?php echo $total_mouse_clicks; ?>
				</td>
				<td>
					<?php echo $total_key_presses; ?>
				</td>
				<td>
					<?php echo $total_times_ran; ?>
				</td>
				<td>
					<?php echo $total_error_count; ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Average</td>
				<?php
	if (count($project_sessions) > 0)
	{
		echo '<td>' .  Session::get_length(round($total_session_length / count($project_sessions))) . '</td>';
		echo '<td>' .  round($total_mouse_clicks / count($project_sessions)) . '</td>';
		echo '<td>' .  round($total_key_presses / count($project_sessions)) . '</td>';
		echo '<td>' .  round($total_times_ran / count($project_sessions)) . '</td>';
		echo '<td>' .  round($total_error_count / count($project_sessions)) . '</td>';
	}
	else //Prevents division by 0 error. There might be a better way of doing this.
	{
		echo '<td>00:00:00</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
	}
                ?>
			</tr>
		</tbody>
	</table>
</div>
<?php
	$grand_total_mouse_clicks += $total_mouse_clicks;
	$grand_total_key_presses += $total_key_presses;
	$grand_total_times_ran += $total_times_ran;
	$grand_total_session_length += $total_session_length;
	$grand_total_error_count += $total_error_count;

	$total_mouse_clicks = 0;
	$total_key_presses = 0;
	$total_times_ran = 0;
	$total_session_length = 0;
	$total_error_count = 0;
}
if(isset($question_sessions) and count($question_sessions) > 0)
{
	require_once("models/question.php");
	echo '<h3>Questions</h3>';
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th title="Name of the activity. Defaults to <activity_type> <activity_id> if no name exists.">Activity</th>
				<th title="The time on the server when the session started.">Session Start</th>
				<th title="The time on the server when the session closed.">Session End</th>
				<th title="The amount of time the session was open for.">Session Length</th>
				<th title="The amount of any mouse button clicks during the session.">Mouse Clicks</th>
				<th title="The amount of any key presses during the session.">Key Presses</th>
				<th title="The amount of times the code was ran during the session.">Times Ran</th>
				<th title="The amount of times ran where an error existed in the code.">Error Count</th>
			</tr>
		</thead>
		<tbody>
			<?php
	foreach($question_sessions as $session)
	{
		echo "<tr>";
		foreach($keys as $session_key)
		{
			if($session_key == "mouse_clicks")
			{
				$total_mouse_clicks += $session->get_prop($session_key);
			}
			else if($session_key == "key_presses")
			{
				$total_key_presses += $session->get_prop($session_key);
			}
			else if($session_key == "times_ran")
			{
				$total_times_ran += $session->get_prop($session_key);
			}
			else if($session_key == "session_length")
			{
				$total_session_length += $session->get_prop("elapsed");
			}
			else if($session_key == "error_count")
			{
				if($session->get_prop("error_count") > 0)
				{
					$total_error_count += $session->get_prop("error_count");
				}
			}

			if($session_key == 'securable_id')
			{
				//echo Securable::get_string_from_id($session->get_prop($session_key));
			}
			else if($session_key == 'activity_id')
			{
				echo "<td>";
				$activity_id = $session->get_prop($session_key);
				$question_name = Question::get($activity_id)->get_properties()['name'];
				if($question_name == "" or $question_name == null)
				{
					$question_name = "Question " . $activity_id;
				}
				echo '<a href="?controller=question&action=read&id=' . $activity_id . '">' . $question_name . '</a>';
				//echo Securable::get_string_from_id($session->get_prop($session_key));
				echo "</td>";
			}
			else
			{
				echo "<td>";
				echo $session->get_prop($session_key);
				echo "</td>";
			}
		}
		echo "</tr>";
	}
            ?>
			<tr>
				<td></td>
				<td></td>
				<td>Total</td>
				<td>
					<?php echo Session::get_length($total_session_length);?>
				</td>
				<td>
					<?php echo $total_mouse_clicks; ?>
				</td>
				<td>
					<?php echo $total_key_presses; ?>
				</td>
				<td>
					<?php echo $total_times_ran; ?>
				</td>
				<td>
					<?php echo $total_error_count; ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>Average</td>
				<?php
	if (count($question_sessions) > 0)
	{
		echo '<td>' .  Session::get_length(round($total_session_length / count($question_sessions))) . '</td>';
		echo '<td>' .  round($total_mouse_clicks / count($question_sessions)) . '</td>';
		echo '<td>' .  round($total_key_presses / count($question_sessions)) . '</td>';
		echo '<td>' .  round($total_times_ran / count($question_sessions)) . '</td>';
		echo '<td>' .  round($total_error_count / count($question_sessions)) . '</td>';
	}
	else //Prevents division by 0 error. There might be a better way of doing this.
	{
		echo '<td>00:00:00</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
		echo '<td>0</td>';
	}
				?>
			</tr>
		</tbody>
	</table>
</div>
<?php
	$grand_total_mouse_clicks += $total_mouse_clicks;
	$grand_total_key_presses += $total_key_presses;
	$grand_total_times_ran += $total_times_ran;
	$grand_total_session_length += $total_session_length;
	$grand_total_error_count += $total_error_count;
}

if(count($exercise_sessions) > 0 or count($project_sessions) > 0 or count($question_sessions) > 0)
{
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th></th>
				<th>Session Length</th>
				<th>Mouse Clicks</th>
				<th>Key Presses</th>
				<th>Times Ran</th>
				<th>Error Count</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Grand Total</td>
				<td>
					<?php echo  Session::get_length($grand_total_session_length);?>
				</td>
				<td>
					<?php echo $grand_total_mouse_clicks; ?>
				</td>
				<td>
					<?php echo $grand_total_key_presses; ?>
				</td>
				<td>
					<?php echo $grand_total_times_ran; ?>
				</td>
				<td>
					<?php echo $grand_total_error_count; ?>
				</td>
			</tr>
			<tr>
				<td>Grand Average</td>
				<td>
					<?php $count_of_sessions = count($exercise_sessions) + count($project_sessions) + count($question_sessions); ?>
					<?php echo Session::get_length(round($grand_total_session_length / $count_of_sessions)); ?>
				</td>
				<td>
					<?php echo round($grand_total_mouse_clicks / $count_of_sessions); ?>
				</td>
				<td>
					<?php echo round($grand_total_key_presses / $count_of_sessions); ?>
				</td>
				<td>
					<?php echo round($grand_total_times_ran / $count_of_sessions); ?>
				</td>
				<td>
					<?php echo round($grand_total_error_count / $count_of_sessions); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php
}
if($grand_total_session_length === 0)
{
	echo '<h3>This user has not logged any sessions.</h3>';
}
?>
