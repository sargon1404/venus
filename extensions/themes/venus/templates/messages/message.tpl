<div id="message-container">
	<div class="title">{{ $message.output_title(false) }}</div>
	<div class="message">
		<div class="icon"><img src="{{ $this.images_url }}messages/message_64.png" alt="message" /></div>
		<div class="text">{{ $message.output_text() }}</div>
		<div class="clear"></div>
	</div>
</div>