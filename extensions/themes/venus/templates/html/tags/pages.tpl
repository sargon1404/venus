<div class="tag-pages">
	{{ $tag.output_extra('extra_pages1') }}
	
	{% foreach $tag.pages as $page %}
	<div class="tag-page">

		{% if $page.show_title %}
		<h3>{{ $page.output_link() }}</h3>
		{% endif %}
						
		<div class="info">																	
			{% if $page.show_date %}
			<span class="date">{{ $page.output_date() }}</span>
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
	
	{{ $tag.output_extra('extra_pages2') }}
</div>
<div class="clear"></div>
		
{{ $tag.output_pagination() }}	