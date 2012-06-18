{if isset($esyndicat_messages)}
	{foreach from=$esyndicat_messages item=esyn_message}
		{if !empty($esyn_message.msg)}
			<div class="message {$esyn_message.type}">
				<div class="inner">
					<div class="icon"></div>
					<ul>
						{foreach from=$esyn_message.msg item=m}
							<li>{$m}</li>
						{/foreach}
					</ul>
				</div>
			</div>
		{/if}
	{/foreach}
{/if}