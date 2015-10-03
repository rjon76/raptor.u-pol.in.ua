<h1>Новости</h1>
{if $lvals.saved == "1"}<p>Все запись сохранены успешно.</p>
{elseif $lvals.saved == "2"}<p>Новая запись добавлена.</p>{/if}
<fieldset><legend>Новая <u><strong>{$lvals.language}</strong></u> запись</legend>
    <form method="post" action="{$base_url}/news/add/lang/{$lvals.language}/">
	<label for="aforder">№ п/п</label> <input type="text" name="aforder" value="" style="width:50px"/>
    <label for="aftitle">Заголовок </label> <input type="text" name="aftitle" value=""  style="width:450px" /><br/>
	<textarea class="ftextarea" name="aftext" cols="20" rows="2"></textarea>
	<div class="form" style="float:right;margin-top:25px;"><input type="submit" class="submit" name="isadd" value="Добавить" /></div>
    </form>
</fieldset>