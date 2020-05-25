{**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 *}
<div id="acronis-cloud-error" style="display: none">
    {if $isAdmin}
    <div class="errorbox">
        <strong><span class="title">{$errorTitle|escape}</span></strong>
        <br>
        {$errorMessage|escape}
    </div>
    {else}
        <div class="alert alert-danger" role="alert">
            <strong><span class="title">{$errorTitle|escape}</span></strong>
            <br>
            {$errorMessage|escape}
        </div>
    {/if}
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var content = document.getElementById('acronis-cloud-error');

    if (content) {
      var previousElementSelector = {var_export($isAdmin)} ? '.contentarea h2, #content h2' : '#order-standard_cart .header-lined, #order-boxes .header-lined';
      var previousElement = document.querySelector(previousElementSelector);
      if (previousElement && previousElement.parentNode) {
        previousElement.parentNode.insertBefore(content, previousElement.nextSibling);
        content.removeAttribute('style');
      }
    }
  });
</script>