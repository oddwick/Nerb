<h1>OSNX</h1>
<hr />
<div class="row">
	<div class="medium-6 columns push-three">
		<h3>Log In</h3>
		<form action="/default/action/login" enctype="application/x-www-form-urlencoded" method="post">
		<label for="user_name">User</label>
		<input type="text" name="user_name" id="user_name" placeholder="User Name or ID" value="<?= $_COOKIE['user_name']; ?>" />
		<label for="user_pass">Password</label>
		<input type="password" name="user_pass" id="user_pass" placeholder="Password" />
		<label for="key_code">Key Code PIN</label>
		<input type="text" name="key_code" id="key_code" placeholder="Keycode" />
		<input type="submit" class="button" value="Login"/>
		</form>
	</div>
</div>
