<div id="warnings" class="grid alert">
	<div class="icon col-0 col-md-3"></div>
	<div class="col-21 col-md-18">
		{% foreach $warnings as $warning %}
		<p>
			{{ $warning.output() }}
		</p>
		{% endforeach %}
	</div>
	<div class="close-icon col-3">
		<a href="javascript:void(0)" onclick="venus.ui.close('warnings')" class="close"></a>
	</div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('warnings');
	}, 10 * 1000);
</script>