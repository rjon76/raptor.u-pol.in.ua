{literal}<script type= "text/javascript">/*<![CDATA[*/
{/literal}{foreach from=$products.cat item=cname}
{literal}var cat{/literal}{$cname}{literal} = false;
{/literal}{/foreach}{literal}
/*]]>*/</script>{/literal}

<div class="panel panel-default">
	<div class="panel-heading">
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/products/add/">Add product</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/products/platforms/">Platform list</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/products/os/">OS list</a></li>
        </ul>
    
        <h2 class="panel-title">Products List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
    
 
    {foreach from=$products.prods item=category key=cat_name}
    <div class="table-responsive">
    <legend><a id="ln{$products.cat[$cat_name]}" class="phide" href="javascript:void();" onclick="return tabs('{$products.cat[$cat_name]}');">{$cat_name}</a></legend>
        <table id="cat{$products.cat[$cat_name]}" class="table table-hover">
        
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product name</th>
                    <th></th>
                    <th></th>
                    {if $lvals.canEdit || $lvals.canDelete}
                    <th class="text-center">Options</th>
                    {/if}
                    {foreach from=$products.langs item=lang}
                    <th class="text-center"><img src="{$ADMIN_DIR}/images/{$lang.l_code}.png" alt="{$lang.l_name}" title="{$lang.l_name}" /></th>
                    {/foreach}
                    <th class="text-center">Order</th>
                </tr>
            
            </thead>
            <tbody>
            {foreach from=$category item=prod}
            
            <tr id="prod{$prod.p_id}" {if $prod.p_blocked=="1"} class="danger"{/if}>
                <td>{$prod.p_id}</td>
                <td>{if $prod.p_featured=="1"}<span>{$prod.p_title|escape:"html"}</span>{else}{$prod.p_title|escape:"html"}{/if}</td>
                <td><a href="{$ADMIN_DIR}/products/changelog/id/{$prod.p_id}/" class="ctrl" style="font-size:11px;">change log</a></td>
                <td><a href="{$ADMIN_DIR}/products/export/id/{$prod.p_id}/" class="ctrl" style="font-size:11px;" onclick="return (!confirm('Export?') ? false : null)">export new data to old database</a></td>
                
                {if $lvals.canEdit || $lvals.canDelete}
                <td class="text-center">
                {if $lvals.canEdit}
                 	<a href="{$ADMIN_DIR}/products/edit/id/{$prod.p_id}/" class="ctrl">edit</a>
                {/if}
                 {if $lvals.canDelete}
                    / <a href="{$ADMIN_DIR}/products/delete/id/{$prod.p_id}/" class="ctrl" onclick="return confirm('Do You really want to delete this product?');">del</a>
                 {/if}
                 </td>
                 {/if}
                 
                {foreach from=$products.langs item=lang}
                <td class="{$lang.l_code} text-center">
                    <a href="{$ADMIN_DIR}/products/featuredit/id/{$prod.p_id}/lang/{$lang.l_code}/">features</a><br/>
                    <a href="{$ADMIN_DIR}/products/demoedit/id/{$prod.p_id}/lang/{$lang.l_code}/">demolimits</a>
                </td>
                {/foreach}
                
                <td class="text-center">
                <img src="{$ADMIN_DIR}/images/2up.gif" width="20" height="20" alt="Product up" class="pointer" onclick="upProduct({$prod.cat_id},{$prod.p_id});" /><br />
                <img src="{$ADMIN_DIR}/images/2down.gif" width="20" height="20" alt="Product down" class="pointer" onclick="downProduct({$prod.cat_id},{$prod.p_id});" />
                </td>
            </tr>
            
            {/foreach}
            </tbody>
        </table>
        </div>
    {/foreach}   
 
    </div>
</div>