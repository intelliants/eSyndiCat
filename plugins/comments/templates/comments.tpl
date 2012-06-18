{include_file css="plugins/comments/templates/css/style"}
{if $comments}
	<div id="comments_container" style="border-bottom: 1px solid #CCCCCC;">
	{foreach from=$comments item=comment name=comments}
		<div class="posted">
			{if $config.listing_rating}
				{section name=star loop=$comment.rating}<img src="plugins/comments/templates/img/gold.png" alt="" />{/section}
			{/if}
			<p>{$lang.comment_author} {if $comment.url neq ''}<a href="{$comment.url}" rel="nofollow" target="_blank"><strong>{$comment.author|escape:"html"}</strong></a>{else}<strong>{$comment.author|escape:"html"}</strong>{/if} / {$comment.date|date_format:$config.date_format}</p>
		</div>
		<div class="comment">
			{if $config.html_comments}
				{$comment.body}
			{else}
				{$comment.body|escape:"html"}
			{/if}
		</div>
		{if !$smarty.foreach.comments.last}<hr />{else}<div style="height: 15px;">&nbsp;</div>{/if}
	{/foreach}
	</div>
{/if}

<div id="error" style="margin-bottom: 10px; display: none;"></div>

{if !$config.allow_listing_comments_submission}
	<div class="notification"><ul><li>{$lang.listing_comments_submission_disabled}</li></ul></div>
{else}
	{if !$config.listing_comments_accounts && !$esynAccountInfo }
		<div class="notification"><ul><li>{$lang.error_comment_logged}</li></ul></div>
	{else}
		{if isset($msg)}
			{if !$error}
				<script type="text/javascript">
					sessvars.$.clearMem();
				</script>
			{/if}
		{/if}
		<form action="" method="post" id="comment" style="padding: 10px 0 0;">
			{if $esynAccountInfo}
				<input type="hidden" name="author" value="{$esynAccountInfo.username}" />
				<input type="hidden" name="email" value="{$esynAccountInfo.email}" />
			{else}
				<p class="field">
					<label>{$lang.comment_author}:</label><br /><input type="text" class="text" name="author" size="25" value="{if isset($smarty.post.author)}{$smarty.post.author|escape:"html"}{/if}" />
				</p>
				<p class="field">
					<label>{$lang.author_email}:</label><br /><input type="text" class="text" name="email" size="25" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:"html"}{/if}" />
				</p>
			{/if}
			<p class="field">
				<label>{$lang.url}:</label><br /><input type="text" class="text" name="url" size="25" value="{if isset($smarty.post.url)}{$smarty.post.url|escape:"html"}{/if}" />
			</p>
			
			{if $config.listing_rating}
				<div id="comment-rating" style="margin: 10px 0 20px;"></div>
			{/if}
			
			<div style="clear: both;"></div>
			
			<p class="field">
				<textarea name="comment" class="ckeditor_textarea" style="margin-top: 5px; width: 99%;" rows="6" cols="40" id="comment_form">{if isset($body) && !empty($body)}{$body|escape:"html"}{/if}</textarea><br />
				<input type="text" class="text" id="comment_counter" />&nbsp;{$lang.characters_left}<br />
			</p>
			
			{include file="captcha.tpl"}
			
			<div>
				<input type="hidden" name="listing_id" value="{$listing.id}" />
				<input type="submit" id="add" name="add_comment" value="{$lang.leave_comment}" class="button"/>
			</div>
		</form>
		{include_file js="js/ckeditor/ckeditor, js/intelli/intelli.textcounter, js/jquery/plugins/jquery.validate, plugins/comments/js/frontend/comment-rating, plugins/comments/js/frontend/comments"}
	{/if}
{/if}
