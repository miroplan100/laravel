@extends('app')

@section('content')

<h2>Уникальные пользователи за 24 часа.. </h2>

@foreach ($arr as $key => $value)
	
<div style="border-bottom: 1px dashed green; margin-bottom: 10px;">

<b>#{{($key + 1)}}</b>

<p>IP: {{$arr[$key]['ip']}}</p>

<p>Кук: {{$arr[$key]['cookie_id']}}</p>

<p>Браузер: {{$arr[$key]['browser']}}</p>

<p>Ос: {{$arr[$key]['os']}}</p>

<p>Гео: <b>{{$arr[$key]['country']}}</b>, {{$arr[$key]['city']}}</p>

<p>Реф: <a href="{{$arr[$key]['referer']}}">{{$arr[$key]['referer']}}</a></p>

<p>Дата: <b>{{$arr[$key]['date']}}</b></p>

</div>

@endforeach

@endsection
