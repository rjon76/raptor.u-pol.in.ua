<div class="panel panel-default">
    <div class="panel-heading">
    
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/mailer/list/">List</a></li>
			</ul>
            
        <h2 class="panel-title">E-mail Archive</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<table class="table table-striped">
    <tr>
        <th>Receiver:</th>
    </tr>
    {foreach from=$archive.receivers item=receiver}
    <tr>
        <td><a class="btn btn-link" href="{$ADMIN_DIR}/mailer/archive/receiver/{$receiver}/">{$receiver}</a></td>
    </tr>
    {/foreach}
</table>


{if $archive.emails}

    <legend>E-mails for {$archive.receiver} (
        <select class="form-control" onchange="document.location = '{$ADMIN_DIR}/mailer/archive/receiver/{$archive.receiver}/date/' + $(this).val() + '/';">
            {foreach from=$archive.dates item=date}
            <option value="{$date}" {if $archive.date == $date}selected="selected"{/if}>{$date}</option>
            {/foreach}
        </select>)</legend>
<table class="table table-striped">
    <tr>
        <th>Subject:</th>
    </tr>
    {foreach from=$archive.emails item=email}
    <tr>
        <td><a href="{$ADMIN_DIR}/mailer/showemail/id/{$email.ea_id}/">{$email.ea_subject}</a></td>
    </tr>
    {/foreach}
</table>

{/if}
</div></div>