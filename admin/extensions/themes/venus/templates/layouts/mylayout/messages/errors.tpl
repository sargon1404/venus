<div id="errors" class="alert">
	<a href="javascript:void(0)" onclick="venus.ui.close('errors')" class="close"></a>
	<div class="icon"></div>
	<div class="content">
		{% foreach $errors as $error %}
		<div class="text">
			{{ $error.output() }}
		</div>
		{% endforeach %}
	</div>

	<div class="clear"></div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('errors');
	}, 10 * 1000);
</script>