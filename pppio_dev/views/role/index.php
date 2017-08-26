<table class="table table-striped table-bordered">
<?php
	$can_read = has_permission(new Permission(Securable::ROLE, Permission_Type::READ));
	echo '<h2>' . $this->model_name . ' List</h2>';
foreach($models as $k => $v)
{
?>
<tr>
	<td>
		<?php echo htmlspecialchars($v); ?>
	</td>
<?php
	if($can_read)
	{ ?>
	<td>
		<a href="<?php echo '?controller=' . $this->model_name . '&action=read&id=' . $k;?>">View</a><br>
	</td>
	<?php } ?>
</tr>
<?php
}
?>
</table>
