<div class="category-blocks">
	{{ $category.output_extra('extra_blocks1') }}
	
	{% foreach $category.blocks as $block %}
	<div class="category-block">
	
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
	
	{{ $category.output_extra('extra_blocks2') }}
<div class="clear"></div>
</div>