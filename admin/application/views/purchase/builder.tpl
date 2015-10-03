{literal}
<script type="text/javascript">
function addQuantityInput(select) {

    var quantities = $('#quantities');
    var value = $(select).val();
    quantities.empty();
    for(var i in value) {

        var block = $(document.createElement('div'));
        var quantityInput = $(document.createElement('input'));
        var upButton = $(document.createElement('a'));
        var downButton = $(document.createElement('a'));

        block.append('<br/>' + $('#lic_' + value[i]).html() + ' (')
             .attr('id', value[i]);

        upButton.attr('href', 'javascript:void(0);')
                .attr('class', 'ctrl')
                .html('up')
                .appendTo(block);

        block.append('/');

        downButton.attr('href', 'javascript:void(0);')
                  .attr('class', 'ctrl')
                  .html('down')
                  .appendTo(block);

        block.append(')<br/>');

        quantityInput.attr('type', 'text')
                     .attr('name', 'license_qnt[' + value[i] + ']')
                     .attr('class', 'text')
                     .css('width', 60)
                     .val(1)
                     .appendTo(block);

        upButton.click(function() {
            var blk = $(this).parent();
            var nextBlock = blk.prev();
            var newBlock = blk.clone(true);

            newBlock.css('display', 'none');

            blk.fadeOut('slow', function() {
                blk.remove();
                newBlock.insertBefore(nextBlock);
                newBlock.fadeIn('slow');
            });
        });

        downButton.click(function(elem) {
            var blk = $(this).parent();
            var nextBlock = blk.next();
            var newBlock = blk.clone(true);

            newBlock.css('display', 'none');

            blk.fadeOut('slow', function() {
                blk.remove();
                newBlock.insertAfter(nextBlock);
                newBlock.fadeIn('slow');
            });
        });

        quantities.append(block);
    }
}
</script>
{/literal}
<h1>Plimus Links Builder</h1>

<fieldset>
    <legend>Link options</legend>

    <form action="" method="post">
    <table border="0" cellpadding="5" cellspacing="1" class="form">
        <tr>
            <td width="400">
                Address:<br/>
                <input type="text" name="address" class="text" style="width:350px;{if $builder.errors.address}border-color:red;{/if}" value="{$builder.val.address}"/>
            </td>
            <td rowspan="9" width="350">
                License:<br/>
                <select name="licenses[]" class="mselect" multiple="multiple" size="25" style="width:335px;" onclick="addQuantityInput(this);">
                    {foreach from=$builder.productsList.prods item=category key=cat_name}
                    {foreach from=$category item=prod}
                    {if $builder.licensesList[$prod.p_id]}
                        <optgroup label="{$prod.p_title}">
                        {foreach from=$builder.licensesList[$prod.p_id] item=license}
                        {if $license.l_price > 0}
                        <option value="{$license.l_id}" id="lic_{$license.l_id}" {if $license.l_parentid}disabled="disabled"{/if}>{$license.l_name} USD {$license.l_price}</option>
                        {/if}
                        {/foreach}
                        </optgroup>
                    {/if}
                    {/foreach}
                    {/foreach}

                </select>
            </td>
            <td rowspan="9" id="quantities" valign="top">
            </td>
        </tr>
        <tr>
            <td>
                Language:<br/>
                <select class="select" name="language">
                    <option value="en" {if $builder.val.language == 'en'}selected="selected"{/if}>English</option>
                    <option value="fr" {if $builder.val.language == 'fr'}selected="selected"{/if}>French</option>
                    <option value="de" {if $builder.val.language == 'de'}selected="selected"{/if}>German</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Currency:<br/>
                <select class="select" name="currency">
                    {foreach from=$builder.currenciesList item=currency}
                    <option value="{$currency.c_id}" {if $builder.val.currency == $currency.c_id}selected="selected"{/if}>{$currency.c_code}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Coupon:<br/>
                <select class="select" name="coupon">
                    <option></option>
                {foreach from=$builder.couponsList item=coupon}
                    <option value="{$coupon.cup_id}" {if $builder.val.coupon == $coupon.cup_id}selected="selected"{/if}>{$coupon.cup_name}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Theme:<br/>
                <select class="select" name="theme">
                    <option value="661554" {if $builder.val.theme == '661554'}selected="selected"{/if}>Light</option>
                    <option value="617036" {if $builder.val.theme == '617036'}selected="selected"{/if}>Dark</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="backup_cd" {if $builder.val.backup_cd}checked="checked"{/if} />
                Backup CD
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="lifetime_upgrades" {if $builder.val.lifetime_upgrades}checked="checked"{/if} />
                Lifetime upgrades (30% form total price)
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="priority_email_support" {if $builder.val.priority_email_support}checked="checked"{/if} />
                Priority e-mail support
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="premium_tech_support" {if $builder.val.premium_tech_support}checked="checked"{/if} />
                Premium tech support package (50% or
                <input type="text" class="text" name="premium_tech_support_price" style="width:80px;"/> )
            </td>
        </tr>
        <tr>
            <td valign="bottom">
                <input type="submit" value="Add" class="submit" name="addOperator" />
            </td>
        </tr>
    </table>
    </form>
</fieldset>