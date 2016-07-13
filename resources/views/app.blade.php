<!DOCTYPE html>

<html>

<head>

<title>Laravel</title>

<meta name="_token" content="{!!csrf_token()!!}" />

<link rel="stylesheet" type="text/css" href="css/style.css">

<script type="text/javascript" src="js/ajax.js"></script>

</head>

<body>
      
<nav>

<ul class="menu">

<li><b style="font-size: 25px; float: left;">[Laravel]</b></li>

<li><a href="/">Статьи</a></li>
																																									
<li>

@if(array_key_exists('user',$_COOKIE)) 

<span style="float: right;"><a href="admin">Админ панель</a> | <a href="javascript:void(0);" onclick="func.exit();">Выход</a></span>

@else

<a href="input" style="float: right;">Вход</a>

@endif

</li>
					
</ul>

</nav>

<div id="content">
               
@yield('content')

</div>

</body>
    
</html>
