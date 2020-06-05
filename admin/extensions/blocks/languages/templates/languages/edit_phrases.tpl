<main class="full">
	<article>
		<section class="right">
			<form action="{{ $this->url }}" method="post" id="package-form">
				<input type="hidden" name="action" value="edit_phrases" />
				<input type="hidden" name="id" value="{{ $item.lid }}" />
				{{ languages_edit_phrases3 }}&nbsp;
				<select name="pid" id="pid" size="1" class="auto" onchange="venus.html.submit_form('package-form')">
				{{ $html.select_options($packages, $pid) }}
				</select>
				&nbsp;<input type="submit" value="{{ go }}" />
			</form>
		</section>

		<section>
			{{ $navbar.output_form_start(true) }}
			<input type="hidden" name="id" value="{{ $item.lid }}" />
			<input type="hidden" name="pid" value="{{ $pid }}" />

			<table class="form">
				{% foreach $phrases as $phrase %}
				<tr>
					<td>{{ $phrase.phrase }}</td>
					<td>
						{% if $phrase.big  %}
						<textarea name="phrases[{{ $phrase.phrase }}]" rows="10" cols="4" class="big">{{ $phrase.text }}</textarea>
						{% else %}
						<input type="text" name="phrases[{{ $phrase.phrase }}]" class="big" value="{{ $phrase.text }}" />
						{% endif %}
					</td>
				</tr>
				{% endforeach %}
			</table>
			{{ $navbar.output_form_end(true) }}
		</section>
	</article>
</main>