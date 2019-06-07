<div id="notifications" class="alert">
	<a href="javascript:void(0)" onclick="venus.ui.close('notifications')" class="close"></a>
	<div class="icon"></div>
	<div class="content">
		{% foreach $notifications as $notification %}
		<div class="text">
			{{ $notification.output() }}
		</div>
		{% endforeach %}
	</div>

	<div class="clear"></div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('notifications');
	}, 10 * 1000);
</script>