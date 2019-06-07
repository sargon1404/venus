<div id="breadcrumbs" class="box">
	<ul>	
	{% foreach $breadcrumbs as $breadcrumb %}
		<li>
			{% if !$breadcrumb.is_last %}
			{{ $breadcrumb.output_link() }}
			{% else %}
			<span>{{ $breadcrumb.output_title() }}</span>
			{% endif %}
		</li>
		{% if !$breadcrumb.is_last %}
		<li class="separator">{{ $breadcrumbs.output_separator() }}</li>
		{% endif %}
	{% endforeach %}		
	</ul>
</div>