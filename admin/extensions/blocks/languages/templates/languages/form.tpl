<div id="item-info">
	<a href="javascript:venus.ui.close('item-info')" class="close"></a>
	<h1>{{ $item.title }}{{ info_version }}{{ $item.info.version }}</h1>
	<div class="item-info-author">{{ info_author }}<a href="{{ $item.info.url }}" target="_blank">{{ $item.info.author }}</a></div>
	{% if $item.description %}
	<div class="item-info-text">
	{{ $item.description | raw }}
	</div>
	{% endif %}

	<div class="item-info-buttons">
		{% if  $item.readme  %}
		<input type="button" value="{{ readme }}" onclick="venus.dialog.open_element('readme-content', '{{ readme | js }}')" />
		<div id="readme-content" class="hidden">
			{{ $item.readme | raw }}
		</div>
		{% endif %}

		{% if  $item.license  %}
		<input type="button" value="{{ license }}" onclick="venus.dialog.open_element('license-content', '{{ license | js }}')" />
		<div id="license-content" class="hidden">
			{{ $item.license | raw }}
		</div>
		{% endif %}
	</div>
</div>
<div class="clear"></div>

{% if $item.id %}
<div class="quick-action-form">
	<span id="loading-item-{{ $item.id }}" class="loading-small"></span>
	<div id="quick-action">
	{{ $item.quick_action | raw }}
	</div>
</div>
{% endif %}

<div id="tabs">
	{{ $installer.output_tab_first() }}
	<a href="javascript:venus.tab.switch(1)" id="tab-1">{{ languages_form_tab1 }}</a>
	{{ $installer.output_tab_second() }}
	{{ $plugins.output('tabs1') }}
	<a href="javascript:venus.tab.switch(2)" id="tab-2">{{ languages_form_tab2 }}</a>
	{{ $plugins.output('tabs2') }}
	<a href="javascript:venus.tab.switch(3)" id="tab-3">{{ languages_form_tab3 }}</a>
	{{ $plugins.output('tabs3') }}
	<a href="javascript:venus.tab.switch(4)" id="tab-4">{{ languages_form_tab4 }}</a>
	{{ $installer.output_tabs() }}
	{{ $plugins.output('tabs') }}
</div>

{% if $item.id %}
<a href="javascript:venus.dialog.open_element('item-details', '{{ details }}', 'details.png')" id="item-info-icon"><img src="{{ $this.images_url }}details.png" alt="{{ details }}" /></a>
{% endif %}

<script type="text/javascript">
venus.tab.set('{{ $this.get_tab() }}');
venus.tab.auto();

venus.ui.set_confirm_strings([
	{type: 'quick', action: 'uninstall', title: '{{ languages_confirm1 | jsc }}', text: '{{ languages_confirm3 | jsc }}'}
]);
</script>

<div id="content-inner">

