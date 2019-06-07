<div class="pagination">

{% if $pagination.previous.show %}
	<a href="{{ $pagination.previous.url | raw}}" data-page="{{ $pagination.previous.page }}" class="previous">{{ pagination_previous }}</a>
{% endif %}

{% if $pagination.first.show %}
	<a href="{{ $pagination.first.url | raw }}" data-page="{{ $pagination.first.page }}" class="first">{{ pagination_first }}</a>
{% endif %}

{% foreach $pagination.pages as $page %}
	<a href="{{ $page.url | raw }}" data-page="{{ $page.page }}" class="{{ $page.class }}">{{ $page.page }}</a>
{% endforeach %}

{% if $pagination.jump.show %}
	<a href="javascript:void(0)" class="jump" data-tooltip-static="{{ $pagination.jump.form }}" data-position="top-left">{{ pagination_jump }}</a>
{% endif %}

{% if $pagination.last.show %}
	<a href="{{ $pagination.last.url | raw }}" data-page="{{ $pagination.last.page }}" class="last">{{ pagination_last }}</a>
{% endif %}

{% if $pagination.next.show %}
	<a href="{{ $pagination.next.url | raw }}" data-page="{{ $pagination.next.page }}" class="next">{{ pagination_next }}</a>
{% endif %}

</div>