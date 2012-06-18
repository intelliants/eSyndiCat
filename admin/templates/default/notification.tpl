<div {if isset($id) && !empty($id)}id="{$id}"{else}id="notification"{/if}>
	{if $msg}
		<div class="message {$msg.type}">
			<div class="inner">
				<div class="icon"></div>
				<ul>
					{foreach from=$msg.msg item=message}
						<li>{$message}</li>
					{/foreach}
				</ul>
			</div>
		</div>
	{/if}
</div>
