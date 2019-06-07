<div id="navbar">
	<div id="navbar-icon">
		{% if $navbar.icon %}
		<img src="{{ $navbar.icon }}" alt="navbar-icon" />
		{% endif %}
	</div>
	
	<div id="navbar-nav">
		<div id="navbar-title">
			<h1>{{ $navbar.output_title() }}</h1>
		</div>
		<div id="navbar-links">
			{{ $navbar.output_links() }}
		</div>
	</div>
	
	<div id="navbar-buttons">
		{{ $navbar.output_outer_form() }}
		{{ $navbar.output_buttons() }}
	</div>
	
	<div class="clear"></div>
</div>