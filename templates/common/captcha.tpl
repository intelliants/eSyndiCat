{if $config.captcha && $config.captcha_name neq ''}
	<div class="captcha" id="captcha">
		<fieldset class="collapsible">
			<legend>{$lang.captcha}</legend>
			{include_captcha name=$config.captcha_name}
		</fieldset>
	</div>
{/if}