<div class="category-news">
	{{ $category.output_extra('extra_news1') }}
	
	{% foreach $category.news as $news_record %}
	<div class="category-news-record">
											
		{% if $news_record.show_date %}
		<div class="date">
			{{ $news_record.output_start_date() }}
		</div>
		{% endif %}
		
		{% if $news_record.show_title %}
		<h2>{{ $news_record.output_link() }}</h2>
		{% endif %}
			
		<div class="container">	
			{% if $news_record.show_image && $news_record.has_image %}
			<div class="image">
				{{ $news_record.output_image(true) }}
			</div>
			{% endif %}
			
			<div class="content">
				{{ $news_record.output_description() }}
			</div>				
		</div>
		
		<div class="clear"></div>
	</div>
	{% endforeach %}
	
	{{ $category.output_extra('extra_news2') }}
</div>
<div class="clear"></div>