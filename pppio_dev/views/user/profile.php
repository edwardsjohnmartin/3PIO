<h2>Profile</h2>
<div>
	<label for="name">Name</label>
	<div><?php echo $properties['name'];?></div>

	<label for="email">Email</label>
	<div><?php echo $properties['email'];?></div>

	<label for="role">Role</label>
	<div><?php echo $properties['role']->value;?></div>
</div>

<h3>Reset Password</h3>
<form action="?controller=user&action=profile" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="token" value="<?php echo getToken();?>" />

    <label for="cur_password">Current Password</label>
    <input type="password" class="form-control" name="cur_password" />

    <label for="new_password">New Password</label>
    <input type="password" class="form-control" name="new_password" />

    <label for="conf_password">Confirm Password</label>
    <input type="password" class="form-control" name="conf_password" />

    <input type="submit" class="form-control" />
</form>
