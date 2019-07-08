</div>

<footer>
	<div class="footer-links">
		<ul class="list">
			<li><a href="{{ $venus.admin_index }}">{{ footer_dashboard }}</a></li>
			<li><a href="{{ $venus.site_index }}">{{ footer_homepage }}</a></li>
		</ul>
	</div>

	<div class="footer-stats"></div>
	<div class="footer-copyright">
	{{ powered_by }} <a href="https://www.venus-cms.org">Venus CMS</a> {{ $venus.version }}
	</div>

	{{ $this.outputFooterExtra() }}
</footer>

<script type="text/javascript">
venus.tooltips.enable();
</script>

{{ $this.outputDialogs() }}

</body>
</html>