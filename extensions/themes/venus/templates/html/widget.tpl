<div class="widget box" id="widget-{{ $widget.output_id() }}">
	{% if $widget.show_title %}
	<div class="title">
		<h3>{{ $widget.output_title() }}</h3>
	</div>
	{% endif %}

	<div class="content">
		{{ $widget.output_content() }}
	</div>
</div>