<div id="warnings-container">
	<div class="close"><a href="javascript:void(0)" onclick="venus.alerts.hide_warnings()"><img src="{{ $this.images_url }}x.png" alt="close" /></a></div>
	<div class="icon"><img src="{{ $this.images_url }}messages/warning_32.png" alt="warnings" /></div>
	<div class="warnings">
		<ul>
		{% foreach $warnings as $warning %}
			<li>{{ $warning.output_text() }}</li>
		{% endforeach %}
		</ul>
	</div>
</div>