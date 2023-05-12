<form method="POST" action="{{ route('login') }}" >
@csrf
<label> username</label>
<input type="text" value="" name="username" placeholder="username">
<label> password</label>
<input type="password" value="" name="password" placeholder="password">
<input type="submit" value="submit"/>
</form>