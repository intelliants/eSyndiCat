{if isset($msg) && !empty($msg)}
	<div id="notification">
		<div class="{if $error}error{else}notification{/if}">
			<ul class="common">
				{foreach from=$msg item=message}
					<li>{$message}</li>
				{/foreach}
			</ul>
		</div>
	</div>
{/if}