<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Users List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

{foreach from=$users.usersList.users item=groupUsers key=groupId}
    <legend>{$users.usersList.groups[$groupId]}</legend>
    <table class="table table-hover">
    <thead><tr><th>Login</th><th class="text-right">Options</th></tr></thead>
    <tbody>
    {foreach from=$groupUsers item=user key=userId}
    <tr>
        <td>{$user}</td>
        <td class="text-right">
            <a href="{$ADMIN_DIR}/users/edit/id/{$userId}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/users/delete/id/{$userId}/" class="ctrl">delete</a>
        </td>
    </tr>
    {/foreach}
    </tbody>
    </table>
{/foreach}

    </div>
</div>