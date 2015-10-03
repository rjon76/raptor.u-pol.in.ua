<h1 style="text-align:center;">{$purchase.productTitle}</h1>
<fieldset>
    <legend>Bundles list</legend>

    {if $purchase.bundlesList}
    <form action="" method="post">
    <table border="0" cellpadding="5" cellspacing="1" class="form" style="margin:0;" width="100%">

    <tr class="th">
        <td><strong>Product</strong></td>
        <td align="center">USD</td>
        <td align="center" style="background:#efefff;">EUR</td>
        <td align="center" style="background:#efefff;">GBR</td>
        <td align="center" style="background:#efefff;">JPY</td>
        <td align="center" style="background:#efefff;">AUD</td>
        <td align="center" style="background:#efefff;">CAD</td>
        <td align="center" style="background:#efefff;">CNY</td>
        <td align="center" style="background:#efefff;">NOK</td>
        <td align="center" style="background:#efefff;">SEK</td>
        <td align="center" style="background:#efefff;">PLN</td>
        <td align="center" style="background:#efefff;">RUB</td>
        <td align="center" style="background:#efefff;">CHF</td>
        <td align="center">&nbsp;</td>
    </tr>

    {foreach from=$purchase.bundlesList item=bundle}
    <tr>
        <td>{$bundle.p_title}</td>
        <td align="center">
            <input type="text" class="text" style="width:70px;" value="{$bundle.bn_price}" name="usd_price_{$bundle.bn_id}" />
        </td>

        {foreach from=$purchase.pricesList[$bundle.bn_id] item=price}
            <td align="center" style="background:#f5fDC1;"><a href="javascript:void(0);" onclick="editBundlePrice({$price.bnp_id}, this)">{$price.bnp_price}</a></td>
        {/foreach}

        <td align="center"><a href="{$ADMIN_DIR}/purchase/delbundle/id/{$bundle.bn_id}/" onclick="return (!confirm('Delete?') ? false : null)" class="ctrl">del</a></td>
    </tr>
    {/foreach}

    <tr class="th">
        <td colspan="14" align="right"><input type="submit" value="Update" class="submit" name="updateBundles" /></td>
    </tr>
    </table>
    </form>
    {else}
    Empty.
    {/if}
</fieldset>