<main>
	<article id="tab-content-1">
		<section>
			<div>
				<h3>{{ languages_form_lang_head1 }}</h3>
				<table class="form">
					<tr>
						<td><label for="title" data-tooltip="{{{ languages_form_lang_hint1 }}}">{{ title }}</label></td>
						<td class="required"><input type="text" name="title" required id="title" value="{{ $item.title }}" /></td>
					</tr>
				</table>
				{{ $installer.output_params_left() }}
				{{ $plugins.output('left') }}
			</div>

			<div>
				<h3>{{ languages_form_lang_head2 }}</h3>
				<table class="form">
					<tr>
						<td><label for="debug-0" data-tooltip="{{{ languages_form_lang_hint2 }}}">{{ debug_mode }}</label></td>
						<td>{{ $html.radio_yes_no('debug', $item.debug) }}</td>
					</tr>
				</table>
				{{ $installer.output_params_right() }}
				{{ $plugins.output('right') }}
			</div>
		</section>

		{{ $installer.output_params() }}
		{{ $plugins.output('main') }}
	</article>


	<article id="tab-content-2" class="hidden">
		<section>
			<div>
				<h3>{{ languages_form_content_head1 }}</h3>
				<table class="form">
					<tr>
						<td><label for="content-0" data-tooltip="{{{ languages_form_content_hint1 }}}">{{ languages_form_content1 }}</label></td>
						<td>{{ $html.radio_yes_no('content', $item.content) }}</td>
					</tr>
				</table>
			</div>

			<div class="empty">
			</div>
		</section>
	</article>


	<article id="tab-content-3" class="hidden">
		<section>
			<div>
				<h3>{{ languages_form_params_head1 }}</h3>
				<table class="form">
					<tr>
						<td><label for="encoding" data-tooltip="{{{ languages_form_params_hint1 }}}">{{ languages_form_params1 }}</label></td>
						<td><input type="text" name="encoding" required id="encoding" value="{{ $item.encoding }}" /></td>
					</tr>
					<tr>
						<td><label for="code" data-tooltip="{{{ languages_form_params_hint2 }}}">{{ languages_form_params2 }}</label></td>
						<td><input type="text" name="code" id="code" required value="{{ $item.code }}" /></td>
					</tr>
					<tr>
						<td><label for="url_code" data-tooltip="{{{ languages_form_params_hint3 }}}">{{ languages_form_params3 }}</label></td>
						<td><input type="text" name="url_code" id="url_code" required value="{{ $item.url_code }}" /></td>
					</tr>
					<tr>
						<td><label for="accept_code" data-tooltip="{{{ languages_form_params_hint4 }}}">{{ languages_form_params4 }}</label></td>
						<td><input type="text" name="accept_code" id="accept_code" required value="{{ $item.accept_code }}" /></td>
					</tr>
				</table>
				{{ $plugins.output('params1') }}
			</div>

			<div>
				<h3>{{ languages_form_params_head2 }}</h3>
				<table class="form">
					<tr>
						<td><label for="timestamp_format" data-tooltip="{{{ languages_form_params_hint5 }}}">{{ languages_form_params5 }}</label></td>
						<td><input type="text" name="timestamp_format" id="timestamp_format" value="{{ $item.timestamp_format }}" /></td>
					</tr>
					<tr>
						<td><label for="date_format" data-tooltip="{{{ languages_form_params_hint6 }}}">{{ languages_form_params6 }}</label></td>
						<td><input type="text" name="date_format" id="date_format" value="{{ $item.date_format }}" /></td>
					</tr>
					<tr>
						<td><label for="time_format" data-tooltip="{{{ languages_form_params_hint7 }}}">{{ languages_form_params7 }}</label></td>
						<td><input type="text" name="time_format" id="time_format" value="{{ $item.time_format }}" /></td>
					</tr>
					<tr>
						<td><label for="birthday_format" data-tooltip="{{{ languages_form_params_hint8 }}}">{{ languages_form_params8 }}</label></td>
						<td><input type="text" name="birthday_format" id="birthday_format" value="{{ $item.birthday_format }}" /></td>
					</tr>
				</table>
				{{ $plugins.output('params2') }}
			</div>
		</section>


		<section>
			<div>
				<h3>{{ languages_form_params_head3 }}</h3>
				<table class="form">
					<tr>
						<td><label for="decimal_separator" data-tooltip="{{{ languages_form_params_hint9 }}}">{{ languages_form_params9 }}</label></td>
						<td><input type="text" name="decimal_separator" id="decimal_separator" value="{{ $item.decimal_separator }}" /></td>
					</tr>
					<tr>
						<td><label for="thousands_separator" data-tooltip="{{{ languages_form_params_hint10 }}}">{{ languages_form_params10 }}</label></td>
						<td><input type="text" name="thousands_separator" id="thousands_separator" value="{{ $item.thousands_separator }}" /></td>
					</tr>
				</table>
				{{ $plugins.output('params3') }}
			</div>

			<div>
				<h3>{{ languages_form_params_head4 }}</h3>
				<table class="form">
					<tr>
						<td><label for="date_picker_format" data-tooltip="{{{ languages_form_params_hint11 }}}">{{ languages_form_params11 }}</label></td>
						<td><input type="text" name="date_picker_format" id="date_picker_format" value="{{ $item.date_picker_format }}" /></td>
					</tr>
					<tr>
						<td><label for="time_picker_format" data-tooltip="{{{ languages_form_params_hint12 }}}">{{ languages_form_params12 }}</label></td>
						<td><input type="text" name="time_picker_format" id="time_picker_format" value="{{ $item.time_picker_format }}" /></td>
					</tr>
				</table>
				{{ $plugins.output('params4') }}
			</div>
		</section>

		{{ $plugins.output('params') }}
	</article>


	<article id="tab-content-4" class="hidden">
		<section>
			<div>
				<h3>{{ languages_form_translations_head1 }}</h3>
				<table class="form">
					<tr>
						<td><label for="title_lang" data-tooltip="{{{ languages_form_translations_hint1 }}}">{{ languages_form_translations1 }}</label></td>
						<td><input type="text" name="title_lang" id="title_lang" value="{{ $item.title_lang }}" /></td>
					</tr>
				</table>
			</div>

			<div>
				<h3>{{ languages_form_translations_head2 }}</h3>
				<table class="form">
					<tr>
						<td><label for="site_name" data-tooltip="{{{ languages_form_translations_hint2 }}}">{{ languages_form_translations2 }}</label></td>
						<td><input type="text" name="site_name" id="site_name" value="{{ $item.site_name }}" /></td>
					</tr>
				</table>
			</div>
		</section>
	</article>

	{{ $installer.output() }}
	{{ $plugins.output('bottom') }}
</main>


<aside>
	<div id="sidebar-top"></div>
	<div id="sidebar-content">
		<section>
			{{ $plugins.output('sidebar1') }}
			<label for="status" data-tooltip="{{{ languages_form_sidebar_hint1 }}}">{{ enabled }}</label>
			{{ $html.select_yes_no('status', $item.status) }}

			{{ $plugins.output('sidebar2') }}

			<label for="note" data-tooltip="{{{ languages_form_sidebar_hint2 }}}">{{ note }}</label>
			<textarea name="note" id="note" rows="10" cols="30">{{ $item.note }}</textarea>

			{{ $plugins.output('sidebar3') }}
		</section>

		{{ $plugins.output('sidebar4') }}
	</div>
</aside>

<div class="clear"></div>
</div>