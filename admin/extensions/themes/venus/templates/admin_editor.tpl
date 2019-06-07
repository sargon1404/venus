{% if $editor.load_toolbar %}
{% include admin_editor_toolbar %}
{% endif %}

<div class="admin-editor" style="width:{{ $editor.width }}">
	<div class="editor-top">
		<div class="editor-top-left">
			{% if $editor.load_toolbar %}
			<a href="javascript:void(0)" onclick="venus.editor.toggle_toolbar('{{ $editor.name | js }}')" class="button" data-tooltip="{{{ editor_toggle_toolbar }}}" data-position="under-right"><img src="{{ $this.images_url }}editor/toggle_toolbar.png" alt="{{ editor_toggle_toolbar }}" /></a>
			{% endif %}
			{% if $editor.show_toggle %}
			<a href="javascript:void(0)" onclick="venus.editor.toggle_editor('{{ $editor.name | js }}','{{ $editor.width }}','{{ $editor.height }}')" class="button" data-tooltip="{{{ editor_toggle }}}" data-position="under-right"><img src="{{ $this.images_url }}editor/toggle.png" alt="{{ editor_toggle }}" /></a>
			{% endif %}
			{% if $editor.show_templates %}
			<a href="javascript:venus.dialog.open('editor_templates', ' {{ dialog_editor_templates }}', 'templates.png', {field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_templates')" class="button" data-tooltip="{{{ editor_editor_templates }}}" data-position="under-right"><img src="{{ $this.images_url }}editor/templates.png" alt="{{ editor_editor_templates }}" /></a>
			{% endif %}
			{% if $editor.show_dynamic_snippets %}
			<a href="javascript:venus.dialog.open('snippets',' {{ dialog_snippets }}','snippets.png', {dynamic: 1, field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_snippets')" class="button" data-tooltip="{{{ editor_snippets_dynamic }}}" data-position="under-right"><img src="{{ $this.images_url }}editor/snippets_dynamic.png" alt="{{ editor_snippets }}" /></a>
			{% endif %}
		</div>

		<div class="editor-top-right">
			{% if $editor.show_page_break %}
			<a href="javascript:void(0)" class="button" onclick="venus.editor.insert_page_break('{{ $editor.name | js }}')" data-tooltip="{{{ editor_page_break }}}" data-position="under-left"><img src="{{ $this.images_url }}editor/page_break.png" alt="{{ editor_pagebreak }}" /></a>
			{% endif %}
			{% if $editor.show_read_more %}
			<a href="javascript:void(0)" class="button" onclick="venus.editor.insert_read_more_start('{{ $editor.name | js }}')" data-tooltip="{{{ editor_readmore_start }}}" data-position="under-left"><img src="{{ $this.images_url }}editor/read_more_start.png" alt="{{ editor_readmore_start }}" /></a>
			<a href="javascript:void(0)" class="button" onclick="venus.editor.insert_read_more_end('{{ $editor.name | js }}')" data-tooltip="{{{ editor_readmore_end }}}" data-position="under-left"><img src="{{ $this.images_url }}editor/read_more_end.png" alt="{{ editor_readmore_start }}" /></a>
			<a href="javascript:void(0)" class="button" onclick="venus.widget.toggle('admin-editor-read-more-{{ $editor.name | js }}',this,'',false,'under-left')" data-tooltip="{{{ editor_readmore }}}" data-position="under-left"><img src="{{ $this.images_url }}editor/read_more.png" alt="{{ editor_readmore }}" /></a>
			<div style="display:none" id="admin-editor-read-more-{{ $editor.name | js }}">
				<table class="form">
					<tr>
						<td><label for="{{ $editor.name | js }}_read_more_url"> {{ editor_readmore_url }}</label></td>
						<td><input type="text" name="{{ $editor.name | js }}_read_more_url" id="{{ $editor.name | js }}_read_more_url" class="big" value="{{ $editor.read_more_url }}" /></td>
					</tr>
					<tr>
						<td><label for="{{ $editor.name | js }}_read_more_text"> {{ editor_readmore_text }}</label></td>
						<td><textarea name="{{ $editor.name | js }}_read_more_text" id="{{ $editor.name | js }}_read_more_text" rows="10" cols="10" class="big">{{ $editor.read_more_text }}</textarea></td>
					</tr>
				</table>
			</div>
			{% endif %}
		</div>
	</div>

	<div class="admin-editor-textarea">
		{{ $editor.output_extra('extra1') }}

		{{ $editor.output_html() }}

		{{ $editor.output_extra('extra2') }}
	</div>

	<div class="editor-bottom">
		<div class="editor-bottom-left">
			<ul>
				{{ $editor.output_extra('extra3') }}
				<li><a href="javascript:venus.dialog.open('links', '{{ editor_link }}', 'links.png', {field_name: '{{ $editor.name | js }}'})"><img src="{{ $this.images_url }}editor/link.png" alt="{{ editor_link }}" /> {{ editor_link }}</a></li>
				<li><a href="javascript:venus.dialog.open('media_image_browser',' {{ dialog_media_image_browser }}', 'media_image_browser.png', {init_dir : '', field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_media_image_browser')"><img src="{{ $this.images_url }}editor/image.png" alt="{{ editor_image }}" /> {{ editor_image }}</a></li>
				<li><a href="javascript:venus.dialog.open('media_file_browser',' {{ dialog_media_file_browser }}', 'media_file_browser.png', {init_dir : '', field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_media_file_browser')"><img src="{{ $this.images_url }}editor/file.png" alt="{{ editor_file }}" /> {{ editor_file }}</a></li>
				<li><a href="javascript:void(0)" data-tooltip-static="" data-tooltip-id="dialog-cnt-smilies-{{{ $editor.name | js }}}" data-position="top-left"><img src="{{ $this.images_url }}editor/smilies.png" alt="{{ editor_smilies }}" /> {{ editor_smilies }}</a></li>
				<li><a href="javascript:venus.dialog.open('videos',' {{ dialog_videos }}', 'videos.png', {field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_videos')"><img src="{{ $this.images_url }}editor/video.png" alt="{{ editor_videos }}" /> {{ editor_videos }}</a></li>
				<li><a href="javascript:venus.dialog.open('content_templates',' {{ editor_content_templates }}', 'content_templates.png', {field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_templates')"><img src="{{ $this.images_url }}editor/content_templates.png" alt="{{ editor_content_templates }}" /> {{ editor_content_templates }}</a></li>
				{% if $editor.show_snippets %}
				<li><a href="javascript:venus.dialog.open('snippets',' {{ dialog_snippets }}', 'snippets.png', {field_name: '{{ $editor.name | js }}'}, '{{ $editor.name | js }}_snippets')"><img src="{{ $this.images_url }}editor/snippets.png" alt="{{ editor_snippets }}" /> {{ editor_snippets }}</a></li>
				{% endif %}
				{% if $editor.show_previous_versions %}
				<li><a href="{{ $editor.previous_versions_code }}"><img src="{{ $this.images_url }}editor/previous_versions.png" alt="{{ editor_previous_versions }}" /> {{ editor_previous_versions }}</a></li>
				{% endif %}
				{{ $editor.output_extra('extra4') }}
			</ul>
		</div>

		<div class="editor-bottom-right">
			<a href="javascript:venus.editor.preview('{{ $editor.name | js }}',' {{ editor_preview }}','{{ $venus->user->token }}')" class="button"><img src="{{ $this.images_url }}editor/preview.png" alt="{{ editor_preview }}" /> {{ editor_preview }}</a>
			<div id="{{ $editor.name | js }}_preview" style="display:none"></div>
		</div>
	</div>
</div>