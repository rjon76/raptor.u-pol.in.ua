<h1 style="text-align:center;">{$license.productTitle}</h1>
<div class="h1select" style="padding:7px 7px 0 0;">
    <a href="/purchase/prices/id/{$license.data.l_pid}/"><- Back</a>
</div>
<fieldset>
    <legend>{$license.data.l_name}</legend>

    <form action="" method="post">
    <table border="0" cellpadding="5" cellspacing="1" class="form">
        <tr>
            <td>
                Max user number:<br/>
                <input type="text" name="usernumber" class="text" value="{$license.data.l_usernumber}" style="width:100px;" />
            </td>
        </tr>
        <tr>
            <td>
                Min user number:<br/>
                <input type="text" name="min_usernumber" class="text" value="{$license.data.l_min_usernumber}" style="width:100px;" />
            </td>
        </tr>
        <tr>
            <td>
                Parent License:<br/>
                <select class="select" name="parent">
                    <option></option>
                {foreach from=$license.licensesList item=lic}
                    <option value="{$lic.l_id}" {if $lic.l_id == $license.data.l_parentid}selected="selected"{/if}>{$lic.l_name}</option>
                {/foreach}
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Type:<br/>
                <select class="select" name="type">
                    <option value="H" {if $license.data.l_type == 'H'}selected="selected"{/if}>Home</option>
                    <option value="B" {if $license.data.l_type == 'B'}selected="selected"{/if}>Business</option>
                    <option value="S" {if $license.data.l_type == 'S'}selected="selected"{/if}>Single License</option>
                    <option value="SL" {if $license.data.l_type == 'SL'}selected="selected"{/if}>Single License [for 1 Computer]</option>
                    <option value="C" {if $license.data.l_type == 'C'}selected="selected"{/if}>Company License</option>
                    <option value="FN" {if $license.data.l_type == 'FN'}selected="selected"{/if}>For non-commercial use</option>
                    <option value="FC" {if $license.data.l_type == 'FC'}selected="selected"{/if}>For commercial use</option>
                    <option value="FE" {if $license.data.l_type == 'FE'}selected="selected"{/if}>For end users</option>
                    <option value="LP" {if $license.data.l_type == 'LP'}selected="selected"{/if}>License packs</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                Name:<br/>
                <input type="text" name="name" class="text" value="{$license.data.l_name}" />
            </td>
        </tr>
        <tr>
            <td>
                Wiki link:<br/>
                <input type="text" name="wiki_link" class="text" value="{$license.data.l_wiki_link}" />
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" name="mailus" {if $license.data.l_mailus == 'Y'}checked="checked"{/if}" /> Mail us
            </td>
        </tr>
        <tr>
            <td><input type="submit" value="Update" class="submit" /></td>
        </tr>
    </table>
    </form>
</fieldset>