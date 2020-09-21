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
		</nav>
		<div id="topbar-right" class="col-14 col-md-4 right">
			{{ $this.outputConfigLink() }}
			{{ $this.outputHelpLink() }}
			{{ $this.outputLogoutLink() }}
		</div>
	</div>
</header>

{% if $navbar.display %}
	{% include navbar %}
{% endif %}

<div id="container">
	<div id="alerts">
		{{ $this.outputAlerts() }}
	</div>