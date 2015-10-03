<h1>Add page</h1>
<form action="{$ADMIN_DIR}/pages/add/" method="post">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="form" style="margin:0;">
<tr>
<td valign="top">
<fieldset style="margin-bottom:5px;">
<legend>Page data</legend>
<table border="0" cellpadding="5" cellspacing="0" class="form">
<tr>
    <td><strong>Address:</strong>{if $page.err.address}<span style="color:red;">(Page with this address already exist)</span>{/if}<br/>
    <input type="text" name="address" class="text" value="{$page.val.address}"/>
    </td>
</tr>
<tr>
    <td><strong>Title:</strong><br/>
    <input type="text" name="title" class="text"  value="{$page.val.title}" />
    </td>
</tr>

<tr>
    <td><strong>Menu title:</strong><br/>
    <input type="text" name="menu_title" class="text"  value="{$page.val.menu_title}" />
    </td>
</tr>

<tr>
    <td><strong>Parent page:</strong><br/>
    <select name="parent" class="select">
        <option value="0">&nbsp;</option>
    {foreach from=$page.pages item=pg}
        <option value="{$pg.pg_id}" class="{$page.langs[$pg.pg_lang].code}" {if $page.val.parent == $pg.pg_id}selected="selected"{/if}>{$pg.pg_address}</option>
    {/foreach}
    </select>
    </td>
</tr>

<tr>
    <td><strong>Language:</strong><br/>
    <select name="lang" class="select">
    {foreach from=$page.langs item=lang key=id}
        <option value="{$id}" class="{$lang.code}" {if $page.val.lang == $id}selected="selected"{/if}>{$lang.code}</option>
    {/foreach}
    </select>
    </td>
</tr>

<tr>
    <td>    
    
    <fieldset style="margin-bottom:5px;">
    <legend>Relative pages (<a id="selectLocation" href="javascript:void(0);">Select location</a>):</legend>

        
        <legend>All pages</legend>
        
        <select id="relativePagesSource" class="select mselect" name="relative_source[]" multiple="multiple" size="10">
        {foreach from=$page.pages item=pg}
            {if !$pg.selected}
                <option value="{$pg.pg_id}" class="{$page.langs[$pg.pg_lang].code}">{$pg.pg_address}</option>
            {/if}
        {/foreach}
        </select>
       
        
        <legend>Selected pages</legend>

        <select id="relativePagesTarget" class="select mselect" name="relative[]" multiple="multiple" size="6">
        {foreach from=$page.pages item=pg}
            {if $pg.selected}
                <option value="{$pg.pg_id}" class="{$page.langs[$pg.pg_lang].code}">{$pg.pg_address}</option>
            {/if}
        {/foreach}
        </select>

    </fieldset>    
    
    </td>
</tr>


</table>
</fieldset>

</td>
<td valign="top">

<fieldset style="margin-bottom:5px;">
<legend>Custom options</legend>
<table border="0" cellpadding="5" cellspacing="0" class="form">
<tr>
    <td>CSS:<br/>
    <input type="text" name="css" class="text"  value="{$page.val.css}" />
    </td>
</tr>
<tr>
    <td>JavaScript:<br/>
    <input type="text" name="jscript" class="text"  value="{$page.val.jscript}" />
    </td>
</tr>
<tr>
    <td>Priority:<br/>
    <input type="text" name="priority" class="text"  value="{$page.val.priority}" />
    </td>
</tr>
<tr>
    <td style="padding:11px 5px 12px 5px;">
        <input type="checkbox" name="cacheable" {if $page.val.cacheable}checked="checked"{/if}/>
        Cacheable
    </td>
</tr>
<tr>
    <td style="padding:11px 5px 12px 5px;">
        <input type="checkbox" name="hidden" {if $page.val.hidden}checked="checked"{/if}/>
        Hidden
    </td>
</tr>
<tr>
    <td style="padding:11px 5px 12px 5px;">
        <input type="checkbox" name="indexed" {if $page.val.indexed}checked="checked"{/if}/>
        Indexed
    </td>
</tr>
<tr>
    <td>Extensions:<br/>
    <select name="extensions[]" class="mselect" multiple="multiple" size="10">
    {foreach from=$page.exts item=ext}
        <option value="{$ext.id}" {if $ext.selected}selected="selected"{/if}>{$ext.name}</option>
    {/foreach}
    </select>
    </td>
</tr>
<tr>
    <td>Headers:<br/>
    <textarea class="text" name="headers">{$page.val.headers}</textarea>
    </td>
</tr>
</table>
</fieldset>

</td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="submit" name="addpage" value="Add" class="submit" />
    </td>
</tr>
</table>
</form>