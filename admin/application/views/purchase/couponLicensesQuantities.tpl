<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Licenses' quantities</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

    <form action="" method="post" class="form-horizontal">
    <table class="table table-hover">
    <thead>
        <tr>
            <th>License</th>
            <th colspan="2" class="text-left">Quantity</th>
        </tr>
        <tr>
        <th></th>
            <th class="text-left">min</th>
            <th class="text-left">max</th>
        </tr></thead>
        <tbody>
        {foreach from=$purchase.productsList.prods item=category key=cat_name}
        {foreach from=$category item=prod}
        {foreach from=$purchase.licensesList[$prod.p_id] item=license}
        {if $purchase.val.cup_validlic[$license.l_id]}
        <tr>
            <td>[{$prod.p_title}] {$license.l_name}</td>
            <td class="text-left"><input type="text" class="form-control" style="width:60px;" value="{if $purchase.val.cup_quantity[$license.l_id].min}{$purchase.val.cup_quantity[$license.l_id].min}{else}0{/if}" name="minqnt_{$license.l_id}" /></td>
            <td class="text-left"><input type="text" class="form-control" style="width:60px;" value="{if $purchase.val.cup_quantity[$license.l_id].max}{$purchase.val.cup_quantity[$license.l_id].max}{else}0{/if}" name="maxqnt_{$license.l_id}" /></td>
        </tr>
        {/if}
        {/foreach}
        {/foreach}
        {/foreach}
        </tbody>
    </table>
    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    	   <input type="submit" class="btn btn-primary" value="Edit changes" name="editQuantities" />
    	</div>
    </div>
    </form>
    </div>
</div>