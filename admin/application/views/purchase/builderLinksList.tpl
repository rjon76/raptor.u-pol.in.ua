<fieldset>
    <legend>Links</legend>

    <table border="0" cellpadding="5" cellspacing="1" class="form">
        <tr>
            <td style="background:#ccc;" align="center" width="50">#</td>
            <td style="background:#ccc;" width="300" align="center">Address</td>
           <!-- <td style="background:#ccc;" width="600" >Link</td>-->            
            <td style="background:#ccc;" width="80"></td>
        </tr>
        {foreach from=$builder.linksList item=link}
        <tr>
            <td>{$link.link_id}</td>
            <td align="center"><a href="{$curSite}/buynow/{$link.link_address}/">{$link.link_address}</td>
           <!-- <td >https://www.plimus.com/jsp/buynow.jsp?{$link.link_data}</td>-->
            <td><a href="{$ADMIN_DIR}/purchase/deletelink/id/{$link.link_id}/" class="ctrl">delete</a></td>
        </tr>
        {/foreach}
    </table>

</fieldset>