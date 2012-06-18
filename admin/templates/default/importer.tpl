{include file="header.tpl"}

{if isset($report) && !empty($report)}
	{include file="box-header.tpl" title=$esynI18N.import_report}

	<table cellspacing="0" width="100%" class="striped">
	
	{foreach from=$report item=rep}
		<tr>
			<td width="250">{$rep.msg}</td>
			<td>
				<strong>
					{if $rep.success}
						<span style="color: green;">OK</span>
					{else}
						<span style="color: red;">FAIL</span>
					{/if}
				</strong>
			</td>
		</tr>
	{/foreach}

	</table>

	{include file="box-footer.tpl"}
{/if}

{if isset($importers) && !empty($importers) && !isset($success)}
	{include file="box-header.tpl" title=$gTitle}
	
	<form action="controller.php?file=importer" method="post">
	{preventCsrf}

	<table cellspacing="0" width="100%" class="striped">
	
	<tr>
		<td width="150"><strong>{$esynI18N.choose_importer}:</strong></td>
		<td>
			<select name="importer" id="importer" class="common">
			{foreach from=$importers item="importer"}
				<option value="{$importer}" {if isset($smarty.post.importer) && $smarty.post.importer eq $importer}selected="selected"{/if}>{$importer}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.database_host}:</strong></td>
		<td>
			<input type="text" name="host" class="common" value="{if isset($smarty.post.host) && !empty($smarty.post.host)}{$smarty.post.host|escape:"html"}{/if}" id="host" />
		</td>
	</tr>

	<tr>
		<td style="vertical-align:top;"><strong>{$esynI18N.database_name}:</strong></td>
		<td>
			<input type="text" name="database" class="common" value="{if isset($smarty.post.database) && !empty($smarty.post.database)}{$smarty.post.database|escape:"html"}{/if}" id="database" />
			<!--<input type="button" name="find_database" class="common" value="{$esynI18N.find_database}" id="find_database"/>
			<div id="databases_box" style="display: none; padding-top: 5px;">
				<select name="databases" id="databases" size="5">
				</select><br />
				<input type="button" name="select" id="select" value="{$esynI18N.select}" class="common" style="margin-top: 5px;" />
			</div>-->
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.database_username}:</strong></td>
		<td>
			<input type="text" name="username" class="common" value="{if isset($smarty.post.username) && !empty($smarty.post.username)}{$smarty.post.username|escape:"html"}{/if}" id="username" />
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.database_password}:</strong></td>
		<td>
			<input type="password" name="password" class="common" />
		</td>
	</tr>

	<tr>
		<td><strong>{$esynI18N.database_prefix}:</strong></td>
		<td>
			<input type="text" name="prefix" class="common" value="{if isset($smarty.post.prefix) && !empty($smarty.post.prefix)}{$smarty.post.prefix|escape:"html"}{/if}" />
		</td>
	</tr>

	<tr>
		<td rowspan="2">
			<input type="submit" name="start" id="start" value="Import" class="common" />
		</td>
	</tr>
	
	</table>

	</form>

	{include file="box-footer.tpl"}
{/if}

{include_file js="js/admin/importer"}

{include file="footer.tpl"}
