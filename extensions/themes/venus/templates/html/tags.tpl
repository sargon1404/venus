<div id="tags" class="box-top">
	<h4>{{ tags }}</h4>
	
	<ul>
		{% foreach $tags as $tag %}
		<li id="tag-{{ $tag.output_id() }}">
			<a href="{{ $tag.output_url() }}"{{ $tag.output_link_properties() }}>
				{% if $tag.show_image && $tag.has_image %}
				{{ $tag.output_image() }}
				{% endif %}	
				
				{% if $tag.show_title %}
				<span>{{ $tag.output_title() }}</span>
				{% endif %}	
			</a>
		</li>
		{% endforeach %}
	</ul>
</div>