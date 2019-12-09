{**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 *}
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