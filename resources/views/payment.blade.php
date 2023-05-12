<meta name="csrf-token" content="{{ csrf_token() }}">
<form action="{{ url('charge') }}" method="post">
    <input type="text" name="amount" />
    {{ csrf_field() }}
    <input type="submit" name="submit" value="Pay Now">
</form>



<form action="{{ url('curltesting') }}" method="post">
    <input type="text" name="amount" />
    {{ csrf_field() }}
    <input type="submit" name="submit" value="curl Now">
</form>