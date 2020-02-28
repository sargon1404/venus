<div id="notifications" class="grid alert">
	<div class="icon col-0 col-md-3"></div>
	<div class="col-21 col-md-18">
		{% foreach $notifications as $notification %}
		<p>
			{{ $notification.output() }}
		</p>
		{% endforeach %}
	</div>
	<div class="close-icon col-3">
		<a href="javascript:void(0)" onclick="venus.ui.close('notifications')" class="close"></a>
	</div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('notifications');
	}, 10 * 1000);
</script>