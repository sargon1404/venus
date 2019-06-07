<div id="errors-container">
	<div class="close"><a href="javascript:void(0)" onclick="venus.alerts.hide_errors()"><img src="{{ $this.images_url }}x.png" alt="close" /></a></div>
	<div class="icon"><img src="{{ $this.images_url }}messages/error_32.png" alt="errors" /></div>
	<div class="errors">
		<ul>
		{% foreach $errors as $error %}
			<li>{{ $error.output_text() }}</li>
		{% endforeach %}
		</ul>
	</div>
</div>