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
venus.sidebar.auto(true);

venus.ui.set_confirm_strings([
	{type: 'navbar', action: 'uninstall', title: '{{ languages_confirm2 | jsc }}', text: '{{ languages_confirm4 | jsc }}'},
	{type: ['quick', 'form'], action: 'uninstall', title: '{{ languages_confirm1 | jsc }}', text: '{{ languages_confirm3 | jsc }}'},
	{type: 'form', action: 'switch_users', title: '{{ languages_confirm5 | jsc }}', text: '{{ languages_confirm6 | jsc }}'}
]);


function set_default(lid)
{
	var form = 'item-form-' + lid;
	var form_select = 'item-action-' + lid;

	venus.get(form_select).val('set_default');
	venus.html.submit_form(form);
}
</script>