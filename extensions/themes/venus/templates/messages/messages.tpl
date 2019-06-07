<div id="messages-container">
	<div class="close"><a href="javascript:void(0)" onclick="venus.alerts.hide_messages()"><img src="{{ $this.images_url }}x.png" alt="close" /></a></div>
	<div class="icon"><img src="{{ $this.images_url }}messages/message_32.png" alt="message" /></div>
	<div class="messages">
	{% foreach $messages as $message %}
		{{ $message.output_text() }}<br />
	{% endforeach %}
	</div>
</div>