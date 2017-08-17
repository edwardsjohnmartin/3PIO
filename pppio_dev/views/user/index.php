<!-- <?php
if(has_permission(new Permission(Securable::USER, Permission_Type::CREATE)))
{
	echo '<a href="/?controller=' . $this->model_name . '&action=create" class="btn btn-primary">Create</a><br>';
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
	<td>
		<?php echo htmlspecialchars($props['name'] . ' (' . $props['email'] . ')'); ?>
	</td>
	<td>
		<?php echo '<a href="/?controller=role&action=read&id=' . $props['role']->key. '">' . htmlspecialchars($props['role']->value) . '</a><br>'; ?>
	</td>
<?php
	if($can_read)
	{ ?>
	<td>
		<a href="<?php echo '/?controller=' . $this->model_name . '&action=read&id=' . $model->get_id();?>">View</a><br>
	</td>
	<?php } ?>
<?php
	if($can_edit)
	{ ?>
	<td>
		<a href="<?php echo '/?controller=user&action=update&id=' . $model->get_id(); ?>">Update</a><br>
	</td>
	<?php } ?>
</tr>
<?php
}
?>
</table>
