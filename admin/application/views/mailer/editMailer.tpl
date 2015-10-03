<div class="panel panel-default">
    <div class="panel-heading">
            
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/mailer/list/">List</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/mailer/archive/">Archive</a></li>
			</ul>
            
        <h2 class="panel-title">Edit mailer</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
<form action="" method="post" class="form-horizontal">


    <div class="form-group">
        <label class="col-sm-2 control-label">Table name</label>
        <div class="col-sm-10">
            <input type="text" name="table" class="form-control" value="{$mailer.val.m_table}"/>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">E-mail field name</label>
        <div class="col-sm-10">
        <input type="text" name="field" class="form-control" value="{$mailer.val.m_field_name}"/>    
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Sender's e-mail</label>
        <div class="col-sm-10">
        <input type="text" name="sender" class="form-control" value="{$mailer.val.m_sender}" />    
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Subject</label>
        <div class="col-sm-10">
        <input type="text" name="subject" class="form-control" value="{$mailer.val.m_subject}"/>    
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Text</label>
        <div class="col-sm-10">
        <textarea name="text" class="form-control">{$mailer.val.m_text}</textarea>    
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-primary" value="Update changes " name="editMailer" />
        </div>
    </div>

</form>
</div></div>