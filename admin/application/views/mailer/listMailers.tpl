<div class="panel panel-default">
    <div class="panel-heading">
    
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/mailer/archive/">E-mail Archive</a></li>
			</ul>
            
        <h2 class="panel-title">Mailers List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
<table class="table table-striped">
    <tr>
        <th><strong>Mailer subject:</strong></th>
        <th class="text-center"><small style="color:#55f;">нажав на кнопку "send" дождитесь пока скрипт полностью отработает и страница полностью загрузится</small></th>
    </tr>
    {foreach from=$mailer.mailersList item=mailer}
    <tr>
        <td>{$mailer.m_subject}</td>
        <td class="text-center">
            <a href="{$ADMIN_DIR}/mailer/send/id/{$mailer.m_id}/" class="ctrl" onclick="return (!confirm('Are you sure to send this letter?') ? false : null)">send</a> /
            <a href="{$ADMIN_DIR}/mailer/edit/id/{$mailer.m_id}/" class="ctrl">edit</a> /
            <a href="{$ADMIN_DIR}/mailer/delete/id/{$mailer.m_id}/" class="ctrl" onclick="return (!confirm('Are you sure to delete this mailer?') ? false : null)">delete</a>
        </td>
    </tr>
    {/foreach}
</table>
</div></div>