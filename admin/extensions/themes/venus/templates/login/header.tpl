<!doctype html>
<html lang="{{ $this.outputLangCode() }}">
<head>
{{ $this.outputHead() }}
</head>

<body>
{{ $this.outputBodyExtra() }}

<header class="login-header">
	<div id="topbar"></div>
	<div id="logo" >
		<a href="{{ $this.site_index }}"><img src="{{ $this.images_url }}header-logo.jpg" alt="logo" /></a>
	</div>
</header>

<div id="container">
	<div id="login-alerts">
		{{ $this.outputAlerts() }}
	</div>