<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Additional offers list</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

    {if $purchase.featuresList}
    <table class="table table-hover">
    <thead>
        <tr>
            <th rowspan="2"><strong>Text</strong></th>
            <th class="text-center" colspan="3"><strong>Contract ID</strong></th>
            <th class="text-center" rowspan="2"><strong>Percent from total price</strong></th>
            <th class="text-center" rowspan="2"><strong>Price</strong></th>
            <th class="text-center" rowspan="2"><strong>Options</strong></th>
        </tr>
        <tr>
            <th class="text-center"><small>Element5</small></th>
            <th class="text-center"><small>Plimus</small></th>
            <th class="text-center"><small>CleverBrige</small></th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$purchase.featuresList item=feature}
        <tr>
            <td>{$feature.af_text}</td>
            <td class="text-center">{$purchase.featuresContractIds[$feature.af_id][2].ac_contract_id}</td>
            <td class="text-center">{$purchase.featuresContractIds[$feature.af_id][3].ac_contract_id}</td>
            <td class="text-center">{$purchase.featuresContractIds[$feature.af_id][5].ac_contract_id}</td>
            <td class="text-center">{$feature.af_price_percent}</td>
            <td class="text-center">{$feature.af_default_price}</td>
            <td class="text-center">
                <a href="{$ADMIN_DIR}/purchase/editadditional/id/{$feature.af_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/purchase/deladditional/id/{$feature.af_id}/" onclick="return (!confirm('Delete?') ? false : null)" class="ctrl">delete</a>
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