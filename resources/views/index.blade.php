@extends('app')

@section('content')

@foreach ($articles as $key => $value) 
	
<article>

<h3><a href="article?id={{$articles[$key]['article_id']}}">{{$articles[$key]['title']}}</a></h3>

<p>{{@substr($articles[$key]['text'],0,@strpos($articles[$key]['text'],' ',800))}}...</p>
	
</article>

@endforeach

@endsection