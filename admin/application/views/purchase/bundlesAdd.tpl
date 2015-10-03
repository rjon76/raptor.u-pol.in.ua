<fieldset>
    <legend>Add new bundle</legend>

    <form action="" method="post">
    <table border="0" cellpadding="5" cellspacing="1" class="form">
        <tr>
            <td>
                Product license:<br/>

                <select name="license" class="mselect" size="15" style="width:400px;">

                {foreach from=$purchase.productsList.prods item=category key=cat_name}
                {foreach from=$category item=prod}
                {if $purchase.licensesList[$prod.p_id]}
                    <optgroup label="{$prod.p_title}">
                    {foreach from=$purchase.licensesList[$prod.p_id] item=license}
                    <option value="{$license.l_id}">{$license.l_name}</option>
                    {/foreach}
                    </optgroup>
                {/if}
                {/foreach}
                {/foreach}

                </select>
            </td>
        </tr>
        <tr>
            <td>
                Price:<br/>
                <input type="text" name="price" class="text" />
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="Add" name="addBundle" class="submit" />
            </td>
        </tr>
    </table>
    </form>
</fieldset>