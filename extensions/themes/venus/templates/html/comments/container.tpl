{% foreach $comments as $comment %}
<div class="comment box comment-usergroup-{{ $comment.author.output_usergroup_id() }}{{ $comment.output_style() }}" id="comment-{{ $comment.output_id() }}">

	{{ $comment.output_extra('extra1') }}
	
	<div class="avatar">
		{{ $comment.output_extra('extra_avatar1') }}
		
		{{ $comment.author.output_avatar(true) }}
		
		{{ $comment.output_extra('extra_avatar2') }}
	</div>
	
	{{ $comment.output_extra('extra2') }}

	<div class="container">

		<div class="author">
			{{ $comment.output_extra('extra_user1') }}
			
			{{ $comment.author.output_link() }}
			
			{{ $comment.output_extra('extra_user2') }}

			<span class="date">{{ $comment.output_timestamp() }}</span>
			
			{{ $comment.output_extra('extra_user3') }}
		</div>

		<div id="comment-content-{{ $comment.output_id() }}">
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
			<div class="signature box-top">
				{{ $comment.author.output_signature() }}
			</div>
			{% endif %}

			<div class="toolbar">
				{{ $comment.output_toolbar() }}
			</div>
		</div>
	</div>
	
	{{ $comment.output_extra('extra3') }}
	
	<div class="clear"></div>

	{% if $document.is_moderator %}
	<div class="moderator-toolbar box-top">
		{{ $comment.output_moderator_toolbar() }}
	</div>
	{% endif %}
</div>
{% endforeach %}

{{ $comments.output_pagination('comments-container') }}