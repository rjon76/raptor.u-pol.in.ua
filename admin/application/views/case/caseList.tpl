{literal}
<script language="javascript">
$(function() {
$("#cs_lang_id").bind('change', function(){document.location.href = BASE_URL+'/case/list/lang/'+$(this).val();
										 })
});
</script>


{/literal}

<h1><div class="h1select">
<label for="cs_lang_id">Languages:</label>
<select class="select" name="cs_lang_id" id="cs_lang_id">
            <option value="">All</option>
            {foreach from=$content.langslist item=lang}
            <option value="{$lang.l_id}" {if $content.lang == $lang.l_id} selected="selected"{/if}>{$lang.l_code}</option>
            {/foreach}
        </select>
</div>Case study</h1>

<fieldset>

<table border="0" cellpadding="6" cellspacing="1" class="form">
<tr>
    <th>ID</th>
    <th>Text</th>
    <th>Autor</th>
    <th>Lang</th>
    <th>Hidden</th>
    <th width="100"></th>
</tr>
{foreach from=$content.cases item=item}
<tr>
    <td>{$item.cs_id}</td>
    <td>{$item.cs_text|truncate:100:true}></td>
    <td>{$item.cs_autor}</td>
    <td>{$item.lang}</td>
    <td>{if $item.cs_hidden=="1"}+{/if}</td>   
    <td>
        <a href="{$ADMIN_DIR}/case/edit/id/{$item.cs_id}/{if $content.lang!==''}lang/{$item.cs_lang_id}{/if}" class="ctrl">Edit</a> |
        <a href="{$ADMIN_DIR}/case/delete/id/{$item.cs_id}/{if $content.lang!==''}lang/{$item.cs_lang_id}{/if}" onclick="{literal}if(!confirm('Do You really want to delete this item?')) return false;{/literal}" class="ctrl">delete</a>
    </td>
</tr>
{/foreach}
</table>
</fieldset>