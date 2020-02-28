<div id="errors" class="grid alert">
	<div class="icon col-0 col-md-3"></div>
	<div class="col-21 col-md-18">
		{% foreach $errors as $error %}
		<p>
			{{ $error.output() }}
		</p>
		{% endforeach %}
	</div>
	<div class="close-icon col-3">
		<a href="javascript:void(0)" onclick="venus.ui.close('errors')" class="close"></a>
	</div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('errors');
	}, 10 * 1000);
</script>