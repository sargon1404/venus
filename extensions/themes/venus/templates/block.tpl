<div class="block" id="block-{{ $block.output_id() }}">

	{{ $plugins.output('block_extra1', $block) }}

	{% if $block.show_title %}
	<h1 class="heading">
		{% if $block.show_category && $block.has_category %}
		{{ $block.category.output_link($block.show_image, false) }}
		{% endif %}

		{{ $block.output_title() }}
	</h1>
	{% endif %}

	{{ $plugins.output('block_extra2') }}

	<div class="container">

		{{ $plugins.output('block_extra_container1') }}

		<div class="attributes">
			{{ $plugins.output('block_extra_attributes1') }}

			{% if $block.comments_show_count && $block.show_comments %}
			<a href="#comments" class="comments-count">{{ $block.output_comments_count() }}</a>
			{% endif %}

			{{ $plugins.output('block_extra_attributes2') }}


			{{ $plugins.output('block_extra_attributes3') }}
		</div>

		{{ $plugins.output('block_extra_container2') }}

		{% if $block.show_description && $block.has_description %}
		<div class="description">
			{{ $block.output_description() }}
		</div>
		{% endif %}

		{{ $plugins.output('block_extra_container3') }}

		<div class="content">
			{% if $block.show_image && $block.has_image %}
			<figure class="image">
				{{ $block.output_image() }}
				<figcaption>
					{{ $block.output_image_caption() }}
				</figcaption>
			</figure>
			{% endif %}

			{{ $block.output_content() }}

			<div class="clear"></div>
		</div>

	</div>

	{{ $plugins.output('block_extra3') }}


	<div class="clear"></div>
</div>

{{ $plugins.output('block_extra4') }}



{{ $plugins.output('block_extra5') }}