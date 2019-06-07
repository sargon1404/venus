	</div>

	{{ $this.output_banners('bottom') }}

	<footer>
		{{ $this.output_footer_extra() }}
		<span style="text-align:right">{{ $this.output_execution_time() }} sec, {{ $this.output_memory_usage() }} MB</span>
	</footer>

</div>

{{ $this.output_dialogs_content() }}
{{ $this.output_footer() }}
</body>
</html>