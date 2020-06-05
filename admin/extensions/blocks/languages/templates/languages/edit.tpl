<input type="hidden" name="id" value="{{ $item.id }}" />

{% if $item.note %}
<div id="item-note">
	<a href="javascript:venus.ui.close('item-note')" class="close"></a>
	{{ $item.note }}
</div>
{% endif %}

{% include form %}

<div id="item-details" class="hidden">
{{ $item.details | raw }}
</div>