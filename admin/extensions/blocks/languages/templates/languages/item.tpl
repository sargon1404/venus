<td><input type="checkbox" name="ids[]" id="ids{{ $item.id }}" form="admin-form" value="{{ $item.id }}" /><label for="ids{{ $item.id }}"></label></td>
{{ $plugins.output('list_item1', $item) }}
<td class="tmain">
	<a href="{{ $item.edit_url }}" data-tooltip="{{{ $item.note }}}">{{ $item.title }}</a>
	<small>{{ $item.name }}</small>
	{{ $plugins.output('list_item_main', $item) }}
</td>
<td class="mobile-small-hidden tid">{{ $item.id }}</td>
<td class="mobile-small-hidden">{{ $item.quick_action | raw }}</td>
{{ $plugins.output('list_item2', $item) }}
<td class="mobile-hidden">
	{% if $item.status %}
	<img src="{{ $this.images_url }}enabled.png" data-tooltip="{{{ languages_list_hint1 }}}" alt="{{ languages_list_hint1 }}" />
	{% endif %}
</td>
<td class="mobile-hidden">
	{% if $item.is_default %}
	<img src="{{ $this.images_url }}buttons/default_small.png" data-tooltip="{{{ languages_list_hint2 }}}" alt="{{ languages_list_hint2 }}" />
	{% else %}
	<a href="javascript:set_default({{ $item.id }})"><img src="{{ $this.images_url }}buttons/set_default_small.png" data-tooltip="{{{ languages_list_hint3 }}}" alt="{{ languages_list_hint3 }}" /></a>
	{% endif %}
</td>
{{ $plugins.output('list_item3', $item) }}
<td class="taction">
	<span class="loading-small" id="loading-item-{{ $item.id }}"></span>
	{{ $item.form_action | raw }}
</td>