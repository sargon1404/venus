<div class="tag" id="tag-{{ $tag.output_id() }}">

	{{ $tag.output_extra('extra1') }}
	
	{% if $tag.show_title %}
	<h1 class="heading">
		{{ $tag.output_title() }}
	</h1>
	{% endif %}

	{{ $tag.output_extra('extra2') }}
	
	<div class="container">
	
		{{ $tag.output_extra('extra_container1') }}

		<div class="content">
		
			{% if $tag.show_image && $tag.has_image %}
			<figure class="image">
				{{ $tag.output_image() }}
				<figcaption>
					{{ $tag.output_image_caption() }}
				</figcaption>
			</figure>
			{% endif %}		
		
			{% if $tag.show_description && $tag.has_description %}
			<div class="description">
				<span>{{ $tag.output_description() }}</span>
			</div>
			{% endif %}
			
			<div class="clear"></div>
		</div>
		
		{{ $tag.output_extra('extra_container2') }}
		
		{% if $tag.show_blocks && $tag.has_blocks %}
			{% include html/tags/blocks %}
		{% endif %}
		{{ $tag.output_extra('extra_container3') }}
		
		{% if $tag.show_pages && $tag.has_pages %}
			{% include html/tags/pages %}						
		{% endif %}		
		{{ $tag.output_extra('extra_container4') }}
	</div>
	
	{{ $tag.output_extra('extra3') }}
	
	<div class="clear"></div>
</div>