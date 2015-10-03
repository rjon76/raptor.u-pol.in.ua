<h1>Add Green list record</h1>
{if $lvals.postRes == 1}
<p>New Green List record was added successfully.</p>
{elseif $lvals.postRes == 0}
<p>Error occured while inserting new records. Some fields are empty or incorrectly filled in.</p>
{/if}
<fieldset><legend>Simple Green List</legend>
<form action="" method="post">
<table border="0" cellpadding="0" cellspacing="0" class="form">
    <tr>
        <td>
            * Address:<br/>
            <input type="text" name="address" id="gl_addr" class="text" value="{$lform.address}" />
        </td>
    </tr>
    <tr>
        <td>
            * Header:<br/>
            <select name="header" class="select" onchange="check404(this);" onkeyup="check404(this);">
		{html_options values=$lform.header.values selected=$lform.header.select output=$lform.header.output}
	    </select>
        </td>
    </tr>
    <tr>
        <td>
            * Destination:<br/>
            <input type="text" name="destination" id="gl_dest" class="text" value="{$lform.destination}" />
        </td>
    </tr>
    <tr>
        <td>
            <input type="submit" class="submit" value="Add" name="lform" />
        </td>
    </tr>
</table>
</form>
</fieldset>
{literal}<script type= "text/javascript">/*<![CDATA[*/
var gl_addr = new LiveValidation('gl_addr', { validMessage: "Ok!", wait: 500 } );
gl_addr.add(Validate.Presence, {failureMessage: "Searched address is not specified!"});
gl_addr.add( Validate.Exclusion, { within: [ 'http://'], partialMatch: true, failureMessage: "Only relative addresses are allowed!" } );

var gl_dest = new LiveValidation('gl_dest', { validMessage: "Ok!", wait: 500 } );
gl_dest.add(Validate.Presence, {failureMessage: "Destination address is not specified!"});
/*gl_dest.add( Validate.Inclusion, { within: [ 'http://'], partialMatch: true, failureMessage: "Only absolute addresses are allowed!" } );*/

function check404(sel) {
    var el = document.getElementById("gl_dest");
    if(typeof(el) != "undefined" ) {
	if(sel.value == "{/literal}{$lvals.is404}{literal}") {
	    el.disabled = true;
	    el.value = "{/literal}{$lvals.addr404}{literal}"
	}
	else {
	    el.disabled = false;
	    el.value = "{/literal}{$lform.destination}{literal}"
	}
    }
}
/*]]>*/</script>{/literal}