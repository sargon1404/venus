{% if $announcements %}
	<div id="announcements" class="box">
		{% foreach $announcements as $announcement %}
		<div class="announcement" id="announcement-{{ $announcement.output_id() }}">
	
			<div class="image">
				{% if $announcement.show_image && $announcement.has_image %}
				{{ $announcement.output_image(true) }}
				{% endif %}
			</div>
	
			<div class="container">
				{% if $announcement.show_date %}
				<div class="date">
					{{ $announcement.output_start_date(false) }}
				</div>
				{% endif %}
	
				{% if $announcement.show_title %}
				<h2>{{ $announcement.output_link() }}</h2>
				{% endif %}
	
				<div class="content">
					{{ $announcement.output_text() }}
				</div>
			</div>
			
			<div class="clear"></div>
		</div>
		{% endforeach %}
	</div>
{% endif %}