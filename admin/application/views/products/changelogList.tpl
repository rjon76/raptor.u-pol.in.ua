	<div class="panel panel-default">
    	<div class="panel-heading">
        	<div class="pull-right">
            <form  class="form-inline">
             <div class="form-group">
    			<label for="inputEmail3" class="control-label">Language:</label>
    			
                    <select class="form-control" onchange="window.location = '{$ADMIN_DIR}/products/changelog/id/{$prodinfo.p_id}/lang/' + this.options[this.selectedIndex].value + '/'">
                        <option value="1" {if $lvals.lang == 1}selected="selected"{/if}>EN</option>
                        <option value="2" {if $lvals.lang == 2}selected="selected"{/if}>FR</option>
                        <option value="3" {if $lvals.lang == 3}selected="selected"{/if}>DE</option>
                    </select>
                        			
			</div>
			</form>
            </div>
       
       <h2 class="panel-title">{$prodinfo.p_title} / Changelog / language -
    		{if $lvals.lang == 1}English{/if}
    		{if $lvals.lang == 2}French{/if}
    		{if $lvals.lang == 3}Deutsch{/if}</h2>
       <div class="clearfix"></div>
	</div>
    <div class="panel-body">   


    	<legend>Add Changelog</legend>

    		<form class="form-horizontal" method="POST" action="{$ADMIN_DIR}/products/changelognew/">
    			<input type="hidden" name="pid" value="{$prodinfo.p_id}" />
    			<input type="hidden" name="lang" value="{$lvals.lang}" />
                <div class="form-group">
                	<label class="col-sm-2 control-label">Build number</label>
                	<div class="col-sm-6">
                 	  <input class="form-control" type="text" value="" name="newChLogNum" size="5" />
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Changelog item</label>
                	<div class="col-sm-10">
                 	  <textarea class="form-control" name="chItem" style="height:300px"></textarea>
                	</div>
                </div>

                <div class="form-group">
                	<div class="col-sm-offset-2 col-sm-10">
                	   <input type="submit" value="Add new" class="btn btn-primary"/>
                	</div>
                </div>

    		</form>



    	{foreach from=$chlogdata.builds key=buildnum item=chlog}

    			<legend>{$buildnum}</legend>

    			<table class="table table-hover">
    				<tr>
    					<td style="width:80%">
    						<ul class="list-group">
    							{foreach from=$chlog.chlog key=num item=chdata}
    								<li class="list-group-item">{$chdata.text}</li>
    							{/foreach}
    						</ul>
    					</td>
    					<td>
    						<a href="{$ADMIN_DIR}/products/changelogedit/build/{$chdata.build}/id/{$prodinfo.p_id}/lang/{$lvals.lang}/" class="ctrl">edit</a><br/>
    						<a href="{$ADMIN_DIR}/products/changelogsend/build/{$chdata.build}/id/{$prodinfo.p_id}/lang/{$lvals.lang}/" class="ctrl">send</a><br/>
    						<a href="{$ADMIN_DIR}/products/changelogdel/build/{$chdata.build}/id/{$prodinfo.p_id}/" onclick="return confirm('Are you sure you want to delete?')" class="ctrl" style="color:#f30000;">delete</a><br/>
    						<br/>
    						{if $chlog.pc_blocked == 0}
    							<input class="pointer" src="{$ADMIN_DIR}/images/checked.gif" onclick="activeDeactiveChangelog('{$chdata.build}', this,{$lvals.lang});return false;" type="image"/>
    							<span id="active{$chdata.build}" style="font-weight: bold; color: Green;"> active</span>
   							{else}
   								<input class="pointer" src="{$ADMIN_DIR}/images/unchecked.gif" onclick="activeDeactiveChangelog('{$chdata.build}', this,{$lvals.lang});return false;" type="image"/>
   								<span id="active{$chdata.build}" style="font-weight: bold; color: Grey"> active</span>
    						{/if}


    					</td>
    				</tr>

    			</table>

    	{/foreach}
</div></div>
