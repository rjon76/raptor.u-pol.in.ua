{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this localized string?");
}
/*]]>*/</script>{/literal}

<div class="panel panel-default">
	<div class="panel-heading">
        
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/greenlist/addext/">Add record</a></li>
        </ul>
    
        <h2 class="panel-title">Extended Green List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <table id="grlist" class="table table-hover table-striped">
            <thead>
                <tr>
            		<th>Expression</th>
            		<th>Regular</th>
            		<th>Headers</th>
            		<th>Direction</th>
            		<th>Order</th>
            		{if $glVals.canEdit}<th>&nbsp;</th>{/if}
            		{if $glVals.canDelete}<th>&nbsp;</th>{/if}
                </tr>
            </thead>
            <tbody>
            {foreach from=$glist item=str}
                <tr>
            		<td>
            			{if $str.regular == "0"}
            				{if $glVals.canEdit}
            					<a href="{$ADMIN_DIR}/greenlist/editext/id/{$str.id}/" style="color: #000055; text-decoration:none;">{$str.expression}</a>
            				{/if}
            			{else}
            				{if $glVals.canEdit}
            					<a href="{$ADMIN_DIR}/greenlist/editext/id/{$str.id}/"  style="color: #005500; text-decoration:none;" >{$str.expression}</a>
            				{/if}
            			{/if}
            		</td>
            		<td>
            			{if $str.regular == "0"}<img src="{$ADMIN_DIR}/images/unchecked.gif" width="13" height="13" alt="" />
            			{else}<img src="{$ADMIN_DIR}/images/checked.gif" width="13" height="13" alt="" />
            			{/if}
            		</td>
            		<td>{foreach from=$str.header item=hd}{$hd}<br />{/foreach}</td>
            		<td>{$str.destination}</td>
            		<td>{$str.order}</td>
            		{if $glVals.canEdit}
            		<td><a href="{$ADMIN_DIR}/greenlist/editext/id/{$str.id}/" class="ctrl">edit</a></td>
            		{/if}
            		{if $glVals.canDelete}
            		<td><a href="{$ADMIN_DIR}/greenlist/deletext/id/{$str.id}/" class="ctrl" onclick="return confirmDrop();">delete</a></td>
            		{/if}
            	</tr>
        	{/foreach}
            </tbody>
        </table>
    </div>
</div>