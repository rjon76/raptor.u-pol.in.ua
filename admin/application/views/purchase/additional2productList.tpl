<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">{$purchase.productTitle} / Additional offers list</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        <form action="" method="post">
        <div class="table-responsive">
        <table class="table table-hover">
        <thead>
            <tr>
                <th>Text</th>
                <th class="text-center" style="background:#dedeff;">USD</th>
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
                <th class="text-right">Options</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$purchase.prices item=prices key=afId}
            <tr>
                <td><a href="{$ADMIN_DIR}/purchase/editadditional/id/{$afId}/">{$purchase.features[$afId].af_text}</a></td>
                <td style="background:#f5fDC1;" class="text-center">{$purchase.features[$afId].af_default_price}</td>
                {foreach from=$prices item=price}
                <td style="background:#f5fDC1;" class="text-center"><a href="javascript:void(0);" onclick="editOfferPrice({$price.afp_id}, this)">{$price.afp_price}</a></td>
                {/foreach}
                <td class="text-right"><a href="{$ADMIN_DIR}/purchase/deladditional2product/id/{$afId}/pid/{$price.afp_pid}/" class="ctrl">del</a></td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
        </form>
    </div>
</div>