function AjaxRequest(config) {
  // Request URL
  this.url = null;
  // Request method
  this.method = 'get';
  // Response mime type
  this.handleAs = 'text';
  // Asynchronous request ?
  this.asynchronous = true;
  // Request parameters
  this.parameters = {};
  // AJAX transport (xmlHttpRequest object)
  this.transport = null;
  // On success callback
  this.onSuccess = function () { };
  // On error callback
  this.onError = function () { };
  // Cancel request method
  this.cancel = function () {
    if (this.transport != null) {
      this.onError = function () { };
      this.onSuccess = function () { };
      this.transport.abort();
    }
  };

  // Check config values
  if (typeof config != "object") {
    throw 'Request parameter should be an object';
  }

  // Check request URL parameter
  if (!config.url) {
    throw 'Request URL needed';
  }
  this.url = config.url;

  // Check request method parameter
  if (config.method) {
    if (typeof config.method === "string") {
      var method = config.method.toLowerCase();
      if (method === "get" || method === "post")
        this.method = method;
      else
        throw "'" + config.method + "' method not supported";
    }
    else {
      throw "'method' parameter should be a string";
    }
  }

  // Check request asynchronous mode parameter
  if (config.asynchronous !== undefined) {
    if (typeof config.asynchronous === "boolean") {
      this.asynchronous = config.asynchronous;
    }
    else {
      throw "'asynchronous' parameter should be a boolean";
    }
  }

  // Check request parameters parameter
  if (config.parameters) {
    if (config.parameters instanceof Object) {
      this.parameters = config.parameters;
    }
    else {
      throw "'parameters' parameter should be a object";
    }
  }

  var callbackFound = false;
  // Check onSuccess callback parameter
  if (config.onSuccess) {
    if (config.onSuccess instanceof Function) {
      this.onSuccess = config.onSuccess;
      callbackFound = true;
    }
    else {
      throw "'onSuccess' parameter should be a function";
    }
  }

  // Check onError callback parameter
  if (config.onError) {
    if (config.onError instanceof Function) {
      this.onError = config.onError;
      callbackFound = true;
    }
    else {
      throw "'onError' parameter should be a function";
    }
  }

  // Check whether onSuccess or onError callback parameter is present
  if (!callbackFound) {
    throw "'onSuccess' or 'onError' parameter not found";
  }

  // Check response mime type parameter
  if (config.handleAs) {
    if (typeof config.handleAs === 'string') {
      var handleAs = config.handleAs.toLowerCase();
      if (['text', 'json', 'xml'].indexOf(handleAs) !== -1) {
        this.handleAs = handleAs;
      }
      else {
        throw "handleAs format '" + config.handleAs + "' not supported";
      }
    }
    else {
      throw "handleAs parameter should be a string";
    }
  }

  // Build parameters string
  var parameters = new Array();
  // Iterate on parameters
  for (var i in this.parameters) {
    // Escape parameter value with encodeURIComponent()
    var value = encodeURIComponent(this.parameters[i]);
    // Store 'parameter_name=escaped_parameter_value' in array 'parameters'
    parameters.push(i + "=" + value);
  }
  // Join escaped parameters with '&'
  var parametersString = parameters.join('&');

  let url;
  let options;

  if (this.method === 'get') {

    url = this.url + "?" + parametersString;

    options = {
      method: "GET"
    };
  }
  else {
    url = this.url;

    options = {
      method: "POST",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: parametersString
    };
  }

  fetch(url, options)
    .then(response => {
      if (response.status === 200) {
        return response.text();
      }
      else
        this.onError(response.status, response.statusText);
    })
    .then(text => {
      let result = text;
      switch (this.handleAs) {
        case 'json': result = JSON.parse(text); break;
        case 'xml': let XML = new DOMParser(); result = XML.parseFromString(text, "text/xml");
      }

      this.onSuccess(result);
    });
}