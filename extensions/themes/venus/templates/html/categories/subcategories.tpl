<div class="category-subcategories">
	{{ $category.output_extra('extra_subcategories1') }}
		
	{% foreach $category.subcategories as $category %}
	<div class="category-subcategory">
	
		{% if $category.show_image && $category.has_image %}
		<div class="image">
			{{ $category.output_image(true) }}
		</div>		
		{% endif %}
		
		<div class="container">
			{% if $category.show_title %}
			<h3>{{ $category.output_link() }}</h3>
			{% endif %}
			
			{% if $category.show_description && $category.has_description %}
			<div class="content">
				{{ $category.output_read_more_text() }}
			</div>
			{% endif %}
			
		</div>
		
		<div class="clear"></div>
	</div>
	{% endforeach %}
	
	{{ $category.output_extra('extra_subcategories2') }}	
	<div class="clear"></div>
</div>
<div class="clear"></div>