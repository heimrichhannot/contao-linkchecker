(function () {
    var LinkChecker;

    function LinkChecker(element, options) {
        this.element = element;
        this.options = options;
        this.url = this.element.getAttribute('data-url');
        this.element.linkchecker = this;

        if (this.element.getAttribute('data-target')) {
            this.target = document.querySelector(this.element.getAttribute('data-target'));
        }

        if (this.target == 'undefined') {
            return false;
        }


        if (!this.url) {
            this.url = window.location.href;
        }


        var params = {};

        this.url.replace(
            /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
            function (m, key, value) { // callback
                params[key] = value !== undefined ? value : '';
            }
        );

        this.params = params;

        this.init();
    }

    LinkChecker.prototype.init = function () {
        this.test();
    };


    LinkChecker.prototype.test = function () {
        var xhr;
        xhr = new XMLHttpRequest();
        console.log();
        method = "post";
        url = this.element.getAttribute('data-url');
        xhr.open(method, url, true);
        xhr.withCredentials = !!this.options.withCredentials;
        response = null;
        handleError = (function (_this) {
            return function () {
                return false;
            };
        })(this);
        updateProgress = (function (_this) {
            return function (e) {
            };
        })(this);
        xhr.onload = (function (_this) {
            return function (e) {
                var _ref;
                if (xhr.readyState !== 4) {
                    return;
                }
                response = xhr.responseText;
                if (xhr.getResponseHeader("content-type") && ~xhr.getResponseHeader("content-type").indexOf("application/json")) {
                    try {
                        response = JSON.parse(response);
                    } catch (_error) {
                        e = _error;
                        response = "Invalid JSON response from server.";
                    }
                }
                if (!((200 <= (_ref = xhr.status) && _ref < 300))) {
                    return handleError();
                } else {
                    return _this._finished(response, e);
                }
            };
        })(this);
        xhr.onerror = (function (_this) {
            return function () {
                return handleError();
            };
        })(this);
        progressObj = (_ref = xhr.upload) != null ? _ref : xhr;
        progressObj.onprogress = updateProgress;
        headers = {
            "Accept": "application/json",
            "Cache-Control": "no-cache",
            "X-Requested-With": "XMLHttpRequest"
        };
        if (this.options.headers) {
            extend(headers, this.options.headers);
        }
        for (headerName in headers) {
            headerValue = headers[headerName];
            if (headerValue) {
                xhr.setRequestHeader(headerName, headerValue);
            }
        }
        formData = new FormData();
        if (this.params) {
            _ref1 = this.params;
            for (key in _ref1) {
                value = _ref1[key];
                formData.append(key, value);
            }
        }

        return this.submitRequest(xhr, formData);
    };

    LinkChecker.prototype.submitRequest = function (xhr, formData) {
        return xhr.send(formData);
    };

    LinkChecker.prototype._finished = function (responseText, e) {
        if (responseText.result == 'undefined' || responseText.result.html == 'undefined') {
            return false;
        }
        this.target.innerHTML = responseText.result.html;
    };

    LinkCheckerRegistry = {
        init: function () {
            this.register();
        },
        register: function () {

            var elements = document.querySelectorAll('[data-linkchecker]');

            for (var i = 0, len = elements.length; i < len; i++) {
                var element = elements[i],
                    config = {};

                // do not attach Dropzone again
                if (typeof element.linkchecker != 'undefined') continue;

                new LinkChecker(element, config);
            }
        }
    };

    // jquery support
    if (window.jQuery) {
        jQuery(document).ready(function () {
            LinkCheckerRegistry.init();
        });

        jQuery(document).ajaxComplete(function () {
            LinkCheckerRegistry.init();
        });
    }

    // mootools support
    if (window.MooTools) {

        window.addEvent('domready', function () {
            LinkCheckerRegistry.init();
        });

        window.addEvent('ajax_change', function () {
            LinkCheckerRegistry.init();
        });
    }

}).call(this);
