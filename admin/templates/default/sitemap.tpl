{include file='header.tpl'}

{include file="box-header.tpl" title=$gTitle}
<div>
	<input class="common" type="radio" id="google_type" name="type_sitemap" value="google"  checked="checked"><label for="google_type">Google</label>
	<input type="radio" class="common" id="yahoo_type" name="type_sitemap" value="yahoo"><label for="yahoo_type">Yahoo</label>
</div>
<div>
	<span>{$esynI18N.start_from}</span>
	<input type="text" size="6" value="0" id="start_num" class="common" />
	<span>{$esynI18N.total_items}</span>
	<input type="text" size="6" value="0" id="all" class="common" />
	<input type="button" value="Create" id="start" class="common" {$disabled} />
</div>
<div id="msg" style="margin: 15px 0;"></div>
<div style="position:relative;border:1px solid #76A9DC; height:33px; width:100%; background-color:#ffffff;color:#335B92;text-align:center;">
	<div id="percent" style="position:absolute;left:50%;top:10px;z-index:2;font-size:13px;font-weight:bold;">0%</div>
	<div id="progress_bar" style="height:33px; width:0%; background:url({$smarty.const.ESYN_URL}admin/templates/{$config.admin_tmpl}/img/bgs/progress_bar.gif) left repeat-x;color:#335B92;"></div>
</div>

<script type="text/javascript">
<!--
intelli.sitemap = {ldelim}{rdelim};
intelli.sitemap.items = [];
intelli.sitemap.limit = 1000;
intelli.sitemap.start = 0;
intelli.sitemap.current = 0;
intelli.sitemap.stage = 1;
intelli.sitemap.file = 0;
intelli.sitemap.pause = 1;
intelli.sitemap.url = 'controller.php?file=sitemap';
intelli.sitemap.type_sitemap = 'google';
intelli.sitemap.total = 0;
intelli.sitemap.stage_all = 0;
intelli.sitemap.percent = 0;

{foreach from=$items key=item item=count}
	intelli.sitemap.items.push(['{$item}','{$count}']);
	intelli.sitemap.total = intelli.sitemap.total + {$count};

	intelli.sitemap.stage_all = intelli.sitemap.stage_all + Math.ceil({$count} / intelli.sitemap.limit);
{/foreach}

$('#all').val(intelli.sitemap.total);
//-->
</script>


{include file="box-footer.tpl"}

{include_file js="js/admin/sitemap, js/intelli/intelli.gmodel"}

{include file="footer.tpl"}