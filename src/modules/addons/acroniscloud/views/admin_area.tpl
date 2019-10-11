<div class="contexthelp" style="margin-top: -42px;">
    <a href="https://marketplace.whmcs.com/product/1246" target="_blank">
        <img src="images/icons/help.png" border="0" align="absmiddle">
        Help
    </a>
</div>

<noscript>
    <strong>{{$noScriptInfo}}</strong>
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