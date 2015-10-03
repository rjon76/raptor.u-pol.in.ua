{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this feature?");
}
/*]]>*/</script>{/literal}
<fieldset><legend>Edit <u><strong>{$lvals.language}</strong></u> demolimits</legend>
    <form method="post" action="{$ADMIN_DIR}/products/demosave/pid/{$lvals.product}/lang/{$lvals.language}/">
    <div class="form"><input type="submit" style="float:right;margin-bottom:10px;" class="submit" name="ispost" value="Save" /></div>
    {foreach from=$demolimits item=demo}
    <div id="dem{$demo.id}" class="frow">
    <input type="hidden" name="did[]" value="{$demo.id}" />
    <input type="text" name="dorder[]" value="{$demo.order}" class="forder1" />
    <textarea class="ftextarea" name="dtext[]" cols="20" rows="2">{$demo.text}</textarea>
    <a class="fdrop" href="{$ADMIN_DIR}/products/demodrop/pid/{$lvals.product}/did/{$demo.id}/lang/{$lvals.language}/" onclick="return confirmDrop();">Drop</a>
    </div>
    {/foreach}
    <div class="form"><input type="submit" style="float:right;" class="submit" name="ispost" value="Save" /></div>
    </form>
</fieldset>