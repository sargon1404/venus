<div id="redirect" class="alert-screen">
	<div class="content">
		{{ $redirect.output_text() }}
	</div>
</div>


<script type="text/javascript">
	var refresh_interval = {{ $redirect.interval }};
	window.setTimeout(function(){
		window.location = "{{ $redirect.url | js_code }}";
	}, refresh_interval * 1000);
</script>