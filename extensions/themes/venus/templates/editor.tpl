<div class="editor" style="width:{{ $editor.width }}">
	<div class="editor-textarea">
		{{ $editor.output_extra('extra1') }}
		
		{{ $editor.output_html() }}
		
		{{ $editor.output_extra('extra2') }}
	</div>

	{% if $editor.show_toolbar %}
	<div class="editor-toolbar">
		<ul>
			{{ $editor.output_extra('extra3') }}
			
			{% if $editor.uploads_enabled %}
			<li><a href="javascript:venus.dialog.open('uploads_browser', '{{ uploads_browser }}','uploads.png', 'field_name={{ $editor.name | js }}')">{{ uploads }}</a></li>
			{% endif %}
			
			{{ $editor.output_extra('extra4') }}
		</ul>
	</div>
	{% endif %}
</div>