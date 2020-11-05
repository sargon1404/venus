{{ $plugins.output('list1') }}

<table class="list">
	<tr>
		<th style="width:3%"><input type="checkbox" id="ids0" onclick="venus.html.toggle_checkboxes('ids',this.checked)" /><label for="ids0"></label></th>
		{{ $plugins.output('list_header1') }}
		<th style="width:30%" class="tmain"><a href="">{{ languages_list1 }}</a></th>
		<th class="mobile-small-hidden" style="width:5%"><a href="">{{ id }}</a></th>
		<th class="mobile-small-hidden" style="width:20%">{{ quick_action }}</th>
		{{ $plugins.output('list_header2') }}
		<th class="mobile-hidden" style="width:10%"><a href="">{{ status }}</a></th>
		<th class="mobile-hidden" style="width:10%">{{ default }}</th>
		{{ $plugins.output('list_header3') }}
		<th style="width:22%">{{ action }}</th>
	</tr>

	{% foreach $items as $item %}
	<tr id="item-{{ $item.id }}">
		<td><input type="checkbox" name="ids[]" id="ids{{ $item.id }}" form="admin-form" value="{{ $item.id }}" /><label for="ids{{ $item.id }}"></label></td>
		{{ $plugins.output('list_item1', $item) }}
		<td class="tmain">
			<a href="{{ $item.edit_url }}" data-tooltip="{{{ $item.note }}}">{{ $item.title }}</a>
			<small>{{ $item.name }}</small>
			{{ $plugins.output('list_item_main', $item) }}
		</td>
		<td class="mobile-small-hidden tid">{{ $item.id }}</td>
		<td class="mobile-small-hidden">{{ $item.actions_list | raw }}</td>
		{{ $plugins.output('list_item2', $item) }}
		<td class="mobile-hidden">
			{% if $item.status %}
			<img src="{{ $this.images_url }}enabled.png" data-tooltip="{{{ languages_list_hint1 }}}" alt="{{ languages_list_hint1 }}" />
			{% endif %}		
		</td>
		<td class="mobile-hidden">	
		</td>
		<td class="taction">
			<span class="loading-small" id="loading-item-{{ $item.id }}"></span>
			{{ $item.actions_form | raw }}
		</td>
	</tr>
	{{ $plugins.output('list_extra', $item) }}
	{% endforeach %}

	{% if !$items %}
	<tr>
		<td class="colspan" colspan="7">{{ languages_list99 }}</td>
	</tr>
	{% endif %}
</table>