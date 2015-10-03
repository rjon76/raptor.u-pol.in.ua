<h1>Comments List</h1>
<blockquote>
<span class="note">For use -  add field Global Extension, default value <span class="note params">"getComments|Comments"</span>. 
<br/> Value exp: <span class="note params">v:148 (comments for product ID)</span>; v:148,128 (comments for several products); v:0|v:50,55 (for several comments where not select product) </span>
</blockquote>

    {foreach from=$comments item=category key=cat_id}
    <fieldset><legend><a id="ln{$cat_id}" class="phide" href="javascript:void();" onclick="return tabs('{$cat_id}');" title="Click to show or hide">{if $category.id >0}{$category.id}/{/if}{$category.name}</a> ({$category.comments|@count}) {if $category.id >0}<a class="add" href="{$ADMIN_DIR}/comments/add/pid/{$category.id}/" style="display:block;float:right;"><img src="{$ADMIN_DIR}/images/add.png" width="16" title="add comment for product" alt="add comment" border="0" /> add comment</a>{/if}</legend>
        <div id="cat{$cat_id}" class="container" style="display:none;">
        <table class="table table-list">
            <thead>
            <th>ID</th>
            <th>Author</th>
            <th>Text</th>
            <th>Pages</th>
            <th>Order</th>
            {if $lvals.canEdit || $lvals.canDelete}
            <th>Options</th>
            {/if}
            </thead>
            <tbody>
            {foreach from=$category.comments item=comment}

            <tr id="comment{$comment.comment_id}"{if $comment.comment_hidden == "1"} class="row-hidden"{/if}>
                <td class="id">{$comment.comment_id}</td>
                <td class="author"><span class="icon-avatars {$comment.comment_avatar}"></span>{$comment.comment_author}</td>
                <td class="text"><span class="date">{$comment.comment_date}</span>{$comment.comment_text}</td>
                <td class="pages">{$comment.comment_pages|replace:',':'<br/>'}</td>
                <td class="order">
                    <img src="{$ADMIN_DIR}/images/2up.gif" width="20" height="20" alt="Product up" class="pointer" onclick="upComment({$cat_id},{$comment.comment_id});" /><br />
                    <img src="{$ADMIN_DIR}/images/2down.gif" width="20" height="20" alt="Product down" class="pointer" onclick="downComment({$cat_id},{$comment.comment_id});" />
                </td>
                
                {if $lvals.canEdit || $lvals.canDelete}
                <td class="edit">
                
                    {if $lvals.canEdit}
                     	<img src="{$ADMIN_DIR}/images/visible.png" width="16" height="16" alt="disabled" title="disabled" class="pointer" onclick="hiddenComment({$comment.comment_id});" />
                    {/if}
                     
                    {if $lvals.canEdit}
                   	    <a href="{$ADMIN_DIR}/comments/edit/id/{$comment.comment_id}/"><img src="{$ADMIN_DIR}/images/edit.png" width="16" height="16" alt="edit comment" title="edit"/></a>
                    {/if}
                     
                    {if $lvals.canDelete}
                        <a href="{$ADMIN_DIR}/comments/delete/id/{$comment.comment_id}/" onclick="return confirm('Do You really want to delete this product?');"><img src="{$ADMIN_DIR}/images/delete.png" width="16" height="16" alt="delete" title="edit"/></a>
                    {/if}

                </td>
                {/if}

            </tr>
            

            
            {/foreach}
</tbody>
                </table>
        </div>
    </fieldset>
    {/foreach}