<!doctype html>
<html lang="{{ $this.outputLangCode() }}">
<head>
{{ $this.outputHead() }}
</head>

<body>
{{ $this.outputBodyExtra() }}


<div id="wrapper">
	{{ $this.outputMenu() }}

	<div id="container-wrapper">
		{% if $navbar.display %}
		{% include navbar %}
		{% endif %}

		<div id="container">
			<div id="alerts">
				{{ $this.outputAlerts() }}
			</div>