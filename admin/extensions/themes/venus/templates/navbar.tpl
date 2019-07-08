<div id="navbar">
	<div id="navbar-icon">
		{% if $navbar.icon %}
		<img src="{{ $navbar.icon }}" alt="navbar-icon" />
		{% endif %}
	</div>

	<div id="navbar-nav">
		<div id="navbar-title">
			<h1>{{ $navbar.outputTitle() }}</h1>
		</div>
		<div id="navbar-links">
			{{ $navbar.outputLinks() }}
		</div>
	</div>

	<div id="navbar-buttons">
		{{ $navbar.outputOuterForm() }}
		{{ $navbar.outputButtons() }}
	</div>

	<div class="clear"></div>
</div>