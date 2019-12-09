{**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 *}
<div class="contexthelp" style="margin-top: -42px;">
    <a href="https://marketplace.whmcs.com/product/1246" target="_blank">
        <img src="images/icons/help.png" border="0" align="absmiddle">
        Help
    </a>
</div>

<noscript>
    <strong>{{$noScriptInfo|escape}}</strong>
</noscript>

<script>
  window.acronis = {
    vue: {
      locale: '{{$whmcsLocale}}'
    }
  };
</script>

<div id="acronis-app"></div>

{if isset($assetLinks['js'])}
    {foreach from=$assetLinks.js item=jsLink}
        <script type="text/javascript" src="{$jsLink}"></script>
    {/foreach}
{/if}