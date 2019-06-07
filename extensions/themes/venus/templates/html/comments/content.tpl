<div class="content">
	{{ $comment.output_extra('extra_html1') }}
	
	{{ $comment.output_html() }}
	
	{{ $comment.output_extra('extra_html2') }}
	
	{% if $comment.modified_by %}
	<div class="last-modified">
		{{ $comment.output_last_modified() }}
	</div>
	{% endif %}
</div>

{% if $comment.signature %}
	<div class="signature">
		{{ $comment.author.output_signature() }}
	</div>
{% endif %}

<div class="toolbar">
	{{ $comment.output_reply_button() }}
</div>