<!doctype html>
<html lang="{{ $this.outputLangCode() }}">
<head>
{{ $this.outputHead() }}
</head>

<body>
{{ $this.outputBodyExtra() }}

<header>
	<div id="topbar" class="grid">
		<nav class="col-10 col-md-20">
			{{ $this.outputMenu() }}
			<img src="{{ $theme.images_url }}header-logo-small.jpg" alt="Logo" height="18" style="display: inline-block;margin-top: 10px; margin-left: 20px; display: none;">
		</nav>
		<div id="topbar-right" class="col-4 col-md-4 right">
			{{ $this.outputConfigLink() }}
			{{ $this.outputHelpLink() }}
			{{ $this.outputLogoutLink() }}
		</div>
	</div>
</header>


<div id="wrapper">
	<div>
		<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
		<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
		<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	</div>

	<div id="container-wrapper">
		{% if $navbar.display %}
		{% include navbar %}
		{% endif %}
		
		<div id="container">
			<div id="alerts">
				{{ $this.outputAlerts() }}
			</div>