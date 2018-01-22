<!-- <?php
if(has_permission(new Permission(Securable::USER, Permission_Type::CREATE)))
{
	echo '<a href="?controller=' . $this->model_name . '&action=create" class="btn btn-primary">Create</a><br>';
}
?> -->

<table class="table table-striped table-bordered">
<?php
//i am expecting $models to be defined!!!!!
//where do i store things like how many pages there should be?
//
$can_read = has_permission(new Permission(Securable::USER, Permission_Type::READ));
$can_edit = has_permission(new Permission(Securable::USER, Permission_Type::EDIT));
foreach($models as $model)
{
$props = $model->get_properties();
?>

<tr>
	<td><?php echo htmlspecialchars($props['name'] . ' (' . $props['email'] . ')'); ?></td>
	<td><?php echo '<a href="?controller=role&action=read&id=' . $props['role']->key. '">' . htmlspecialchars($props['role']->value) . '</a><br>'; ?></td>
	<td>
		<?php
		if($props['role']->key === Role::STUDENT){?>
			<button onclick=reset_password(<?php echo $model->get_id();?>)>Reset User's Password</button>
		<?php
		}
		?>
	</td>
<?php
	if($can_read){?>
		<td><a href="<?php echo '?controller=' . $this->model_name . '&action=read&id=' . $model->get_id();?>">View</a><br></td>
	<?php 
	} 

	if($can_edit){?>
		<td><a href="<?php echo '?controller=user&action=update&id=' . $model->get_id(); ?>">Update</a><br></td>
		<td><a href="<?php echo '?controller=user&action=delete&id=' . $model->get_id(); ?>" onclick="return confirm('Do you want to delete this user?')">Delete</a><br></td>
	<?php 
	} 
	?>
</tr>
<?php
}
?>
</table>
<div id="div_alert"></div>
<script>
	function reset_password(user_id) {
		clearAlerts();

		var conf = confirm("Do you want to reset this users password to a randomly generated password?");

		if (conf) {
			console.log("conf worked and is true");

			//call reset_password in user controller
			$.ajax({
				method: "POST",
				url: "?controller=user&action=reset_password",
				data: { user_id: user_id },
				success: function (data) {
					if (data.success) {
						html_class = "success";
					} else {
						html_class = "danger";
					}
					showAlert(data.message, html_class);
				}
			});
		}
	}

	function showAlert(alertMessage, html_class) {
		div_alert.innerHTML += '<div class="alert alert-' + html_class + ' alert-dismissible mar-0" role="alert" id="infoAlert">'
			+ alertMessage + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span ' +
			'aria-hidden="true">&times;</span></button></div>';
	}

	function clearAlerts() {
		div_alert.innerHTML = '';
	}
</script>
