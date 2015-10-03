<div class="panel panel-default">
    <div class="panel-heading">  
        <h2 class="panel-title">Products</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
{foreach from=$productsList.prods item=category key=cat_name}

<legend>{$cat_name}</legend>

<table class="table table-hover">
<thead>
    <tr>
        <th>Product name</th>
        <th width="260" class="text-center">Edit</th>
    </tr>
</thead>
<tbody>
    {foreach from=$category item=prod}
    <tr>
        <td>{$prod.p_title}</td>
        <td align="right">
            <a href="{$ADMIN_DIR}/purchase/prices/id/{$prod.p_id}/" class="ctrl">prices</a> |
            <a href="{$ADMIN_DIR}/purchase/bundles/id/{$prod.p_id}/" class="ctrl">bundles</a> |
            <a href="{$ADMIN_DIR}/purchase/additional2product/id/{$prod.p_id}/" class="ctrl">additional offers</a>
        </td>
    </tr>
    {/foreach}
</tbody>
</table>
{/foreach}
</div></div>