<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

@if(!empty($_bot) && $_bot)
<meta name="robots" content="noindex,nofollow">
@endif

@if(!empty($_meta))
<meta name="description" content="{{$_meta['description']}}">
<meta name="keywords" content="{{$_meta['keywords']}}">

<meta property="fb:app_id"          content="227375124451364" /> 
<meta property="og:type"            content="website" /> 
<meta property="og:url"             content="{{Request::fullUrl()}}" /> 
<meta property="og:title"           content="{{$_meta['title']}}" /> 
<meta property="og:description"     content="{{$_meta['description']}}" />
<meta property="og:image"           content="{{$_meta['image']}}" /> 

<meta name="twitter:card" content="summary" />
<meta name="twitter:url" content="{{Request::fullUrl()}}" />
<meta name="twitter:title" content="{{$_meta['title']}}" />
<meta name="twitter:description" content="{{$_meta['description']}}" />
<meta name="twitter:image" content="{{$_meta['image']}}" />

<title>{{$_meta['title']}}</title>
@endif