<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new block</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
<form action="" method="post" enctype="multipart/form-data">

<div class="col-xs-12 col-sm-6">
    <div class="form-group">
    	<label>Name</label>
        <input type="text" class="form-control" name="name" />
     </div>

    <div class="form-group">
    	<label>File</label>
        <input type="text" class="form-control" name="file" />
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
         <input id="btnSearch" type="button" class="btn btn-default" value="Search"/> 
         <input id="btnReset" type="reset" class="btn btn-default" value="Reset"/>
     </div>

    <div class="form-group">
      <label></label>                    
          <select id="selectMselect" class="form-control" name="parent[]" multiple="multiple" size="10">
              {foreach from=$content.blocks item=block}
              <option value="{$block.b_id}">{$block.b_name}</option>
              {/foreach}
          </select>
    </div>
            
    <input type="submit" value="Add new" class="btn btn-primary" name="addBlock" />

</div>
<div class="col-xs-12 col-sm-6">
    <div class="form-group">
    	<label>Description</label>
        <textarea class="form-control" name="text"></textarea>
     </div>
            
    <div class="form-group">
    	<label>Image (not more 100 Kb)</label>
        <input type="file" name="userfile" value="" />
     </div>

</div>
</form>
    </div>
</div>