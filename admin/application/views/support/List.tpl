<h1>Support managers</h1>

<fieldset>
<table border="0" cellpadding="6" cellspacing="1" class="form">
<tr>
    <td><strong>ID</strong></td>
    <td><strong>Login</strong></td>
    <td><strong>Nik</strong></td>
    <td><strong>Chat id</strong></td>        
    <td></td>
</tr>
{foreach from=$support item=item}
<tr>
    <td>{$item.sm_id}</td>
    <td>{$item.sm_login}</td>
    <td>{$item.sm_nik}</td>
    <td>{$item.sm_chat_id}</td>
    <td>
        {if $lvals.canEdit}<a href="/support/edit/id/{$item.sm_id}/" class="ctrl">edit</a>{/if}
        {if $lvals.canEdit && $lvals.canDelete} | {/if}
        {if $lvals.canDelete}<a href="/support/delete/id/{$item.sm_id}/" onclick="return confirm('Do You really want to delete this record?');" class="ctrl">delete</a>{/if}
    </td>
</tr>
{/foreach}
</table>
</fieldset>