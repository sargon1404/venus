<!doctype html>
<html lang="{{ $this.output_lang_code() }}">
<head>
	{{ $this.output_head() }}
</head>

<body>
{{ $this.output_body_extra() }}

<div id="document">

	<header>
		<div id="header-logo">
			<h1><a href="{{ $this.site_index }}">{{ $theme.settings.site_logo }}</a></h1>
			<h3>{{ $theme.settings.site_slogan }}</h3>
		</div>

		<div id="header-right">
			<ul>
				{% if !$venus.user.uid %}
				<li><a href="javascript:venus.dialog.open('login','{{ login }}','login.png')">{{ login }}</a></li>
				<li><a href="{{ $this.output_register_url() }}">{{ register }}</a></li>
				{% else %}
				<li><a href="{{ $this.output_profile_url() }}">{{ my_profile }}</a></li>
				{% if $this.block_is_installed('private_messages') %}
					{% if $venus.user.pms_unread %}
					<li><a href="{{ $this.output_private_messages_url() }}" class="red">{{ private_messages }} [{{ $venus.user.pms_unread }}]</a></li>
					{% else %}
					<li><a href="{{ $this.output_private_messages_url() }}">{{ private_messages }}</a></li>
					{% endif %}
				{% endif %}
				<li><a href="{{ $this.output_control_panel_url() }}">{{ control_panel }}</a></li>
				<li>{{ $this.output_logout_form() }}<a href="javascript:venus.logout()">{{ logout }}</a></li>
				{% endif %}
			</ul>

			<div id="header-search">
			{{ $this.output_widgets('header') }}
			</div>
		</div>

		<nav>
			{{ $this.output_menu('main') }}
			<div class="clear"></div>
		</nav>
	</header>

	{% if $theme.is_homepage %}
	<img src="{{ $this.images_url }}home.jpg" style="width:100%" alt="venus" />
	{% endif %}

	{{ $this.output_banners('top') }}
	{{ $this.output_messages() }}
	{{ $this.output_errors() }}
	{{ $this.output_notifications() }}
	{{ $this.output_warnings() }}

	<div id="container">
		{{ $this.output_breadcrumbs() }}
		{{ $this.output_announcements() }}