{{ $plugins.output('list1') }}

<table class="list sticky">
	<thead>
		<th style="width:3%"><input type="checkbox" id="ids0" onclick="venus.html.toggle('ids', this)" /><label for="ids0"></label></th>
		{{ $plugins.output('list_header1') }}
		<th style="width:30%" class="tmain"><a href="{{ $venus.order.output_link('title') }}">{{ languages_list1 }} {{ $venus.order.output_icon('title') }}</a></th>
		<th class="mobile-small-hidden" style="width:20%">{{ quick_action }}</th>
		{{ $plugins.output('list_header2') }}
		<th class="mobile-hidden" style="width:10%">{{ languages_list2 }}</th>
		<th class="mobile-hidden" style="width:15%">{{ version }}</th>
		{{ $plugins.output('list_header3') }}
		<th style="width:22%">{{ action }}</th>
	</thead>

	<tbody>
	{% foreach $items as $item %}
	<tr>
		<td style="width:3%"><input type="checkbox" name="ids[]" form="admin-form" id="ids{{ $item.id }}" value="{{ $item.name }}" /><label for="ids{{ $item.id }}"></label></td>
		{{ $plugins.output('list_item1', $item) }}
		<td class="tmain">
			<a href="{{ $item.install_url }}">{{ $item.title }}</a>
			<small>{{ $item.name }}</small>
			{{ $plugins.output('list_item_main', $item) }}
		</td>
		<td class="mobile-small-hidden">{{ $item.actions_list | raw }}</td>
		{{ $plugins.output('list_item2', $item) }}
		<td class="mobile-hidden"><img src="{{ $item.flag }}" alt="{{ $item.title }}" width="20" /></td>
		<td class="mobile-hidden">{{ $item.version }}</td>
		{{ $plugins.output('list_item3', $item) }}
		<td class="taction">{{ $item.actions_select | raw }}</td>
	</tr>
	{{ $plugins.output('list_extra', $item) }}
	{% endforeach %}

	{% if !$items %}
	<tr>
		<td class="colspan" colspan="6">{{ languages_list99 }}</td>
	</tr>
	{% endif %}
	</tbody>
</table>

{{ $controls.output_bottom() }}

{{ $plugins.output('list2') }}