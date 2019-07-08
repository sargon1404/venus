<!doctype html>
<html lang="{{ $this.outputLangCode() }}">
<head>
	{{ $this.outputHead() }}
</head>

<body>
{{ $this.outputBodyExtra() }}

<header>
	<div id="topbar">
		<nav>
			{{ $this.outputMenu() }}
		</nav>
		<div id="topbar-right">
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