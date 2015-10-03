
    <legend>Operators 2 licenses ids</legend>

    {if $purchase.operator2license}
    <form action="" method="post">
    <table class="table table-hover table-middle">
        {foreach from=$purchase.operator2license item=category key=operatorId}

        <tr>
            <th colspan="6" class="text-center" style="background:#ccc;padding:10px 0;">Operator: <strong>{$purchase.operatorsList[$operatorId].op_name}</strong></th>
        </tr>
        <tr>
            <th style="background:#ddd;" class="text-center">License</th>
            <th style="background:#ddd;" class="text-center">Currerncy</th>
            <th style="background:#ddd;" class="text-center">ID</th>
            <th style="background:#ddd;" class="text-center">Default</th>
            <th style="background:#ddd;" class="text-center">Blocked</th>
            <th style="background:#ddd;">&nbsp;</th>
        </tr>

        {foreach from=$category item=row}
        <tr>
            <td style="padding-left:10px;">{$purchase.licensesList[$row.oi_lid].l_name}</td>
            <td class="text-center">{$purchase.currenciesList[$row.oi_curid].c_code}</td>
            <td class="text-center"><input type="text" name="priceid_{$row.oi_id}" value="{$row.oi_price_id}" class="form-control" /></td>
            <td class="text-center"><input type="radio" name="default" value="{$row.oi_id}" {if $row.oi_default == 'Y'}checked="checked"{/if} /></td>
            <td class="text-center"><input type="checkbox" name="blocked_{$row.oi_id}" {if $row.oi_blocked == 'Y'}checked="checked"{/if} /></td>
            <td class="text-center"><a href="{$ADMIN_DIR}/purchase/deloli/id/{$row.oi_id}/" onclick="return (!confirm('Delete?') ? false : null)" class="ctrl">del</a></td>
        </tr>
        {/foreach}
        {/foreach}
        <tr>
            <td class="text-right" colspan="7"><input type="submit" value="Update" class="btn btn-primary" name="updateOperators" /></td>
        </tr>
    </table>
    </form>
    {else}
    Empty.
    {/if}

    <legend>Add operator 2 license id</legend>

    <form class="form-horizontal" action="" method="post">

                <div class="form-group">
                	<label class="col-sm-2 control-label">License</label>
                	<div class="col-sm-10">
                        <select class="form-control" name="license">
                            {foreach from=$purchase.licensesList item=license}
                            <option value="{$license.l_id}">{$license.l_name}</option>
                            {/foreach}
                        </select>
                    </div>
                 </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Currency</label>
                	<div class="col-sm-10">
                        <select class="form-control" name="currency">
                            {foreach from=$purchase.currenciesList item=currency}
                            <option value="{$currency.c_id}" {if $currency.c_id == 12}selected="selected"{/if}>{$currency.c_code}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>


                <div class="form-group">
                	<label class="col-sm-2 control-label">Operator</label>
                	<div class="col-sm-10">
                        <select class="form-control" name="operatorId">
                            {foreach from=$purchase.operatorsList item=operator}
                            <option value="{$operator.op_id}" {if $operator.op_default == 'Y'}selected="selected"{/if}>{$operator.op_name}</option>
                            {/foreach}
                        </select>
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">ID</label>
                	<div class="col-sm-10">
                	<input type="text" class="form-control" name="priceId" />
                	</div>
                </div>

                <div class="form-group">
                	<div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label><input type="checkbox" name="default" /> is default</label>
                        </div>
                	</div>
                </div>
                
                <div class="form-group">
                	<div class="col-sm-offset-2 col-sm-10">
                	<input type="submit" value="Add" class="btn btn-primary" name="addOperator" />
                	</div>
                </div>
    </form>
