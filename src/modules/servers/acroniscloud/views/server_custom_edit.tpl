<script>
  (function() {
    var authType = '';

    function setFieldLabel(selector, label) {
      var childElements = document.querySelectorAll(selector);
      if (!childElements.length) {
        return;
      }
      childElements.forEach(function (childElement) {
        var field = childElement.parentElement.previousElementSibling;
        field.textContent = label;
      });
    }

    function addHintAfter(selector, isSmall) {
      var hintElement = isSmall ? document.createElement('small') : document.createElement('div');
      hintElement.classList.add('acronisAuthHint');
      hintElement.innerText = '{$labels['hint']}';
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
        Array.from(hintElements).forEach(function (e) { e.classList.remove('hidden'); });
      }
    }

    function hideHint() {
      var hintElements = document.getElementsByClassName('acronisAuthHint');
      if (hintElements.length) {
        Array.from(hintElements).forEach(function (e) { e.classList.add('hidden'); });
      }
    }

    function createAuthRadioOption(name, value, label) {
      var elLabel = document.createElement('label');
      elLabel.classList.add('radio-inline');

      var input = document.createElement('input');
      input.setAttribute('type', 'radio');
      input.setAttribute('name', name);
      input.setAttribute('value', value);
      if (value === authType) {
        input.setAttribute('checked', '');
      }
      input.addEventListener('change', function(event) {
        authType = event.target.value;
        setClientFields(authType);
      });
      elLabel.appendChild(input);

      var text = document.createTextNode(label);
      elLabel.appendChild(text);

      return elLabel;
    }

    function createNewAuthField() {
      var authTypeField = document.createElement('div');
      authTypeField.classList.add('inputAuthType', 'form-group');

      var authTypeLabel = document.createElement('label');
      authTypeLabel.classList.add('col-lg-3', 'col-sm-4', 'control-label');
      authTypeLabel.textContent = '{$labels['authentication']}';
      authTypeField.appendChild(authTypeLabel);

      var authTypeInput = document.createElement('div');
      authTypeInput.classList.add('col-lg-4', 'col-sm-4');
      var radioUsername = createAuthRadioOption('auth-type', 'username', '{$labels['username']}');
      var clientCredentialsLabel = '{$labels['client_id']} ({$labels['recommended']})';
      var radioClientCredentials = createAuthRadioOption('auth-type', 'client_credentials', clientCredentialsLabel);
      authTypeInput.appendChild(radioUsername);
      authTypeInput.appendChild(radioClientCredentials);
      authTypeField.appendChild(authTypeInput);

      var usernameInput = document.getElementById('addUsername');
      var usernameField = usernameInput.parentElement.parentElement;
      usernameField.parentElement.insertBefore(authTypeField, usernameField);
    }

    function createOldAuthField() {
      var authTypeField = document.createElement('tr');
      authTypeField.classList.add('inputAuthType');

      var authTypeLabel = document.createElement('td');
      authTypeLabel.classList.add('fieldlabel');
      authTypeLabel.textContent = '{$labels['authentication']}';
      authTypeField.appendChild(authTypeLabel);

      var authTypeInput = document.createElement('td');
      authTypeInput.classList.add('fieldarea');
      var radioUsername = createAuthRadioOption('auth-type', 'username', '{$labels['username']}');
      var radioClientCredentials = createAuthRadioOption('auth-type', 'client_credentials', '{$labels['client_id']}');
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
        Array.from(existingFields).forEach(function (field) { field.classList.remove('hidden'); });
        // reset to username when un-hiding
        document.querySelectorAll('input[value="username"]').forEach(function (i) { i.checked = true; });
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
        accessHashInputs.forEach(function (i) { i.value = ''; });
        accessHashFields.forEach(function (f) { f.parentElement.parentElement.classList.remove('hidden'); });
      } else {
        accessHashInputs.forEach(function (i) { i.value = mode; });
        accessHashFields.forEach(function (f) { f.parentElement.parentElement.classList.add('hidden'); });
      }
      if (mode === 'client_credentials') {
        setFieldLabel('#addUsername, input[name="username"]', '{$labels['client_id']}');
        setFieldLabel('#addPassword, input[name="password"]', '{$labels['client_secret']}');
        hideHint();
      } else {
        setFieldLabel('#addUsername, input[name="username"]', '{$labels['username']}');
        setFieldLabel('#addPassword, input[name="password"]', '{$labels['password']}');
        mode === 'reset' ? hideHint() : showHint();
      }
    }

    function checkServerType(selection) {
      var isEvent = !!selection.target;
      var selectedValue = isEvent ? selection.target.value : selection;

      if (selectedValue === '{$acronisService}') {
        // reset back to username when switching to acronis cloud
        authType = authType || 'username';
        createAuthTypeFields();
        setClientFields(authType);
      } else if (isEvent) {
        var selectAuthTypes = document.getElementsByClassName('inputAuthType');
        Array.from(selectAuthTypes).forEach(function (at) { at.classList.add('hidden'); });
        authType && setClientFields('reset');
        authType = '';
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      authType = document.getElementById('serverHash').value;
      var serverTypeSelects = document.querySelectorAll('#addType, #inputServerType');
      serverTypeSelects.forEach(function (ts) {
        ts.addEventListener('change', checkServerType);
        if (ts.offsetParent) {
          // selector is visible
          checkServerType(ts.value);
        }
      });
    });
  })();
</script>