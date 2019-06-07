<div class="page" id="page-{{ $page.output_id() }}">

	{{ $page.output_extra('extra1') }}
	
	{% if $page.show_title %}
	<h1 class="heading">
		{% if $page.show_category && $page.has_category %}
		{{ $page.output_category_link($page.show_image, false) }}
		{% endif %}		

		{{ $page.output_title() }}
	</h1>
	{% endif %}
	
	{{ $page.output_extra('extra2') }}
	
	<div class="container">
	
		{{ $page.output_extra('extra_container1') }}
		
		{% if $page.show_author || $page.show_date %}
		<div class="meta-top">
			{{ $page.output_extra('extra_meta_top1') }}
			
			{% if $page.show_author && $page.has_author %}
			<span class="author">{{ $page.output_author(true, true, false) }}</span>
			{% endif %}
			
			{{ $page.output_extra('extra_meta_top2') }}
			
			{% if $page.show_date %}
			<span class="date">{{ $page.output_date() }}</span>
			{% endif %}
			
			{{ $page.output_extra('extra_meta_top3') }}
			
			<div class="clear"></div>
		</div>
		{% endif %}
		
		{{ $page.output_extra('extra_container2') }}
			
		<div class="attributes">
			{{ $page.output_extra('extra_attributes1') }}
			
			{% if $page.comments_show_count && $page.show_comments %}
			<a href="#comments" class="comments-count">{{ $page.output_comments_count() }}</a>
			{% endif %}
			
			{{ $page.output_extra('extra_attributes2') }}
			
			{{ $page.output_rating() }}
			
			{{ $page.output_extra('extra_attributes3') }}
		</div>
		
		{{ $page.output_extra('extra_container3') }}
		
		<div class="content">
			{% if $page.show_image && $page.has_image %}
			<figure class="image">
				{{ $page.output_image() }}
				<figcaption>
					{{ $page.output_image_caption() }}				
				</figcaption>
			</figure>
			{% endif %}

			{{ $page.output_content() }}

			<div class="clear"></div>
		</div>
		
		{{ $page.output_extra('extra_container4') }}
		
		{% if $page.show_modified_date %}	
		<div class="meta-bottom">			
			{{ $page.output_extra('extra_meta_bottom1') }}
			
			{{ $page.output_modified_date() }}
			
			{{ $page.output_extra('extra_meta_bottom2') }}
		</div>
		{% endif %}
		
		{{ $page.output_extra('extra_container5') }}

		{% if $page.pages_count > 1 %}
		<div class="pagination-container">
			{{ $page.output_pagination() }}
		</div>
		{% endif %}
		
		{{ $page.output_extra('extra_container6') }}
		
	</div>
	{{ $page.output_extra('extra3') }}
	
	{{ $page.output_tags() }}
	
	<div class="clear"></div>
</div>

{{ $page.output_extra('extra4') }}

{{ $page.output_comments() }}

{{ $page.output_extra('extra5') }}