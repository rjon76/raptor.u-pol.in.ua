<h1>{$lvals.title}</h1>
{if $lvals.saved == "1"}<p>All records were saved succesfully.</p>
{elseif $lvals.saved == "2"}<p>New record was added succesfully.</p>{/if}
<fieldset><legend>Add <u><strong>{$lvals.language}</strong></u> feature</legend>
    <form method="post" action="{$ADMIN_DIR}/products/featureadd/pid/{$lvals.product}/lang/{$lvals.language}/">
	<input type="text" name="aforder" value="" class="forder1" />
	<textarea class="ftextarea" name="aftext" cols="20" rows="2"></textarea>
	<input id="fp0" name="afpromo" class="fpromo" type="checkbox"{if $feat.promo == "1"} checked="checked"{/if} value="1" /><label for="fp0" class="flabel">Is promo</label>
	<div class="form" style="float:right;margin-top:25px;"><input type="submit" class="submit" name="isadd" value="Add" /></div>
    </form>
</fieldset>