<h1>Import page</h1>
<form action="" method="post" enctype="multipart/form-data">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form" style="margin:0;">
<tr>
<td valign="top">
<fieldset style="margin-bottom:5px;">
	<legend>Select file</legend>
	File:	<input type="file" name="import_file" class="text" />
	
	{if $err}
		<br/>
		{foreach from=$err item=ext}
				{$ext}<br/>
		{/foreach}
	{/if}
	
</fieldset>
</td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="submit" name="loadpage" value="Upload" class="submit" /> 
    </td>
</tr>
</table>
</form>