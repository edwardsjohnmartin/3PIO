<?php
	require_once('views/shared/html_helper.php'); 
	//use a helper to make a form... or a helper to make the fields... haven't decided yet.
	//right now assuming $model_name ...
	//i want to be able to get the properties something that's not already created...
	//i can just use the types...
	echo $this->model_name;
	echo '<div>';
	foreach($properties as $key => $value)
	{
		if(isset($types[$key])) //it had better be set! should i just use string if it's not set?
		{
			echo HtmlHelper::label($key);
			echo HtmlHelper::span($types[$key], $key, $value);
		}
	}
	echo '<br>';
	echo '<table class="table table-striped table-bordered">';
	echo '<thead><tr><th>Securable</th><th>Permissions</th></tr></thead>';
	echo '<tbody>';
	foreach($model::get_permissions_for_role($model->get_id()) as $securable => $permissions)
	{
		echo '<tr>';
		echo '<td>' . ucfirst(strtolower(Securable::search($securable))) . '</td>'; // will want to split into words if we ever have multi word securables
		echo '<td>';
		$i = 0;
		foreach ($permissions as $permission => $t)
		{
			if($i==0)
			{
				echo ucfirst(strtolower(Permission_Type::search($permission)));
			}
			else
			{
				echo strtolower(Permission_Type::search($permission));
			}
			if($i < count($permissions) - 1) echo ', ';
			$i++;
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';

?>
