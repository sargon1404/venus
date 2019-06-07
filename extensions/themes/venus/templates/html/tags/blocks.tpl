<div class="tag-blocks">
	{{ $tag.output_extra('extra_blocks1') }}
	
	{% foreach $tag.blocks as $block %}
	<div class="tag-block">
	
		{% if $block.show_image && $block.has_image %}
		<div class="image">
			{{ $block.output_image(true) }}
		</div>
		{% endif %}
		
		<div class="container">
			{% if $block.show_title %}
			<h3>{{ $block.output_link() }}</h3>
			{% endif %}
			
			{% if $block.show_description && $block.has_description %}
			<div class="content">
				{{ $block.output_read_more_text() }}
			</div>
			{% endif %}
		</div>
		
		<div class="clear"></div>
	</div>
	{% endforeach %}
	
	{{ $tag.output_extra('extra_blocks2') }}
<div class="clear"></div>
</div>