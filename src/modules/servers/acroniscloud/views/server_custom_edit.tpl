{**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 *}
<script>
  (function() {
    var authType = '';

    function setFieldLabel(selector, label) {
      var childElements = document.querySelectorAll(selector);
      if (!childElements.length) {
        return;
      }
      [].forEach.call(childElements, function (childElement) {
        var field = childElement.parentElement.previousElementSibling;
        field.textContent = label;
      });
    }

    function addHintAfter(selector, isSmall) {
      var hintElement = isSmall ? document.createElement('small') : document.createElement('div');
      hintElement.setAttribute('class', 'acronisAuthHint');
      hintElement.innerHTML = '{$labels['hint']|escape}';
      var inputCell = document.querySelector(selector);
      inputCell.parentElement.appendChild(hintElement);
    }

    function showHint() {
      var hintElements = document.getElementsByClassName('acronisAuthHint');
      if (!hintElements.length) {
        if (document.getElementById('addPassword')) {
          addHintAfter('#addPassword', true);
        }
        addHintAfter('input[name="password"]', false);
      } else {
        [].forEach.call(hintElements, function (e) { e.classList.remove('hidden'); });
      }
    }

    function hideHint() {
      var hintElements = document.getElementsByClassName('acronisAuthHint');
      if (hintElements.length) {
        [].forEach.call(hintElements, function (e) { e.classList.add('hidden'); });
      }
    }

    function createAuthRadioOption(name, value, label) {
      var elLabel = document.createElement('label');
      elLabel.setAttribute('class', 'radio-inline');

      var input = document.createElement('input');
      input.setAttribute('type', 'radio');
      input.setAttribute('name', name);
      input.setAttribute('value', value);
      if (value === authType) {
        input.setAttribute('checked', '');
      }
      input.addEventListener('change', function(event) {
        authType = event.target.value;
        // select the radio button in the hidden form also (if it exists)
        [].forEach.call(document.querySelectorAll('input[value="' + authType + '"]'), function (i) { i.checked = true; });
        setClientFields(authType);
      });
      elLabel.appendChild(input);

      var text = document.createTextNode(label);
      elLabel.appendChild(text);

      return elLabel;
    }

    function createNewAuthField() {
      var authTypeField = document.createElement('div');
      authTypeField.setAttribute('class', 'inputAuthType form-group');

      var authTypeLabel = document.createElement('label');
      authTypeLabel.setAttribute('class', 'col-lg-3 col-sm-4 control-label');
      authTypeLabel.textContent = '{$labels['authentication']|escape}';
      authTypeField.appendChild(authTypeLabel);

      var authTypeInput = document.createElement('div');
      authTypeInput.setAttribute('class', 'col-lg-4 col-sm-4');
      var radioUsername = createAuthRadioOption('auth-type', 'username', '{$labels['username']|escape}');
      var radioClientCredentials = createAuthRadioOption('auth-type', 'client_credentials', '{$labels['client_id_method']|escape}');
      authTypeInput.appendChild(radioUsername);
      authTypeInput.appendChild(radioClientCredentials);
      authTypeField.appendChild(authTypeInput);

      var usernameInput = document.getElementById('addUsername');
      var usernameField = usernameInput.parentElement.parentElement;
      usernameField.parentElement.insertBefore(authTypeField, usernameField);
    }

    function createOldAuthField() {
      var authTypeField = document.createElement('tr');
      authTypeField.setAttribute('class', 'inputAuthType');

      var authTypeLabel = document.createElement('td');
      authTypeLabel.setAttribute('class', 'fieldlabel');
      authTypeLabel.textContent = '{$labels['authentication']|escape}';
      authTypeField.appendChild(authTypeLabel);

      var authTypeInput = document.createElement('td');
      authTypeInput.setAttribute('class', 'fieldarea');
      var radioUsername = createAuthRadioOption('auth-type', 'username', '{$labels['username']|escape}');
      var radioClientCredentials = createAuthRadioOption('auth-type', 'client_credentials', '{$labels['client_id_method']|escape}');
      authTypeInput.appendChild(radioUsername);
      authTypeInput.appendChild(radioClientCredentials);
      authTypeField.appendChild(authTypeInput);

      var usernameInput = document.querySelector('input[name="username"]');
      var usernameField = usernameInput.parentElement.parentElement;
      usernameField.parentElement.insertBefore(authTypeField, usernameField);
    }

    function createAuthTypeFields() {
      var existingFields = document.getElementsByClassName('inputAuthType');
      if (existingFields.length) {
        [].forEach.call(existingFields, function (field) { field.classList.remove('hidden'); });
        // reset to username when un-hiding
        [].forEach.call(document.querySelectorAll('input[value="username"]'), function (i) { i.checked = true; });
        return;
      }
      if (document.getElementById('addUsername')) {
        createNewAuthField();
      }
      createOldAuthField();
    }

    function setClientFields(mode) {
      var accessHashFields = document.querySelectorAll('#newToken, #apiToken');
      var accessHashInputs = document.querySelectorAll('#newHash, #serverHash');
      if (mode === 'reset') {
        [].forEach.call(accessHashInputs, function (i) { i.value = ''; });
        [].forEach.call(accessHashFields, function (f) { f.parentElement.parentElement.classList.remove('hidden'); });
      } else {
        [].forEach.call(accessHashInputs, function (i) { i.value = mode; });
        [].forEach.call(accessHashFields, function (f) { f.parentElement.parentElement.classList.add('hidden'); });
      }
      if (mode === 'client_credentials') {
        setFieldLabel('#addUsername, input[name="username"]', '{$labels['client_id']|escape}');
        setFieldLabel('#addPassword, input[name="password"]', '{$labels['client_secret']|escape}');
        hideHint();
      } else {
        setFieldLabel('#addUsername, input[name="username"]', '{$labels['username']|escape}');
        setFieldLabel('#addPassword, input[name="password"]', '{$labels['password']|escape}');
        mode === 'reset' ? hideHint() : showHint();
      }
    }

    // hostname
    {literal}
    function urlToHostname(input) {
      if (!input) {
        return input;
      }
      // https://stackoverflow.com/a/5717133
      var pattern = new RegExp(
        '^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$', // fragment locator
        'i'
      );
      var result = pattern.exec(input);
      if (result[1]) {
        document.querySelector("#inputSecure").checked = result[1] === "https://";
      }
      return result[2];
    }
    {/literal}
    var inputs = [];
    function initUrlInputs() {
      function add(input, label) {
        var urlInput = input.cloneNode();
        urlInput.removeAttribute('id')
        urlInput.removeAttribute('name')
        urlInput.classList.add('hidden');
        input.parentNode.appendChild(urlInput);

        input.addEventListener('input', event => {
          var value = event.target.value;
          inputs.forEach(input => {
            if (input.origin !== event.target) {
              input.origin.value = value;
            }
            input.url.value = value;
          });
        });
        urlInput.addEventListener('input', event => {
          var value = event.target.value;
          var hostname = urlToHostname(value);
          inputs.forEach(input => {
            input.origin.value = hostname;
            if (input.url !== event.target) {
              input.url.value = value;
            }
          });
        });

        inputs.push({
          origin: input,
          url: urlInput,
          label: label,
          labelHTML: label.innerHTML,
        });
      }

      var newInput = document.querySelector('#addHostname');
      if (newInput) {
        add(newInput, newInput.labels[0]);
      }

      var oldInput = document.querySelector('#inputHostname');
      if (oldInput) {
        add(oldInput, oldInput.parentNode.parentNode.children[0]);
      }
    }
    function showUrlInputs() {
      inputs.forEach(input => {
        input.label.innerText = '{$labels['url_hostname']|escape}';
        input.origin.classList.add('hidden');
        input.url.classList.remove('hidden');

        input.origin.value = urlToHostname(input.origin.value);
     });
    }
    function hideUrlInputs() {
      inputs.forEach(input => {
        input.label.innerHTML = input.labelHTML;
        input.origin.classList.remove('hidden');
        input.url.classList.add('hidden');
      });
    }

    function checkServerType(selection) {
      var isEvent = !!selection.target;
      var selectedValue = isEvent ? selection.target.value : selection;

      if (selectedValue === '{$acronisService}') {
        // reset back to username when switching to acronis cloud
        authType = authType || 'username';
        createAuthTypeFields();
        setClientFields(authType);
        showUrlInputs();
      } else if (isEvent) {
        var selectAuthTypes = document.getElementsByClassName('inputAuthType');
        [].forEach.call(selectAuthTypes, function (at) { at.classList.add('hidden'); });
        authType && setClientFields('reset');
        authType = '';
        hideUrlInputs();
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      authType = document.getElementById('serverHash').value;
      initUrlInputs();
      var serverTypeSelects = document.querySelectorAll('#addType, #inputServerType');
      // workaround for IE11
      [].forEach.call(serverTypeSelects, function (ts) {
        ts.addEventListener('change', checkServerType);
        if (ts.offsetParent) {
          // selector is visible
          checkServerType(ts.value);
        }
      });
    });
  })();
</script>
