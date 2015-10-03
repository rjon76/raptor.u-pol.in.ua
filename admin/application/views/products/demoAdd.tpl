<h1>{$lvals.title}</h1>
{if $lvals.saved=="1"}<p>All records were saved succesfully.</p>
{elseif $lvals.saved=="2"}<p>New record was added succesfully.</p>{/if}
<fieldset><legend>Add <u><strong>{$lvals.language}</strong></u> demolimitation</legend>
    <form method="post" action="{$ADMIN_DIR}/products/demoadd/pid/{$lvals.product}/lang/{$lvals.language}/">
	<input type="text" name="adorder" value="" class="forder1" />
	<textarea class="ftextarea" name="adtext" cols="20" rows="2"></textarea>
	<div class="form" style="float:right;margin-top:25px;"><input type="submit" class="submit" name="isadd" value="Add" /></div>
    </form>
</fieldset>