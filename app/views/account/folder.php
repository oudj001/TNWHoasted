<form action="<?= $password_url ?>" method="POST">
	<label>
		Only enter password if you want to change it<br>
		<input type="password" name="password">
	</label>
	<input type="submit" value="Set password">
</form>
<form action="<?= $invite_url ?>" method="POST">
	<label>
		Invite by email<br>
		<input type="text" name="email">
	</label>
	<input type="submit" value="Send invite">
</form>