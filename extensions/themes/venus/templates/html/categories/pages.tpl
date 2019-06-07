<div class="category-pages">
	{{ $category.output_extra('extra_pages1') }}
	
	{% foreach $category.pages as $page %}
	<div class="category-page">

		{% if $page.show_title %}
		<h3>{{ $page.output_link() }}</h3>
		{% endif %}
						
		<div class="info">														
			{% if $page.show_category && $page.has_category %}
			<span class="main-category">{{ $page.output_category_link(false, true, true) }}</span>
			{% endif %}
			
			{% if $page.show_date %}
			<span class="date">{{ $page.output_date(true) }}</span>
			{% endif %}
			
			{% if $page.show_author && $page.has_author %}
			<span class="author">{{ $page.output_author(false) }}</span>
			{% endif %}
		</div>
		
		<div class="container">						
			{% if $page.show_image && $page.has_image %}
			<div class="image">
				{{ $page.output_image(true) }}
			</div>
			{% endif %}
																	
			{% if $page.show_text && $page.has_text %}
			<div class="content">																				
				<div class="text">
				{{ $page.output_read_more_text() }}
				</div>				
				
				<div class="bottom">
					{{ $page.output_read_more_link('class="button"') }}
				</div>
			</div>												
			{% endif %}
			
			<div class="clear"></div>
		</div>
																				
	</div>
	{% endforeach %}
	
	{{ $category.output_extra('extra_pages2') }}
</div>
<div class="clear"></div>
		
{{ $category.output_pagination() }}	