{if $model->hasErrors()}
<div class="error">
	{$model->printErrors() assign="errors"}
	{$errors}
</div>    
{/if}
<fieldset>
<legend>New Case Study</legend>
<form action="" method="post">
<table border="0" cellpadding="6" cellspacing="1" class="form">
<tr>
    <td {if $model->getError('cs_autor')}class="error"{/if}>
        * Autor:<br/>
		<textarea class="textarea" name="cs_autor"  rows="2" style="height:80px">{$content.val.cs_autor}</textarea>
    </td>
</tr>
<tr>
    <td {if $model->getError('cs_text')}class="error"{/if}>
        * Text:<br/>
        <textarea class="textarea" name="cs_text">{$content.val.cs_text}</textarea>
    </td>
</tr>
<tr>
    <td {if $model->getError('cs_lang_id')}class="error"{/if}>
        Language:<br/>
        <select class="select" name="cs_lang_id">
            <option value="">&nbsp;</option>
            {foreach from=$content.langs item=item}
            <option value="{$item.l_id}" {if $content.val.cs_lang_id == $item.l_id} selected="selected"{/if}>{$item.l_code}</option>
            {/foreach}
        </select>
    </td>
</tr>
<tr>
    <td {if $model->getError('cs_link')}class="error"{/if}>
        Link:<br/>
        <input type="text" name="cs_link" class="text" value="{$content.val.cs_link}" />
    </td>
</tr>
<tr>
    <td>
        Hidden:<br/>
        <input type="checkbox" name="cs_hidden"  {if $content.val.cs_hidden} checked="checked"{/if} />
    </td>
</tr>
<tr>
    <td>Pages not view:<br/>
    <select name="cs_pages_not_view[]" class="mselect" multiple="multiple" size="10">
    {foreach from=$content.pages item=pg}
        <option value="{$pg.pg_id}" class="{$content.langs[$pg.pg_lang].l_code}" {if $pg.selected}selected="selected"{/if}>{$pg.pg_address}</option>
    {/foreach}
    </select>
    </td>
</tr>
<tr>
    <td><input type="submit" class="submit" value="Add" name="addItem" /></td>
</tr>

</table>
</form>
</fieldset>