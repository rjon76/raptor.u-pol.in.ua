<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Operators list</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $operators.operatorsList}
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$operators.operatorsList item=operator}
            <tr>
                <td>{$operator.op_name}</td>
                <td class="text-right">
                    <a href="{$ADMIN_DIR}/purchase/operator/id/{$operator.op_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/purchase/deloperator/id/{$operator.op_id}/" onclick="return (!confirm('Delete?') ? false : null)" class="ctrl">delete</a>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        {else}
        <div class="alert alert-info" role="alert" style="margin:25px 0;">Sorry, there is no data to display.</div>
        {/if}
        
    </div>
</div>