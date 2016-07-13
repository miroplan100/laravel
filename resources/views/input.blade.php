@extends('app')

@section('content')

<div class="block-input">

<h2>Вход</h2>

<div id="input.info" style="color: red;"></div>

<form onsubmit = "return false;" >

<div><input type="text" id="login" placeholder="Логин"></div>

<div><input type="password" id="password" placeholder="Пароль"></div>

<div><input type="hidden" value="{!!csrf_token()!!}" name="_token"></div>

<div><button onclick="func.input(this);">Вход</button></div>

</form>

</div>

@endsection