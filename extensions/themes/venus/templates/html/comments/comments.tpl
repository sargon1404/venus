{% if $comments || $document.comments_open %}
	<div id="comments">
	
	{{ $comments.output_extra('extra1') }}
		
	{% if $comments %}
		<div id="comments-container">
			{% include html/comments/container %}
		</div>
	{% endif %}
	
	{{ $comments.output_extra('extra2') }}
	
	{% if $document.is_moderator && $comments %}
		<div id="comments-moderator-tools">
			{{ $comments.output_moderator_tools() }}
		</div>
	{% endif %}
	
	{{ $comments.output_extra('extra3') }}

	{% if $document.comments_open %}
	<div id="comment-form">
		{{ $comments.output_form_start() }}
		
			{{ $comments.output_extra('extra_form1') }}
					
			<div class="comment-fields-top">
				{{ $comments.output_form_fields() }}				
			</div>
			
			{{ $comments.output_extra('extra_form2') }}
	
			<div class="comment-editor" id="comment_editor">
				{{ $comments.output_editor() }}
			</div>
			
			{{ $comments.output_extra('extra_form3') }}
	
			{% if $comments.show_captcha %}
			{{ $comments.output_form_captcha() }}
			{% endif %}
			
			{{ $comments.output_extra('extra_form4') }}
	
			<div class="comment-submit">
				{{ $comments.output_form_submit() }}
			</div>
			
			{{ $comments.output_extra('extra_form5') }}
	
			<div class="comment-fields-bottom">
				{{ $comments.output_form_notify() }}
			</div>
			
			{{ $comments.output_extra('extra_form6') }}
		
		{{ $comments.output_form_end() }}
	</div>
	{% endif %}
	</div>
	
	{{ $comments.output_extra('extra4') }}
{% endif %}