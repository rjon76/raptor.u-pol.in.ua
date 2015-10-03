<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title">{$purchase.productTitle}</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    <legend>Prices list</legend>

    <div class="text-center" style="background:#eee;padding:10px 0;margin-bottom:5px;">
    <form action="" method="post" class="form-inline">
        <div class="form-group">
        <label>Product operator:</label>
        <select class="form-control" name="operator_id">
            <option value="0"></option>
            {foreach from=$purchase.operatorsList item=operator}
                <option value="{$operator.op_id}" {if $operator.op_id == $purchase.productOperatorId}selected="selected"{/if}>{$operator.op_name}</option>
            {/foreach}
        </select>
        </div>
        <input type="submit" class="btn btn-primary" value="Update" name="updateOperator" />
    </form>
    </div>

{if $purchase.licensesList}

<div class="table-responsive">
<form action="" method="post">
<table class="table table-hover table-middle">
<thead>
    <tr>
        <th><strong>License</strong></th>
        <th class="text-center">USD</th>
        <th class="text-center" style="background:#efefff;">EUR</th>
        <th class="text-center" style="background:#efefff;">GBR</th>
        <th class="text-center" style="background:#efefff;">JPY</th>
        <th class="text-center" style="background:#efefff;">AUD</th>
        <th class="text-center" style="background:#efefff;">CAD</th>
        <th class="text-center" style="background:#efefff;">CNY</th>
        <th class="text-center" style="background:#efefff;">NOK</th>
        <th class="text-center" style="background:#efefff;">SEK</th>
        <th class="text-center" style="background:#efefff;">PLN</th>
        <th class="text-center" style="background:#efefff;">RUB</th>
        <th class="text-center" style="background:#efefff;">CHF</th>
        <th class="text-center"><small>Order</small></th>
        <th class="text-center"><small>Default</small></th>
        <th class="text-center"><small>Blocked</small></th>
        <th class="text-center">&nbsp;</th>
    </tr>
</thead>
<tbody>
    {foreach from=$purchase.licensesList item=license}
    <tr>
        <td><a href="{$ADMIN_DIR}/purchase/license/id/{$license.l_id}/">{$license.l_name}</a></td>
        <td class="text-center"><input type="text" class="form-control" style="width:70px;" value="{$license.l_price}" name="usd_price_{$license.l_id}" /></td>

        {foreach from=$purchase.pricesList[$license.l_id] item=price}
            <td class="text-center" style="background:#f5fDC1;"><a href="javascript:void(0);" onclick="editPrice({$license.l_id}, {$price.pr_curid}, this)">{$price.pr_price}</a></td>
        {/foreach}
        <td class="text-center"><input type="text" class="form-control" style="width:50px;" value="{$license.l_order}" name="order_{$license.l_id}" /></td>
        <td class="text-center"><div class="radio"><label><input type="radio" name="default" value="{$license.l_id}" {if $license.l_default == 'Y'}checked="checked"{/if}/></label></div></td>
        <td class="text-center"><div class="checkbox"><label><input type="checkbox" {if $license.l_blocked == 'Y'}checked="checked"{/if} name="blocked_{$license.l_id}" /></label></div></td>
        <td class="text-center"><a href="{$ADMIN_DIR}/purchase/dellicense/id/{$license.l_id}/" onclick="return (!confirm('Delete?') ? false : null)" class="ctrl"> del </a></td>
    </tr>
    {/foreach}

    <tr class="th">
        <td colspan="17" class="text-right"><input type="submit" value="Update" class="btn btn-primary" name="updatePrices" /></td>
    </tr>
    </tbody>
</table>
</form>
</div>
{else}
Empty.
{/if}