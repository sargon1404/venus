<main>
{% include list %}
</main>

<aside>
	<div id="sidebar-top"><h2>{{ sidebar_filter_order }}</h2></div>
	<div id="sidebar-content">
		{{ $plugins.output('sidebar1', $items) }}
		{{ $controls.output_filters() }}
		{{ $controls.output_order() }}
		{{ $plugins.output('sidebar2', $items) }}
	</div>
</aside>
<div class="clear"></div>


<script type="text/javascript" >
venus.navbar.auto();
venus.sidebar.auto();

venus.ui.set_confirm_strings([
	{type: 'navbar', action: 'delete', title: '{{ languages_confirm2 | jsc }}', text: '{{ languages_confirm4 | jsc }}'},
	{type: ['quick', 'form'], action: 'delete', title: '{{ languages_confirm1 | jsc }}', text: '{{ languages_confirm3 | jsc }}'}
]);
</script>

<div id="upload-form" class="hidden" data-title="{{ languages_list3 }}">
	<form action="{{ $url }}" id="form-test" method="post" enctype="multipart/form-data">
		{{ $html.token() }}
		{{ $html.ajax() }}
		<input type="hidden" name="action" value="upload" />

		<div class="upload-area">
			<input type="file" name="import_file" />
		</div>

		<progress value="0" max="100" class="hidden"></progress>

		<input type="checkbox" name="import_overwrite" value="1" id="import_overwrite" /><label for="import_overwrite">{{ languages_list4 }}</label>

		<div id="upload-form-error" class="hidden"></div>
	</form>
</div>