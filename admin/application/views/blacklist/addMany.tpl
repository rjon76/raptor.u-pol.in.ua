<h1>Add Key to Blacklist</h1>

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
        * Key numbers: <br/>
        
        <textarea name="bl_name" class="text" cols="300" rows="20" style="width:950px; font-size:14px">{$content.val.bl_name}</textarea>
    </td>
</tr>
<tr>
    <td>
        <input type="submit" class="submit" value="Add" name="add" />
    </td>
</tr>
</table>
</form>
</fieldset>