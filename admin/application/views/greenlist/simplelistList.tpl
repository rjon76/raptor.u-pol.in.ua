{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this localized string?");
}
/*]]>*/</script>{/literal}

<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/greenlist/addsimple/">Add record</a></li>
        </ul>
    
        <h2 class="panel-title">Simple Green List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <table id="grlist" class="table table-hover table-striped">
            <thead>
                <tr>
                	<th>Address</th>
                	<th>Headers</th>
                	<th>Direction</th>
                	{if $glVals.canEdit}
                	<th>&nbsp;</th>
                	{/if}
                	{if $glVals.canDelete}
                	<th>&nbsp;</th>
                	{/if}
                </tr>
            </thead>
            <tbody>
            {foreach from=$glist item=str}
            <tr>
        	<td>{$str.address}</td>
        	<td>{foreach from=$str.header item=hd}{$hd}<br />{/foreach}</td>
        	<td>{$str.destination}</td>
        	{if $glVals.canEdit}
        	<td><a href="{$ADMIN_DIR}/greenlist/editsimple/id/{$str.id}/" class="ctrl">edit</a></td>
        	{/if}
        	{if $glVals.canDelete}
        	<td><a href="{$ADMIN_DIR}/greenlist/deletesimple/id/{$str.id}/" class="ctrl" onclick="return confirmDrop();">delete</a></td>
        	{/if}
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>