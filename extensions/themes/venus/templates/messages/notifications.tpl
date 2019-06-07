<div id="notifications-container">
	<div class="close"><a href="javascript:void(0)" onclick="venus.alerts.hide_notifications()"><img src="{{ $this.images_url }}x.png" alt="close" /></a></div>
	<div class="icon"><img src="{{ $this.images_url }}messages/notification_32.png" alt="notification" /></div>
	<div class="notifications">
		<ul>
		{% foreach $notifications as $notification %}
			<li>{{ $notification.output_text() }}</li>
		{% endforeach %}
		</ul>
	</div>
</div>