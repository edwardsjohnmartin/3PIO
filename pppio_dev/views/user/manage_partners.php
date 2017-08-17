
<?php
	require_once('views/shared/html_helper.php'); 
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo '<h2>Partners</h2>';
	echo '<div>Partners are automatically logged out when the primary user logs out.</div>';
	if(isset($_SESSION['partners']) && $_SESSION['partners'] != null && count($_SESSION['partners']) > 0)
	{
		echo '<table class="table table-striped table-bordered">';
		foreach($_SESSION['partners'] as $id => $partner)
		{
			echo '<tr>';
			echo '<td>' . $partner->get_properties()['name'] . '</td>';
			echo '<td><a href="\?controller=user&action=log_out_partner&id=' . $id . '">Log partner out</a></td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	else
	{
		echo '<div>You currently don\'t have any partners.</div>';
	}


echo '<a href="?controller=user&action=log_in_partner" class="btn btn-primary">Add a Partner</a><br>';
?>

