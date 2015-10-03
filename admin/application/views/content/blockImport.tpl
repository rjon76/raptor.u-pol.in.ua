<script type="text/javascript" src="{$ADMIN_DIR}/js/jquery.js"></script>
<script type="text/javascript" src="{$ADMIN_DIR}/js/jquery.form.js"></script>
<script type="text/javascript" src="{$ADMIN_DIR}/js/ajax.js"></script>
<script type="text/javascript" src="{$ADMIN_DIR}/js/basic.js"></script>

{if ($page.import) }
	<h1>Page loaded</h1>
	<br/><br/>
	<h2>{$page.import}</h2>
	<br/><br/>
{else}

<h1>Processing</h1>

<form  method="post" enctype="multipart/form-data">
<table border="0" cellpadding="0" cellspacing="0" width="60%" class="form" style="margin:0;">

<tr>
	<td>	<h3>Block:</h3>	</td>
</tr>
		{ if ($page.check.blocks) }
			{foreach from=$page.check.blocks key=id item=ext}
				<tr><td>{$ext}</td> <td id="b_{$id}"><a onclick="importCorrect('b','{$id}'); return false" href="#">correct</a></td>
			{/foreach}
		{else if}
			<tr><td>Success</td>
		{/if}
</tr>

<tr>
	<td>	<h3>Block file:</h3>	</td>
</tr>
		{ if ($page.check.blocks_file) }
			{foreach from=$page.check.blocks_file item=ext}
				<tr><td>{$ext}</td>
			{/foreach}
		{else if}
			<tr><td>Success</td>
		{/if}
</tr>

<tr>
	<td>	<h3>Block Field:</h3>	</td>
</tr>
		{ if ($page.check.blocks_fields || $page.check.blocks_fields_type) }
			{foreach from=$page.check.blocks_fields key=id item=ext}
				<tr><td>{$ext}</td> <td id="bf_{$id}"><a onclick="importCorrect('bf','{$id}'); return false" href="#">correct</a></td></tr>
			{/foreach}
			{foreach from=$page.check.blocks_fields_type key=id item=ext}
				<tr><td>{$ext}</td>
			{/foreach}			
		{else if}
			<tr><td>Success</td>
		{/if}
</tr>

{if isset($page.img)}
<tr>
	<td>	<h3>Images:</h3>	</td>
</tr>
		{ if ($page.img) }
			{foreach from=$page.img item=ext}
				<tr><td>{$ext}</td>
			{/foreach}
		{else if}
			<tr><td>Success</td>
		{/if}
</tr>{/if}

<tr>
    <td colspan="2" align="center">
		<input type="hidden" name="load_file" value="{$page.file}"/>	
        <input type="submit" name="validate_page" value="Validate" class="submit" />	
        <input type="submit" name="import_page" value="Import" class="submit" />
    </td>
</tr>

</table>
</form>

{/if}