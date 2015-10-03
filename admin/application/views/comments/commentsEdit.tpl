{literal}
<script type="text/javascript">

if (window.jQuery) {
    
    var $182 = jQuery.noConflict(); 
    
    $(document).ready(function() {
    
        (function ($) {   
            $182(".form #selectPages, .form #selectProducts").select2();
            
            if ($182('#comment_author').val() == ""){
                $182('#comment_author').val($182('#comment_avatar option').eq(0).text());    
            }
            
            $182('#comment_avatar option').bind('click', function(){
                
                $182('#comment_author').val($182(this).text());     
                
            });
            
        })(jQuery);
      
    });
}


</script>
{/literal}

<h1>{if $lvals.isNewRecord}New comment{else}Edit comment{/if}</h1>
{if $lvals.postRes == 1}
<p>Comment <strong>{$comment.comment_text}</strong> was saved successfully.</p>
{elseif $lvals.postRes == 0}
<p>Error occured while saving record. Some fields are empty or incorrectly filled in.</p>
{/if}
<fieldset>
<form action="" method="post">
 <table class="form table" style="margin-left:200px;">
    <tr>
	<td></td><td><input type="hidden" id="comment_author" name="comment_author" value="{$comment.comment_author}" maxlength="64"/></td>
    </tr>
    <tr>
	<td>Author:</td>
    <td>
    <select id="comment_avatar" name="comment_avatar" class="select mselect">
    <option class="avatars avatarDefault" value="avatarDefault"{if $comment.comment_avatar == "avatarDefault"} selected="selected"{/if}>Default</option>
    <option class="avatars avatar1" value="avatar1"{if $comment.comment_avatar == "avatar1"} selected="selected"{/if}>Mike Breed</option>
    <option class="avatars avatar2" value="avatar2"{if $comment.comment_avatar == "avatar2"} selected="selected"{/if}>Ben Markton</option>
    <option class="avatars avatar3" value="avatar3"{if $comment.comment_avatar == "avatar3"} selected="selected"{/if}>Terry Conord</option>
    <option class="avatars avatar4" value="avatar4"{if $comment.comment_avatar == "avatar4"} selected="selected"{/if}>Javier</option>
    <option class="avatars avatar5" value="avatar5"{if $comment.comment_avatar == "avatar5"} selected="selected"{/if}>Milan Lebeda, Web animation industry</option>
    <option class="avatars avatar6" value="avatar6"{if $comment.comment_avatar == "avatar6"} selected="selected"{/if}>Bennet Williams</option>
    <option class="avatars avatar7" value="avatar7"{if $comment.comment_avatar == "avatar7"} selected="selected"{/if}>Hannah Carry</option>
    <option class="avatars avatar8" value="avatar8"{if $comment.comment_avatar == "avatar8"} selected="selected"{/if}>Max Rian</option>
    <option class="avatars avatar9" value="avatar9"{if $comment.comment_avatar == "avatar9"} selected="selected"{/if}>Michael Bone</option>
    <option class="avatars avatar10" value="avatar10"{if $comment.comment_avatar == "avatar10"} selected="selected"{/if}>Damien Blanche</option>
    <option class="avatars avatar11" value="avatar11"{if $comment.comment_avatar == "avatar11"} selected="selected"{/if}>Bennet Williams</option>
    <option class="avatars avatar12" value="avatar12"{if $comment.comment_avatar == "avatar12"} selected="selected"{/if}>Max Rian</option>
    <option class="avatars avatar13" value="avatar13"{if $comment.comment_avatar == "avatar13"} selected="selected"{/if}>Lilian Taylor</option>
    <option class="avatars avatar14" value="avatar14"{if $comment.comment_avatar == "avatar14"} selected="selected"{/if}>Samantha Guilmor</option>
    <option class="avatars avatar15" value="avatar15"{if $comment.comment_avatar == "avatar15"} selected="selected"{/if}>Eve Green</option>
    <option class="avatars avatar16" value="avatar16"{if $comment.comment_avatar == "avatar16"} selected="selected"{/if}>Dennis Chan</option>
    <option class="avatars avatar17" value="avatar17"{if $comment.comment_avatar == "avatar17"} selected="selected"{/if}>Kate Evans</option>
    <option class="avatars avatar18" value="avatar18"{if $comment.comment_avatar == "avatar18"} selected="selected"{/if}>Brandon Flowers, Flash sites developer</option>
    <option class="avatars avatar19" value="avatar19"{if $comment.comment_avatar == "avatar19"} selected="selected"{/if}>Drew Debois, student</option>
    <option class="avatars avatar20" value="avatar20"{if $comment.comment_avatar == "avatar20"} selected="selected"{/if}>Gabor Csomak, Flash &amp; Flex Developer’s Magazine</option>
    <option class="avatars avatar21" value="avatar21"{if $comment.comment_avatar == "avatar21"} selected="selected"{/if}>Lauren Berry</option>
    <option class="avatars avatar22" value="avatar22"{if $comment.comment_avatar == "avatar22"} selected="selected"{/if}>Arthur Green</option>
    <option class="avatars avatar23" value="avatar23"{if $comment.comment_avatar == "avatar23"} selected="selected"{/if}>Sarah Jackson</option>
    <option class="avatars avatar24" value="avatar24"{if $comment.comment_avatar == "avatar24"} selected="selected"{/if}>Andrew Garfield</option>
    <option class="avatars avatar25" value="avatar25"{if $comment.comment_avatar == "avatar25"} selected="selected"{/if}>Kate Ray</option>
    <option class="avatars avatar26" value="avatar26"{if $comment.comment_avatar == "avatar26"} selected="selected"{/if}>Dan Marius</option>
    <option class="avatars avatar27" value="avatar27"{if $comment.comment_avatar == "avatar27"} selected="selected"{/if}>Wayne Lloyd</option>
    <option class="avatars avatar28" value="avatar28"{if $comment.comment_avatar == "avatar28"} selected="selected"{/if}>Andrew Walker</option>
    <option class="avatars avatar29" value="avatar29"{if $comment.comment_avatar == "avatar29"} selected="selected"{/if}>Anna Brooks</option>
    <option class="avatars avatar30" value="avatar30"{if $comment.comment_avatar == "avatar30"} selected="selected"{/if}>Allie</option>
    <option class="avatars avatar31" value="avatar31"{if $comment.comment_avatar == "avatar31"} selected="selected"{/if}>Erica Marceau</option>
    <option class="avatars avatar32" value="avatar32"{if $comment.comment_avatar == "avatar32"} selected="selected"{/if}>Damien</option>
    <option class="avatars avatar33" value="avatar33"{if $comment.comment_avatar == "avatar33"} selected="selected"{/if}>Yoav</option>
    <option class="avatars avatar34" value="avatar34"{if $comment.comment_avatar == "avatar34"} selected="selected"{/if}>Michael</option>
    <option class="avatars avatar35" value="avatar35"{if $comment.comment_avatar == "avatar35"} selected="selected"{/if}>Andrew_ZA</option>
    <option class="avatars avatar36" value="avatar36"{if $comment.comment_avatar == "avatar36"} selected="selected"{/if}>Paulo-Neto</option>
    <option class="avatars avatar37" value="avatar37"{if $comment.comment_avatar == "avatar37"} selected="selected"{/if}>Laszlo Novotny, Home graphic output industry</option>
    <option class="avatars avatar38" value="avatar38"{if $comment.comment_avatar == "avatar38"} selected="selected"{/if}>Murilo Moss Barquette</option>
    <option class="avatars avatar39" value="avatar39"{if $comment.comment_avatar == "avatar39"} selected="selected"{/if}>Yoav Brooks</option>
    <option class="avatars avatar40" value="avatar40"{if $comment.comment_avatar == "avatar40"} selected="selected"{/if}>Brian Crik</option>
    <option class="avatars avatar43" value="avatar43"{if $comment.comment_avatar == "avatar43"} selected="selected"{/if}>Samantha Gunn</option>
    <option class="avatars avatar44" value="avatar44"{if $comment.comment_avatar == "avatar44"} selected="selected"{/if}>Rick Ross</option>
    <option class="avatars avatar45" value="avatar45"{if $comment.comment_avatar == "avatar45"} selected="selected"{/if}>Simon Higgins</option>
    <option class="avatars avatar46" value="avatar46"{if $comment.comment_avatar == "avatar46"} selected="selected"{/if}>Mel Cozzens</option>
    <option class="avatars avatar41" value="avatar41"{if $comment.comment_avatar == "avatar41"} selected="selected"{/if}>---</option>
    <option class="avatars avatar42" value="avatar42"{if $comment.comment_avatar == "avatar42"} selected="selected"{/if}>---</option>
    <option class="avatars avatar47" value="avatar47"{if $comment.comment_avatar == "avatar47"} selected="selected"{/if}>---</option>
    <option class="avatars avatar48" value="avatar48"{if $comment.comment_avatar == "avatar48"} selected="selected"{/if}>---</option>
    <option class="avatars avatar49" value="avatar49"{if $comment.comment_avatar == "avatar49"} selected="selected"{/if}>---</option>
    <option class="avatars avatar50" value="avatar50"{if $comment.comment_avatar == "avatar50"} selected="selected"{/if}>---</option>
	</select>
    
    </td>
    </tr>
    <tr>
	<td>Pages:</td><td>
    <select id="selectPages" name="comment_pages[]" class="select mselect" multiple="multiple">
	{html_options values=$pages.values selected=$pages.select output=$pages.names}
	</select>
	</td>
    </tr>
    <tr>
	<td>Products:</td><td>
    <select id="selectProducts" name="comment_product_id" class="select mselect">
    <option value="">Select product</option>
	{html_options values=$products.values selected=$products.select output=$products.names}
	</select>
	</td>
    </tr>
    <tr>
	<td>Text:</td><td><textarea class="text" id="comment_text" name="comment_text" rows="6">{$comment.comment_text}</textarea></td>
    </tr>
    <tr>
	<td><label for="pblocked">Comment is hidden:</label></td>
    <td><input type="checkbox" id="comment_hidden" name="comment_hidden" value="1"{if $comment.comment_hidden == "1"} checked="checked"{/if} /></td>
    </tr>
   	<td><label for="pblocked">Back to the list after the action:</label></td>
    <td><input type="checkbox" id="toitem" name="toitem" value="1" checked="checked"/></td>
    </tr>
    <tr><td>&nbsp;</td><td>
    {if $lvals.canEdit}
	    <input type="submit" name="ispost" class="submit" value="{if $lvals.isNewRecord}Add{else}Save{/if}" />    
    {/if}
</td>
    </tr>
 </table>
</form>
</fieldset>