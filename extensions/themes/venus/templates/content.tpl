<div>
	{{ $this.outputWidgets('top') }}
	
	<article id="content">		
		{{ $this.outputContent() }} 
	</article>
	
	<aside>
		{{ $this.outputWidgets('right') }}
	</aside>
	
	<div class="clear"></div>
	
	{{ $this.outputWidgets('bottom') }}
</div>
