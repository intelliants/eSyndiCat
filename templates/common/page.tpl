{include file="header.tpl"}

<h1>{$title}</h1>

{esynHooker name="tplFrontPagesAfterHeader"}

<div class="box">{$content}</div>

{esynHooker name="tplFrontPagesBeforeFooter"}

{include file="footer.tpl"}