<div class="rating">
	<div class="message" id="rating-message">{{ $rating.output_message() }}</div>
	<div class="error" id="rating-error">{{ $rating.output_error() }}</div>
	
	{{ $rating.output_rating() }}
	
	{% if $rating.show_count %}
	<span class="count">{{ $rating.output_count() }}</span>
	{% endif %}
	<div class="clear"></div>
</div>
<div class="clear"></div>