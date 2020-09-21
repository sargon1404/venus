<div id="navbar" class="grid">
	<div id="navbar-links" class="col-24 col-lg-12 center left-lg">
		{% if $navbar.icon %}
			<img src="{{ $navbar.icon }}" alt="navbar-icon" class="hidden visible-lg" />
		{% endif %}

		<h1>{{ $navbar.outputTitle() }}</h1>

		{{ $navbar.outputLinks() }}
	</div>

	<div id="navbar-buttons" class="col-24 col-lg-12 center right-lg">
		{{ $navbar.outputButtons() }}
	</div>
</div>
<script>
venus.navbar.auto();
</script>