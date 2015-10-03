<div class="panel panel-default">
    <div class="panel-heading">
                <ul class="nav nav-pills pull-right">
              	<li role="presentation"><a href="{$ADMIN_DIR}/pages/add/">Pages new</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/list/">Pages list</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/clone/">Page cloner</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/import/">Page import</a></li>

			</ul>
        <h2 class="panel-title">Import page</h2>
        <div class="clearfix"></div>
    </div>
    
    <div class="panel-body">
    
        {if $err}
            <div class="alert alert-danger" role="alert">{foreach from=$err item=ext}{$ext}<br/>{/foreach}</div>
        {/if}
    
        <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
            <fieldset>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="file">File input</label>
                <div class="col-sm-10">
            	   <input id="file" type="file" name="import_file" placeholder="*.xml file"/>
                </div>
            </div>   
            
            <div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
      				<div class="checkbox">
        				<label>
          					<input type="checkbox" value="1" name="checkFiles" checked="checked" />Check files exists (css/js)
        				</label>
      				</div>
    			</div>
  			</div>
            
            </fieldset>
            
            <div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
                <input type="submit" name="loadpage" value="Upload" class="btn btn-primary" /> 
    			</div>
  			</div>

        </form>
    </div>
    
</div>