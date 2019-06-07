<div class="rating">
	<div class="message" id="rating-message">{{ $rating.output_message() }}</div>
	<div class="error" id="rating-error">{{ $rating.output_error() }}</div>
	
	<div class="numeric">
		<span class="value">{{ $rating.output_rating_number() }}</span>
		{% if $rating.show_count %}
		- <span class="count">{{ $rating.output_count() }}</span>
		{% endif %}

		{{ $rating.output_rating() }}
	</div>
</div>
<div class="clear"></div>