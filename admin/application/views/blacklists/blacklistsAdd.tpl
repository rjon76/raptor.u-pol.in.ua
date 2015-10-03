<div class="panel panel-default">
    	<div class="panel-heading">
     
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/blacklists/list/">List</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/blacklists/generate/">Generate file</a></li>
			</ul>
        
            <h2 class="panel-title">Add IP</h2>
            <div class="clearfix"></div>
		</div>
        <div class="panel-body">
<form action="" method="post" class="form-horizontal">
<fieldset>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="ips">* Ip adress: {if $conts.err.ips}<span style="color:red;">(ip is empty)</span>{/if}</label>
            	<div class="col-xs-10 col-md-6">
                    <textarea name="ips" class="form-control">{$val.ips}</textarea>
                </div>
             </div> 

            <div class="form-group">
                <label class="col-sm-2 control-label" for="ips">Not use for site</label>
                <div class="col-xs-10 col-md-6">
               		<select name="site_ids[]" class="form-control" multiple="multiple">
    				{html_options options=$sitesList selected=$val.site_ids}
    				</select>
                </div>
            </div> 
</fieldset>
            <div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Add" name="ispost" />
    			</div>
  			</div>
            
            


</form>


</div>
</div>