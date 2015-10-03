<div class="panel panel-default">
	<div class="panel-heading">
    
    <ul class="nav nav-pills pull-right">
    	<li role="presentation"><a href="{$ADMIN_DIR}/products/changelog/id/114/">List</a></li>
    </ul>
    
        <h2 class="panel-title">{$prodinfo.p_title} / Edit Changelog</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

   	<legend>Version {$prodinfo.p_version}  / language -
    		{if $lvals.lang == 1}English{/if}
    		{if $lvals.lang == 2}French{/if}
    		{if $lvals.lang == 3}Deutsch{/if}</legend>
    	<form class="form-horizontal" method="POST" action="{$ADMIN_DIR}/products/changelogsave/">
    		<input type="hidden" name="pid" 	value="{$prodinfo.p_id}" />
    		<input type="hidden" name="pbid" 	value="{$otherinfo.buildid}" />
    		<input type="hidden" name="lang" 	value="{$lvals.lang}" />
			<input type="hidden" name="chId" 	value="{$chlogdata.pc_id}" />
            
            <div class="form-group">
            	<label class="col-sm-2 control-label">Build number</label>
            	<div class="col-sm-6">
             	  <input class="form-control" type="text" value="{$chlogdata.pb_build}" name="ChLogNum" size="5"  />
            	</div>
            </div>
            
            <div class="form-group">
            	<label class="col-sm-2 control-label">Changelog</label>
            	<div class="col-sm-10">
                    <textarea class="form-control" name="chItem" style="height:300px;">{$chlogdata.pc_text}</textarea>
            	</div>
            </div>

            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" value="Update changes" class="btn btn-primary"/>
            	</div>
            </div>
    	</form>
    </div>
</div>

