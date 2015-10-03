<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new User</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
<form action="{$ADMIN_DIR}/users/list/" method="post" class="form-horizontal">
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Username</label>
        	<div class="col-sm-10">
        	   <input type="text" name="login" class="form-control" value="{$users.val.login}" />
               <span class="help-block">
               {if $users.err.login}<span style="color:red;">(username is empty)</span><br />{/if}
               {if $users.err.loginExist}<span style="color:red;">(username is already exist)</span>{/if}
               </span>
        	</div>
        </div>

        
        <div class="form-group">
       	    <label class="col-sm-2 control-label">Password</label>
           	<div class="col-sm-10">
                <input type="password" name="passwd" class="form-control" />
                <span class="help-block">
                {if $users.err.passwd}<span style="color:red;">(password is empty)</span><br />{/if}
                {if $users.err.rep}<span style="color:red;">(password and confirmation are not matching)</span>{/if}
               </span>
           	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Confirm password</label>
        	<div class="col-sm-10">
        	   <input type="password" name="rep_passwd" class="form-control" />
        	</div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Group</label>
            <div class="col-sm-10">
                <select name="group" class="form-control">
                    {html_options options=$users.groupsList selected=$users.val.group}
                </select>
            </div>
        </div>
        
        <div class="form-group">
           	<div class="col-sm-offset-2 col-sm-10">
                <input type="submit" class="btn btn-primary" value="Add" name="user" />
           	</div>
        </div>
</form>
    </div>
</div>
