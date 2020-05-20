<form action="{{ $url }}" method="post">
{{ $html.token() }}
<input type="hidden" name="action" value="login" />
<input type="hidden" name="referrer_url" value="{{ $referrer_url }}" />

<section class="half three-fourths-lg full-sm middle">

	<fieldset>
		{{ $plugins.output('fields_1') }}

		<div>
			<label for="username" class="required">{{ login_form1 }}</label>
			<input type="text" id="username" name="username" autofocus />
		</div>

		<div>
			<label for="password" class="required">{{ login_form2 }}</label>
			<input type="password" id="password" name="password" />
		</div>

		{{ $plugins.output('fields_2') }}

		<div>
			<label for="language">{{ login_form3 }}</label>
			<select name="language" id="language" size="1">
			{{ $html.selectOptions($languages, $default_language) }}
			</select>
		</div>

		{{ $plugins.output('fields_3') }}

		<div class="submit">
			<input type="submit" value="{{ login_form4 }}" />
		</div>
	</fieldset>

</section>

</form>