{literal}
<script type="text/javascript" language="javascript">
$(document).ready(function(){
    
    function image_reload(obj){
			var src = $(obj).attr('src');
			var pos = src.indexOf('&rnd');
			if (pos > -1){
				src = src.substr(0, pos);
			}
			src = src +'&rnd='+Math.round(Math.random(0)*1000);
			$(obj).attr('src',src);
		}
    
    image_reload('#captchaimg');
    
    
    $('#captchaimg').bind('click', function(){ image_reload(this)});
});
</script>
{/literal}
	<div class="col-xs-12 col-md-4 col-md-offset-4">

		<h2 class="page-header">Authorization</h2>
			
            <form method="post" action="{$ADMIN_DIR}/auth/login/" id="login-form" class="well form">
				
                <fieldset>
	
					<div class="form-group">
                    	<label for="UserLogin_user_email" class="control-label required">Login <span class="required">*</span></label>
                    	<input type="text" name="username" placeholder="" class="input-lg form-control">
                    </div>    
    				<div class="form-group">
                    	<label for="UserLogin_user_password" class="control-label required">Password <span class="required">*</span></label>
                        <input type="password" name="password" placeholder="" class="input-lg form-control">
					</div>	
					<div class="form-group">
						<img id="captchaimg" class="pull-left" src="{$ADMIN_DIR}/captcha.php?c=f5f5f5&f=333" alt="Reload code" title="Click to reload" /> <input type="text" name="captcha" class="form-control input-lg" style="width:190px">
					</div>
					<div class="form-actions">
						<button name="yt0" type="submit" id="yw1" class="btn-block btn-lg btn btn-danger">Sign In</button>
    				</div>
				</fieldset>   
			</form>	
	</div>
