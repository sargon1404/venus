<div style="text-align:right">
	{{ $this.output_footer_extra() }}
</div>

<script type="text/javascript">
//<![CDATA[
venus.add_onload(function (){
	venus.dialog.resize();
});
//]]>
</script>

{{ $this.output_dialogs_content() }}
{{ $this.output_footer() }}
</body>
</html>