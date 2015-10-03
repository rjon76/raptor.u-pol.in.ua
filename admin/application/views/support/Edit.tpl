<h1>{if $lvals.isNewRecord}New operation system{else}{$support.sm_login}{/if}</h1>
{if $lvals.postRes == 1}
<p>Record <strong>{$support.sm_login}</strong> was saved successfully.</p>
{elseif $lvals.postRes == 0}
<p>Error occured while saving record. Some fields are empty or incorrectly filled in.</p>
{/if}
<fieldset>
<form action="" method="post">
 <table border="0" cellpadding="0" cellspacing="0" class="form" style="margin:0 0 0 200px;">
   {if !$lvals.isNewRecord}
    <tr>
		<td>ID:</td><td><strong>{$support.sm_id}</strong>
                <input type="hidden" id="sm_id" name="sm_id" value="{$support.sm_id}" />

        </td>
    </tr>
    {/if}
    <tr>
	<td>Login *:</td><td><input type="text" id="sm_login" name="sm_login" class="text" value="{$support.sm_login}" /></td>
    </tr>
    <tr>
	<td>Nik *:</td><td><input type="text" id="sm_nik" name="sm_nik" class="text" value="{$support.sm_nik}" /></td>
    </tr>
    <tr>
	<td>Chat id :</td><td><input type="text" id="sm_chat_id" name="sm_chat_id" class="text" value="{$support.sm_chat_id}" /></td>
    </tr>
	
<tr>
    <td>Products support:</td>
	<td>
		<select name="products_support[]" class="mselect" multiple="multiple" size="10">
		{foreach from=$products.prods  key=k item=product}
			<option  disabled="disabled" >{$k}</option>

			{foreach from=$product key=p_id item=product}
				<option value="{$p_id}" {if $product.select}selected="selected"{/if} >&nbsp&nbsp&nbsp{$product.p_title}</option>
			{/foreach}
			
		{/foreach}
		</select>
    </td>
</tr>
	
    <tr>
    <tr><td>&nbsp;</td><td>
    {if $lvals.canEdit}
	    <input type="submit" name="ispost" class="submit" value="{if $lvals.isNewRecord}Add{else}Save{/if}" />    
    {/if}
    </tr>
 </table>
</form>
</fieldset>
{literal}<script type= "text/javascript">/*<![CDATA[*/
var sm_login = new LiveValidation('sm_login', { validMessage: "Ok!", wait: 500 } );
sm_login.add(Validate.Presence, {failureMessage: "Login is not specified!"});

var sm_nik = new LiveValidation('sm_nik', { validMessage: "Ok!", wait: 500 } );
sm_nik.add(Validate.Presence, {failureMessage: "Nik is not specified!"});

var sm_chat_id = new LiveValidation('sm_chat_id', { validMessage: "Ok!", wait: 500 } );
sm_chat_id.add( Validate.Numericality, { onlyInteger: true } );

/*]]>*/</script>{/literal}