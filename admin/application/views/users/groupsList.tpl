<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Groups List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<table class="table table-hover table-condensed">
<thead><tr><th>Group name</th><th class="text-right">Options</th></tr></thead>
{foreach from=$users.groupsList item=group key=groupId}
    <tr>
        <td>{$group}</td>
        <td class="text-right">
            <a href="{$ADMIN_DIR}/users/gedit/id/{$groupId}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/users/gdelete/id/{$groupId}/" class="ctrl">delete</a>
        </td>
    </tr>
{/foreach}
</table>
    </div>
</div>