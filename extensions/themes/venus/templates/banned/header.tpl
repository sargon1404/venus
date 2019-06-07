<!doctype html>
<html lang="{{ $this.output_lang_code() }}">
<head>
	{{ $this.output_head() }}
</head>

<body>
{{ $this.output_body_extra() }}

<div id="document">

	<header>	
		<div id="header-logo">
			<h1><a href="{{ $this.site_index }}">{{ $theme.settings.site_logo }}</a></h1>
			<h3>{{ $theme.settings.site_slogan }}</h3>
		</div>	
	</header>
	
	<div id="container">