<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Controller 2 Site relations</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<legend>Related sites</legend>
{if $conts.relsList}
<table class="table table-hover table-striped">
    <thead>
    <tr>
        <th>Sites</th>
        <th class="text-right">Options</th>
    </tr>
    </thead>
    <tbody>
    {foreach from=$conts.relsList item=rel}
    <tr>
        <td>{$rel.s_hostname}</td>
        <td class="text-right"><a href="{$ADMIN_DIR}/controllers/delrel/cont/{$rel.sc_controller_id}/site/{$rel.sc_site_id}/" class="ctrl">delete</a></td>
    </tr>
    {/foreach}
    </tbody>
</table>
{else}
<div class="alert alert-info" role="alert">No any related sites.</div>
{/if}


<legend>Add relation</legend>
{if $conts.sitesList}
<form action="" method="post" class="form-inline">

    <div class="form-group">
    	<label>Sites</label>
            <select name="site" class="form-control">
            {html_options options=$conts.sitesList}
            </select>
     </div>

    <input type="submit" class="btn btn-primary" value="Edit" name="reladd" />

</form>
{else}
<div class="alert alert-info" role="alert">All sites added to this controller.</div>
{/if}
    </div>
</div>