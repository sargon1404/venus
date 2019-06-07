<div id="messages" class="alert">
	<a href="javascript:void(0)" onclick="venus.ui.close('messages')" class="close"></a>
	<div class="icon"></div>
	<div class="content">
	{% foreach $messages as $message %}
		<div class="text">
			{{ $message.output() }}
		</div>
	{% endforeach %}
	</div>

	<div class="clear"></div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('messages');
	}, 10 * 1000);
</script>