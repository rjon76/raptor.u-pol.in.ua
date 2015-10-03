<h1>Add IP</h1>

<fieldset>
<legend>Ip data</legend>

<form action="" method="post">
<table border="0" cellpadding="0" cellspacing=="0" class="form">
    <tr>
        <td>
            * Ip adress: {if $conts.err.ips}<span style="color:red;">(ip is empty)</span>{/if}
            <br/>
			<input type="text" class="text" value="{$val.ips}" name="ips" />
        </td>
    </tr>
    <tr>
        <td>
            Not use for site:
            <br/>
           		<select name="site_ids[]" class="select" multiple="multiple">
				{html_options options=$sitesList selected=$val.site_ids}
				</select>
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" class="submit" value="Save" name="ispost" />
        </td>
    </tr>
</table>
</form>

</fieldset>