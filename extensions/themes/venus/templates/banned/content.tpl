<div>
	<article id="content" style="width:97%;margin:0 auto">		
		<h1 class="heading">{{ $ban.output_title() }}</h1>
		
		{{ $ban.output_html() }}
		
		{% if $ban.html %}
		<hr />
		{% endif %}
		
		<p>
			<span>{{ banned_str1 }} {{ $ban.output_start_date() }}</span>
			<br />		
			<span>{{ banned_str2 }} {{ $ban.output_end_date() }}</span>	
		</p>
	</article>
	<div class="clear"></div>
</div>
