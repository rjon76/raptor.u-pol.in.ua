{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this localized string?");
}
/*]]>*/</script>{/literal}
<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/localstring/add/">Add new</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/localstring/search/">Search localization</a></li>
        </ul>
        
        <h2 class="panel-title">Localized Strings</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    

<form action="" method="post" name="list">
		<div id="hint"></div>
			<div id="wCont" align="center">
				<div id="editw" align="center"></div>
				
			</div>
            <div class="table-responsive">
			<table id="lstrlist" class="table table-hover table-condensed table-bordered">
				<thead>
					<tr>
						<th width="15%">Nick</th>
    					{foreach from=$langs item=item key=key}
						<th class="text-center">{$item.code}</th>
    					{/foreach}
                        {if $lvals.canDelete}
	    				<th class="text-center" width="5%">Options</th>
                        {/if}
    				</tr>
				</thead>
                <tbody>
    			{foreach from=$lstrings item=str}
    			<tr id="row_{$str.id}">
					<td>
						<a href="javascript:editNickWindow({$str.id})">{if $str.nick|count_characters:true eq 0}click to edit...{else}{$str.nick|escape:"html"|truncate:100:"...":true}{/if}</a>
					</td>
					{foreach from=$langs item=item key=key}
        			{assign var="text" value="text_`$item.code`"}
        			{assign var="code" value="isT_`$item.code`"}
					<td {if $key!==1} class="transl{if $str.$code eq 0}_none{else}_yes{/if}"{/if}>
						<a href="javascript:editWindow('{$item.code}',{$str.id})">{if $str.$text|count_characters:true eq '0'}click to edit...{else}{$str.$text|escape:"html"|truncate:20:"...":true}{/if}</a>
					</td>
        			{/foreach}
					{if $lvals.canDelete}
					<td class="text-center">
						<a href="{$ADMIN_DIR}/localstring/delete/lang/en/id/{$str.id}/{if $lvals.page > 1}page/{$lvals.page}/{/if}" class="ctrl" onclick="return deleteLocalstring({$str.id});"><img src="{$ADMIN_DIR}/images/delete.gif" border="0" /></a>
					</td>
					{/if}
    			</tr>
    			{/foreach}
                </tbody>
			</table>
            </div>
	</form>
    </div>
</div>