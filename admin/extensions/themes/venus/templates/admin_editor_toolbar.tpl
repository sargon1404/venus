<div id="admin-editor-toolbar" style="display:none">
	<ul>
		{{ $editor.output_extra('extra_toolbar1') }}	
		<li><a href="javascript:venus.editor.insert_div_columns2(venus.editor.name)" data-tooltip="{{{ editor_col2 }}}" data-scroll=""><img src="{{ $this.images_url }}editor/col2.png" alt="{{ editor_col2 }}" /></a></li>
		<li><a href="javascript:venus.editor.insert_div_columns3(venus.editor.name)" data-tooltip="{{{ editor_col3 }}}" data-scroll=""><img src="{{ $this.images_url }}editor/col3.png" alt="{{ editor_col3 }}" /></a></li>
		{{ $editor.output_extra('extra_toolbar2') }}	
		<li class="separator"></li>
		<li><a href="javascript:venus.editor.insert_table(venus.editor.name,'list')" data-tooltip="{{{ editor_table1 }}}" data-scroll=""><img src="{{ $this.images_url }}editor/table1.png" alt="{{ editor_table1 }}" /></a></li>
		<li><a href="javascript:venus.editor.insert_table(venus.editor.name,'grid')" data-tooltip="{{{ editor_table2 }}}" data-scroll=""><img src="{{ $this.images_url }}editor/table2.png" alt="{{ editor_table2 }}" /></a></li>
		<li><a href="javascript:venus.editor.insert_table(venus.editor.name,'data')" data-tooltip="{{{ editor_table3 }}}" data-scroll=""><img src="{{ $this.images_url }}editor/table3.png" alt="{{ editor_table3 }}" /></a></li>
		{{ $editor.output_extra('extra_toolbar3') }}	
	</ul>
</div>