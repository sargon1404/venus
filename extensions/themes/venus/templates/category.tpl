<div class="category" id="category-{{ $category.output_id() }}">

	{{ $category.output_extra('extra1') }}
	
	{% if $category.show_title %}
	<h1 class="heading">
		{% if $category.show_category && $category.has_category %}
		{{ $category.output_category_link($category.show_category_image, false) }}
		{% endif %}
		
		{{ $category.output_title() }}
	</h1>
	{% endif %}
	
	{{ $category.output_extra('extra2') }}

	<div class="container">
	
		{{ $category.output_extra('extra_container1') }}
		
		<div class="content">
		
			{% if $category.show_image && $category.has_image %}
			<figure class="image">
				{{ $category.output_image() }}
				<figcaption>
					{{ $category.output_image_caption() }}
				</figcaption>
			</figure>
			{% endif %}
				
			{% if $category.show_description && $category.has_description %}
			<div class="description">
				<span>{{ $category.output_description() }}</span>
			</div>
			{% endif %}
			
			<div class="clear"></div>
		</div>
		
		{{ $category.output_extra('extra_container2') }}
		
		{% if $category.show_subcategories && $category.has_subcategories %}
			{% include html/categories/subcategories %}
		{% endif %}
		{{ $category.output_extra('extra_container3') }}
				
		{% if $category.show_blocks && $category.has_blocks %}
			{% include html/categories/blocks %}
		{% endif %}
		{{ $category.output_extra('extra_container4') }}
		
		{% if $category.show_links && $category.has_links %}
			{% include html/categories/links %}
		{% endif %}
		{{ $category.output_extra('extra_container5') }}

		{% if $category.show_news && $category.has_news %}
			{% include html/categories/news %}
		{% endif %}
		{{ $category.output_extra('extra_container6') }}			
		
		{% if $category.show_pages && $category.has_pages %}
			{% include html/categories/pages %}						
		{% endif %}		
		{{ $category.output_extra('extra_container7') }}
	</div>
	
	{{ $category.output_extra('extra3') }}
	
	<div class="clear"></div>
</div>