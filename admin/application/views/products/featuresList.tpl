{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this feature?");
}
/*]]>*/</script>{/literal}
<fieldset><legend>Edit <u><strong>{$lvals.language}</strong></u> features</legend>
    <form method="post" action="{$ADMIN_DIR}/products/featuresave/pid/{$lvals.product}/lang/{$lvals.language}/">
    <div class="form"><input type="submit" style="float:right;margin-bottom:10px;" class="submit" name="ispost" value="Save" /></div>
    {foreach from=$features item=feat}
    <div id="feat{$feat.id}" class="frow">
    <input type="hidden" name="fid[]" value="{$feat.id}" />
    <input type="text" id="order{$feat.id}" name="forder[]" value="{$feat.order}" class="forder1" />
    <textarea class="ftextarea" name="ftext[]" cols="20" rows="2">{$feat.text}</textarea>
    <input id="fp{$feat.id}" name="fpromo[]" class="fpromo" type="checkbox"{if $feat.promo=="1"} checked="checked"{/if} value="{$feat.id}" /><label for="fp{$feat.id}" class="flabel">Is promo</label>
    <a class="fdrop" href="{$ADMIN_DIR}/products/featuredrop/pid/{$lvals.product}/fid/{$feat.id}/lang/{$lvals.language}/" onclick="return confirmDrop();">Drop</a>
    </div>
    {/foreach}
    <div class="form"><input type="submit" style="float:right;" class="submit" name="ispost" value="Save" /></div>
    </form>
</fieldset>