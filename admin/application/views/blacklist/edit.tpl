<h1>Edit Blacklist Key</h1>
{if $model->hasErrors()}
<div class="error">
	{$model->printErrors() assign="errors"}
	{$errors}
</div>    
{/if}


<fieldset>
<legend>Key data</legend>
<form action="" method="post">
<table border="0" cellpadding="6" cellspacing="1" class="form">
<tr>
    <td {if $model->getError('bl_name')}class="error"{/if}>
        * Key number: <br/>
        <input type="text" class="text" name="bl_name" value="{$content.val.bl_name}" style="width:950px" />
    </td>
</tr>
<tr>
    <td {if $model->getError('bl_count')}class="error"{/if}>
        Attempt: <br/>
        <input type="text" class="text" name="bl_count" value="{$content.val.bl_count}" />
    </td>
</tr>
<tr>
    <td>
        Hidden:<br/>
        <input type="checkbox" name="bl_hidden" {if $content.val.bl_hidden} checked="checked"{/if}  />
    </td>
</tr>
<tr>
    <td>
         <input type="submit" class="submit" value="Update" name="update" />
    </td>
</tr>
</table>
</form>
</fieldset>