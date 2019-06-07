<!doctype html>
<html lang="{{ $this.output_lang_code() }}">
<head>
{{ $this.output_head() }}
</head>

<body>
{{ $this.output_body_extra() }}

<header class="login-header">
	<div id="topbar"></div>
	<div id="logo" >
		<a href="{{ $this.site_index }}"><img src="{{ $this.images_url }}header-logo.jpg" alt="logo" /></a>
	</div>
</header>

<div id="container">
	<div id="login-alerts">
		{{ $this.output_alerts() }}
	</div>