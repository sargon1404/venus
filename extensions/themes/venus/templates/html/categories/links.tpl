<div class="category-links">
	{{ $category.output_extra('extra_links1') }}

	{% foreach $category.links as $link %}
	<div class="category-link">
		<a href="{{ $link.output_url() }}" {{ $link.output_link_properties() }}>
		{% if $link.show_image && $link.has_image %}
		{{ $link.output_image() }}
		{% endif %}
		
		{% if $link.show_title %}
		<span>{{ $link.output_title() }}</span>
		{% endif %}
		</a>
	</div>
	{% endforeach %}
	
	{{ $category.output_extra('extra_links2') }}
	<div class="clear"></div>
</div>