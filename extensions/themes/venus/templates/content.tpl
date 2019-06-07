<div>
	{{ $this.output_widgets('top') }}
	
	<article id="content">		
		{{ $this.output_content() }} 
	</article>
	
	<aside>
		{{ $this.output_widgets('right') }}
	</aside>
	
	<div class="clear"></div>
	
	{{ $this.output_widgets('bottom') }}
</div>