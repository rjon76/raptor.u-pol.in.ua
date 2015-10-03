{if $model->hasErrors()}
<div class="error">
	{$model->printErrors() assign="errors"}
	{$errors}
</div>    
{/if}
<fieldset>
<legend>Biuld download link</legend>
<form action="" method="post">
<table border="0" cellpadding="6" cellspacing="1" class="form">
<tr>
    <td {if $model->getError('dl_download_dir')}class="error"{/if}>
        * Parent dir :<br/>
         <input type="text" name="dl_download_dir" class="text" value="{$model->dl_download_dir}" style="width:700px"  />
    </td>
</tr>

<tr>
    <td {if $model->getError('dl_download_link')}class="error"{/if}>
        * Link to bild (relative to parent dir):<br/>
         <input type="text" name="dl_download_link" class="text" value="{$model->dl_download_link}" style="width:700px"  />
    </td>
</tr>
<tr>
    <td {if $model->getError('dl_date_expired')}class="error"{/if}>
        * Date expired:<br/>
         <input type="text" name="dl_date_expired" class="text" style="width:200px;" id="dl_date_expired" value="{$model->dl_date_expired}" />
                {literal}
                <script type="text/javascript">
                    $.datepicker.setDefaults({
                        showOn: 'both',
                        buttonImageOnly: true,
                        buttonImage: admin_dir+'/images/calendar.gif',
                        buttonText: 'Calendar'
                    });

                    $('#dl_date_expired').datepicker({dateFormat: 'yy-mm-dd'});
                </script>
                {/literal}
    </td>
</tr>
{if $model->dl_link_expired}
<tr>
    <td >
        Link:<br/>
       <textarea class="textarea" name="dl_link_expired"  rows="2" style="height:80px; width:700px">{$model->dl_link_expired}</textarea>
    </td>
</tr>
<tr>
    <td >
       <a href="{$model->dl_link_expired}" target="_blank">Test link</a>
    </td>
</tr>
{/if}
<tr>
    <td><input type="submit" class="submit" value="Generate link" name="submit" /></td>
</tr>

</table>
</form>
</fieldset>