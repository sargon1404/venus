<div id="dialog-container">
	{{ $this.output_errors() }}
	{{ $this.output_messages() }}
	{{ $this.output_notifications() }}
	{{ $this.output_warnings() }}
	
	<div id="content"> 
		{{ $this.output_content() }} 
	</div>
</div>