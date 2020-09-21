<main>
{% include list %}
</main>

<aside>
	<div id="sidebar-top"><h2>{{ sidebar_filter_order }}</h2></div>
	<div id="sidebar-content">
		{{ $plugins.output('sidebar1', $items) }}
		{{ $controls.outputFilters() }}
		{{ $plugins.output('sidebar2', $items) }}
	</div>
</aside>
<div class="clear"></div>
