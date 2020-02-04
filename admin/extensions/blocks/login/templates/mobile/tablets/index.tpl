<form action="{{ $url }}" method="post" id="login-form">
{{ $html.token() }}
<input type="hidden" name="action" value="login" />
<input type="hidden" name="referrer_url" value="{{ $referrer_url }}" />

<article id="login">

	<fieldset>
		{{ $plugins.output('fields_1') }}
		
		<div class="field required">
			<label for="username">{{ login_form1 }}</label>
			<input type="text" id="username" name="username" required autofocus />
		</div>
		
		<div class="field required">
			<label for="password">{{ login_form2 }}</label>
			<input type="password" id="password" name="password" required />
		</div>
		
		{{ $plugins.output('fields_2') }}
		
		<div class="field">
			<label for="language">{{ login_form3 }}</label>
			<select name="language" id="language" size="1">
			{{ $html.selectOptions($languages, $default_language) }}
			</select>
		</div>
		
		{{ $plugins.output('fields_3') }}
		
		<div class="field">
			<input type="submit" value="{{ login_form4 }}" />
		</div>
	</fieldset>
	
</article>
</form>