<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Edit block</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        <form action="" method="post" enctype="multipart/form-data">
        
        <div class="col-xs-12 col-sm-6">
            
            <div class="form-group">
            	<label>Name</label>
                <input type="text" class="form-control" name="name" value="{$content.val.name}" />
             </div>
             
             <div class="form-group">
             	<label>File</label>
                 <input type="text" class="form-control" name="file" value="{$content.val.file}" />
              </div>
               <div class="form-group">
              	<label>Parent blocks</label>
                </div>
                
               <div class="form-group">
                <label class="checkbox-inline"><input id="checkSearch" type="checkbox" checked="checked" disabled="disabled"/>all at first </label>
                <label class="checkbox-inline"><input id="checkLike" type="checkbox"/>any match</label> 
                <label class="checkbox-inline"><input id="checkUnselect" type="checkbox"/>unselect matches</label>
               </div>
                
                <div class="form-group">
                	<label></label>
                    <input placeholder="Search & select" id="inputSearch" type="text" class="form-control text-search"/> <br />
                    <input id="btnSearch" type="button" class="btn btn-default" value="Search"/> <input id="btnReset" type="reset" class="btn btn-default" value="Reset"/>
                 </div>
                 <div class="form-group">
                 	<label></label>                    
                     <select id="selectMselect" class="form-control" name="parent[]" multiple="multiple" size="10">
                        {foreach from=$content.blocks item=block}
                        <option value="{$block.b_id}"{if in_array($block.b_id,$content.val.parent)} selected="selected"{/if}>{$block.b_name}</option>
                        {/foreach}
                    </select>
                  </div>


                    <input type="submit" class="btn btn-primary" value="Update changes" name="updateBlock" />
        
        
        </div>
        <div class="col-xs-12 col-sm-6">
            
            <div class="form-group">
            	<label>Description</label>
                <textarea class="form-control" name="text" rows="10">{$content.val.text}</textarea>
             </div>
             
             <div class="form-group">
             	<label>Image (not more 100 Kb)</label>
                 <input type="file" name="userfile" value="" />
              </div>
                    
              {if $content.val.base64 != ""}
              <div class="form-group">
              	<label>Image</label>
                  <img class="base64" src="{$content.val.base64}"/> 
               </div>
              {/if}
        
        </div>
        <div class="clearfix"></div>

        </form>
    </div>
</div>