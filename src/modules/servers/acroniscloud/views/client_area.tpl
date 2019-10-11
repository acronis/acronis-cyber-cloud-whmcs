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