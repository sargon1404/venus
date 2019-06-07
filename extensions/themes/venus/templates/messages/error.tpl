<div id="error-container">
	<div class="title">{{ $error.output_title(false) }}</div>
	<div class="error">
		<div class="icon"><img src="{{ $this.images_url }}messages/error_64.png" alt="error" /></div>
		<div class="text">{{ $error.output_text() }}</div>
		<div class="clear"></div>
	</div>
</div>