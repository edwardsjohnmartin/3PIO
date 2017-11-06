<?php
//Creates a table of all sessions belonging to a single student

$total_mouse_clicks = 0;
$total_key_presses = 0;
$total_times_ran = 0;
$total_session_length = 0;

$grand_total_mouse_clicks = 0;
$grand_total_key_presses = 0;
$grand_total_times_ran = 0;
$grand_total_session_length = 0;

//Used to iterate over all the properties and get the information out of the sessions array
$keys = array(
	"securable_id",
	"activity_id",
	"start_time",
	"end_time",
	"session_length",
	"mouse_clicks",
	"key_presses",
	"times_ran"
);

if(isset($exercise_sessions) and count($exercise_sessions) > 0)
{
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Activity Type</th>
				<th>Activity ID</th>
				<th>Session Start</th>
				<th>Session End</th>
				<th>Session Length</th>
				<th>Mouse Clicks</th>
				<th>Key Presses</th>
				<th>Times Ran</th>
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

			echo "<td>";
			if($session_key == 'securable_id')
			{
				echo Securable::get_string_from_id($session->get_prop($session_key));
			}
			else
			{
				echo $session->get_prop($session_key);
			}
			echo "</td>";
		}
		echo "</tr>";
	}
            ?>
			<tr>
				<td></td>
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
			</tr>
			<tr>
				<td></td>
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
	}
	else //Prevents division by 0 error. There might be a better way of doing this.
	{
		echo '<td>00:00:00</td>';
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

	$total_mouse_clicks = 0;
	$total_key_presses = 0;
	$total_times_ran = 0;
	$total_session_length = 0;
}
if(isset($project_sessions) and count($project_sessions) > 0)
{
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Activity Type</th>
				<th>Activity ID</th>
				<th>Session Start</th>
				<th>Session End</th>
				<th>Session Length</th>
				<th>Mouse Clicks</th>
				<th>Key Presses</th>
				<th>Times Ran</th>
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

			echo "<td>";
			if($session_key == 'securable_id')
			{
				echo Securable::get_string_from_id($session->get_prop($session_key));
			}
			else
			{
				echo $session->get_prop($session_key);
			}
			echo "</td>";
		}
		echo "</tr>";
	}
            ?>
			<tr>
				<td></td>
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
			</tr>
			<tr>
				<td></td>
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
	}
	else //Prevents division by 0 error. There might be a better way of doing this.
	{
		echo '<td>00:00:00</td>';
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

	$total_mouse_clicks = 0;
	$total_key_presses = 0;
	$total_times_ran = 0;
	$total_session_length = 0;
}
if(isset($question_sessions) and count($question_sessions) > 0)
{
?>
<div class="force-x-scroll">
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Activity Type</th>
				<th>Activity ID</th>
				<th>Session Start</th>
				<th>Session End</th>
				<th>Session Length</th>
				<th>Mouse Clicks</th>
				<th>Key Presses</th>
				<th>Times Ran</th>
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

			echo "<td>";
			if($session_key == 'securable_id')
			{
				echo Securable::get_string_from_id($session->get_prop($session_key));
			}
			else
			{
				echo $session->get_prop($session_key);
			}
			echo "</td>";
		}
		echo "</tr>";
	}
            ?>
			<tr>
				<td></td>
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
			</tr>
			<tr>
				<td></td>
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
	}
	else //Prevents division by 0 error. There might be a better way of doing this.
	{
		echo '<td>00:00:00</td>';
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
			</tr>
		</thead>
		<tbody>
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
			</tr>
		</tbody>
	</table>
</div>
<?php
}
?>
