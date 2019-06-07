<div id="warnings" class="alert">
	<a href="javascript:void(0)" onclick="venus.ui.close('warnings')" class="close"></a>
	<div class="icon"></div>
	<div class="content">
		{% foreach $warnings as $warning %}
		<div class="text">
			{{ $warning.output() }}
		</div>
		{% endforeach %}
	</div>

	<div class="clear"></div>
</div>

<script type="text/javascript">
	setTimeout(function(){
		venus.ui.close('warnings')
	}, 10 * 1000);
</script>