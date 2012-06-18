{include file="header.tpl"}

<h1>{if $error}{$lang.oops}{else}{$lang.thanks}{/if}</h1>

<div class="box">{$msg}</div>

{include file="footer.tpl"}
