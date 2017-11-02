<?php
	//Creates a table of all sessions belonging to a single student

	$total_mouse_clicks = 0;
	$total_key_presses = 0;
	$total_times_ran = 0;
	$total_session_length = 0;

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
			foreach($sessions as $session)
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
				<td><?php echo Session::get_length($total_session_length);?></td>
				<td><?php echo $total_mouse_clicks; ?></td>
				<td><?php echo $total_key_presses; ?></td>
				<td><?php echo $total_times_ran; ?></td>
            </tr>
            <tr>
				<td></td>
				<td></td>
				<td></td>
				<td>Average</td>
                <td><?php echo Session::get_length(round($total_session_length / count($sessions)));?></td>
				<td><?php echo round($total_mouse_clicks / count($sessions)); ?></td>
				<td><?php echo round($total_key_presses / count($sessions)); ?></td>
				<td><?php echo round($total_times_ran / count($sessions)); ?></td>
            </tr>
		</tbody>
	</table>
</div>
