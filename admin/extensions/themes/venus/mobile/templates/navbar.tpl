<div id="admin-navbar">
	<div id="admin-navbar-title">
		<h1>{{ $venus.navbar.output_title() }}</h1>
		<div id="admin-navbar-links">
			{{ $venus.navbar.output_links() }}
		</div>
	</div>
	<div id="admin-navbar-buttons">
		{{ $venus.navbar.output_outer_form() }}
		{{ $venus.navbar.output_buttons() }}
	</div>
</div>