<!doctype html>
<html lang="{{ $this.outputLangCode() }}">
<head>
	{{ $this.outputHead() }}
</head>

<body>
{{ $this.output_body_extra() }}

<header>
	<div id="topbar">
		<nav>
			{{ $this.output_menu() }}
		</nav>
		<div id="topbar-right">
			{{ $this.output_config_link() }}
			{{ $this.output_help_link() }}
			{{ $this.output_logout_link() }}
		</div>
	</div>
</header>

{% if $navbar.display %}
	{% include navbar %}
{% endif %}

<div id="container">
	<div id="alerts">
		{{ $this.output_alerts() }}
	</div>