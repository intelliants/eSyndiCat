{include file="header.tpl" css="js/jquery/plugins/mcdropdown/jquery.mcdropdown"}

<h1>{$lang.suggest_category}</h1>

{include file="notification.tpl"}

<div class="box">
	<p>{$lang.suggest_category_top1}</p>

<form method="post" action="{$smarty.const.ESYN_URL}suggest-category.php?id={$category.id}" style="margin-top: 8px;">

<fieldset style="collapsible">
	<legend>
		<span id="categoryTitle">
			<strong>{$category.title}</strong>
		</span> 
		(<a href="#" onclick="return false;"><span id="changeLabel">{$lang.change}</span></a>)
	</legend>

	<div id="treeContainer" style="display:none;">
		<div id="tree" class="tree"></div>
	</div>
</fieldset>	

<input type="hidden" id="category_id" name="category_id" value="{$category.id}" />
<input type="hidden" id="category_title" name="category_title" value="{$category.title}" />

<br />

<strong>{$lang.category_title}:</strong><br />
<input type="text" class="text" name="title" id="title" size="30" value="{if isset($cat_title)}{$cat_title|escape:"html"}{/if}" /><br />
	
{include file="captcha.tpl"}

<input type="submit" name="add_category" value="{$lang.suggest_category}" style="margin-top: 10px;" class="button" />
</form>

</div>

{esynHooker name="suggestCategoryBeforeIncludeJs"}

{include_file js="js/intelli/intelli.tree, js/frontend/suggest-category, js/jquery/plugins/jquery.dimensions, js/jquery/plugins/jquery.bgiframe, js/jquery/plugins/mcdropdown/jquery.mcdropdown"}

{esynHooker name="suggestCategoryBeforeFooter"}

{include file="footer.tpl"}
