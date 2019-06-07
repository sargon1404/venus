<div id="permission-denied-container">
	<div class="title">{{ permission_denied }}</div>
	<div class="permission-denied">
		<div class="icon"><img src="{{ $this.images_url }}messages/permission_denied.png" alt="permission_denied" /></div>
		<div class="text">
			{{ permission_denied_text | raw }}		
		</div>
		<div class="clear"></div>
	</div>
</div>