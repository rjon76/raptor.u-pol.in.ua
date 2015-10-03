<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Block's fields</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    

{if $content.fields}
<table class="table table-hover table-condensed">
<thead>
<tr>
    <th>Name</th>
    <th class="text-center">Type</th>
    <th class="text-right">Options</th>
</tr>
</thead>
<tbody>
{foreach from=$content.fields item=field}
<tr>
    <td>{$field.bf_name}</td>
    <td class="text-center">{$field.bf_type}</td>
    <td class="text-right">
        <a href="{$ADMIN_DIR}/content/editfield/bid/{$field.bf_block_id}/fid/{$field.bf_id}/" class="ctrl">edit</a> |
        <a href="{$ADMIN_DIR}/content/deletefield/bid/{$field.bf_block_id}/fid/{$field.bf_id}/" class="ctrl">delete</a>
    </td>
</tr>
{/foreach}
</tbody>
</table>
{else}

    <div class="alert alert-info" role="alert">No fields for this block.</div>

{/if}
    </div>
</div>