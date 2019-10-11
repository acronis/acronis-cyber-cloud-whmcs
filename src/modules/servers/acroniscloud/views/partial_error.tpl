<div id="acronis-cloud-error" style="display: none">
    {if $isAdmin}
    <div class="errorbox">
        <strong><span class="title">{$errorTitle}</span></strong>
        <br>
        {$errorMessage}
    </div>
    {else}
        <div class="alert alert-danger" role="alert">
            <strong><span class="title">{$errorTitle}</span></strong>
            <br>
            {$errorMessage}
        </div>
    {/if}
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var content = document.getElementById('acronis-cloud-error');

    if (content) {
      var previousElementSelector = {var_export($isAdmin)} ? '.contentarea h2' : '#order-standard_cart .header-lined, #order-boxes .header-lined';
      var previousElement = document.querySelector(previousElementSelector);
      if (previousElement && previousElement.parentNode) {
        previousElement.parentNode.insertBefore(content, previousElement.nextSibling);
        content.removeAttribute('style');
      }
    }
  });
</script>