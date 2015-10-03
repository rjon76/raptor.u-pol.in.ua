{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Вы действительно хотите удалить эту запись?");
}
/*]]>*/</script>{/literal}
<fieldset><legend>Изменить <u><strong>{$lvals.language}</strong></u> запись</legend>
    <form method="post" action="{$base_url}/news/save/lang/{$lvals.language}/">
    <div class="form"><input type="submit" style="float:right;margin-bottom:10px;" class="submit" name="ispost" value="Сохранить" /></div>
    {foreach from=$news item=news}
    <div id="feat{$news.id}" class="frow">
    <input type="hidden" name="fid[]" value="{$news.id}" />
    № п/п <input type="text" id="order{$news.id}" name="forder[]" value="{$news.order}" style="width:50px; margin-top:10px"  />
    Заголовок <input type="text" id="title{$news.id}" name="ftitle[]" value="{$news.title}" style="width:450px; margin-top:10px"  /><br/>
    <textarea class="ftextarea" name="ftext[]" cols="20" rows="2">{$news.text}</textarea>
   <a class="fdrop" href="{$base_url}/news/drop/fid/{$news.id}/lang/{$lvals.language}/" onclick="return confirmDrop();">Удалить</a>
    </div>
    {/foreach}
    <div class="form"><input type="submit" style="float:right;" class="submit" name="ispost" value="Сохранить" /></div>
    </form>
</fieldset>