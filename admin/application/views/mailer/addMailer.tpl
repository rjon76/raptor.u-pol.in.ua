<div class="panel panel-default">
    <div class="panel-heading">

        <h2 class="panel-title">Add mailer</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<form action="" method="post" class="form-horizontal">

    <div class="form-group">
        <label class="col-sm-2 control-label">Table name: <small style="color:#55f;">(имя таблицы в которой храняться мыла)</small></label>
        <div class="col-sm-10">
        <input type="text" name="table" class="form-control" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">E-mail field name: <small style="color:#55f;">(имя поля в таблице хранящее мыло)</small></label>
        <div class="col-sm-10">
        <input type="text" name="field" class="form-control" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Sender's e-mail: <small style="color:#55f;">(мыло отправителя)</small></label>
        <div class="col-sm-10">
        <input type="text" name="sender" class="form-control" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Subject</label>
        <div class="col-sm-10">
        <input type="text" name="subject" class="form-control" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Text</label>
        <div class="col-sm-10">
        <textarea name="text" class="form-control"></textarea>
        </div>
    </div>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        <input type="submit" class="btn btn-primary" value="Add" name="addMailer" />
        </div>
    </div>

</form>
</div></div>