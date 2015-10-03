<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Coupons list</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

    <div class="rightCtrl" style="text-align:center;background:#eee;padding:10px 0;">
    <form action="" method="post" id="sortCouponsForm" class="form-inline">
    
    	<input type="hidden" value="true" name="sortCoupons" />
    	<input type="hidden" value="ccc" name="by" id="by" />
        <div class="form-group">
        	<label>Sort by product:</label>       
            <select name="by_product" onchange="couponSortSubmit('by_product','sortCouponsForm');" class="form-control">
            <option value="-1">All</option>
            {foreach from=$purchase.productsList.prods item=category key=cat_name}
            <optgroup label="{$cat_name}">
                {foreach from=$category item=prod}
                	{if $purchase.couponProductSelected eq $prod.p_id}
                		<option selected="selected" value="{$prod.p_id}">{$prod.p_title}</option>
                	{else}
                		<option value="{$prod.p_id}">{$prod.p_title}</option>
                	{/if}
                {/foreach}
            </optgroup>
            {/foreach}
        </select>
         </div>
        
        <div class="form-group">
        	<label>Sort by license:</label>        
            
            <select name="by_license" onchange="couponSortSubmit('by_license','sortCouponsForm');" class="form-control">
            <option value="-1">All</option>
            {foreach from=$purchase.productsList.prods item=category key=cat_name}
            {foreach from=$category item=prod}
            {if $purchase.licensesList[$prod.p_id]}
                <optgroup label="{$prod.p_title}">
                {foreach from=$purchase.licensesList[$prod.p_id] item=license}
                   	{if $purchase.couponLicenseSelected eq $license.l_id}
		                <option selected="selected" value="{$license.l_id}">{$license.l_name}</option>
		            {else}
                		<option value="{$license.l_id}">{$license.l_name}</option>
                	{/if}
                {/foreach}
                </optgroup>
            {/if}
            {/foreach}
            {/foreach}
        </select>
         </div>
         
    </form>
    </div>
{if count($purchase.couponsList)}
    <form action="" method="post">
    <div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Code</th>
            <th class="text-center"><strong>Operator</strong></th>
            <th class="text-center"><strong>Discount</strong></th>
            <th class="text-center"><strong>End date</strong></th>
            <th class="text-center"><strong>Blocked</strong></th>
            <th class="text-right">Options</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$purchase.couponsList item=coupon}
        <tr>
            <td>{$coupon.cup_name}</td>
            <td>{$coupon.cup_code}</td>
            <td class="text-center">{$purchase.operatorsList[$coupon.cup_opid].op_name}</td>
            <td class="text-center">{$coupon.cup_percent}</td>
            <td class="text-center">{$coupon.cup_date}</td>
            <td class="text-center">
            {if $coupon.cup_blocked eq "N"}
            	<input src="{$ADMIN_DIR}/images/unchecked.gif" class="pointer" onclick="blockCoupon({$coupon.cup_id},this);return false;" type="image">
            {else}
            	<input src="{$ADMIN_DIR}/images/checked.gif" class="pointer" onclick="blockCoupon({$coupon.cup_id},this);return false;" type="image">
            {/if}
            </td>
            <td class="text-right">
                <a href="{$ADMIN_DIR}/purchase/editcoupon/id/{$coupon.cup_id}/" class="ctrl">edit</a> |
                <a href="{$ADMIN_DIR}/purchase/delcoupon/id/{$coupon.cup_id}/" class="ctrl" onclick="return verifyDelete();">del</a>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    </form>
    {else}
    <div class="alert alert-info" role="alert" style="margin:25px 0;">Sorry, there is no data to display.</div>
    {/if}
    </div>
</div>
{literal}
<script language="javascript" type="text/javascript">
function verifyDelete() {
    if (confirm("Are you sure you want to delete this coupon?")) {
       return true;
    } else {
       return false;
    }
}
</script>
{/literal}