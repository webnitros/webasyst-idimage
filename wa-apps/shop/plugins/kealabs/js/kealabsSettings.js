(function ($) {
    $(function () {
        var self = this;
        var KEA_WS_REGISTER = "https://kealabs.com/api/";
        var TEST_MODE = false;
        var uuid = $("#kea-settings-uuid").text();
        var kea = new KeaIntegration(KEA_WS_REGISTER, uuid);
        init();
        self.controller = new RegistrationController(uuid, self.registrationForm, registrationCallback);

        function init() {
            var isConfigured = $("#kea-settings-configured").text() == 1;
            var autoConfigured = $("#kea-settings-autoconfigured").text() == 1;

            self.notConfiguredBlock = $("#kea-not-configured");
            self.mainForm = $("#plugins-settings-form");
            var cbEnabled = self.mainForm.find("input[name=enabled]");
            cbEnabled.prop("checked", cbEnabled.val() == "1");

            $(".kea-auto-registation").hide();
            $(".kea-finished-registration").hide();

            self.registrationForm = $("#kea-new-account");
            self.btnRegister = $("#btn-register");

            self.loading = $(".kea-loading");
            self.tabs = $(".kea-tabs");
            self.tabLogin = $("#tab-login");
            self.tabRegister = $("#tab-register");
            self.tabLogin.on('click', function () {
                self.registrationForm.hide();
                self.mainForm.show();
                self.tabLogin.addClass('kea-tab-white').removeClass('kea-tab');
                self.tabRegister.addClass('kea-tab').removeClass('kea-tab-white');
            });
            self.tabRegister.on('click', function () {
                self.registrationForm.show();
                self.mainForm.hide();
                self.tabLogin.addClass('kea-tab').removeClass('kea-tab-white');
                self.tabRegister.addClass('kea-tab-white').removeClass('kea-tab');
            });

            if (isConfigured) {
                self.tabs.hide();
                self.mainForm.show();
                self.notConfiguredBlock.hide();
                self.mainForm.find('.kea-feature-list').hide();
                self.mainForm.addClass('kea-text-centered');
                self.mainForm.addClass("kea-box-shadow-0").removeClass("kea-box-shadow-6");
                self.registrationForm.hide();
            }
            if (autoConfigured) {
                self.tabs.hide();
                self.registrationForm.hide();
                self.notConfiguredBlock.hide();
                self.mainForm.hide();
                showCongratulationsAuto();
            } else if(!isConfigured) {
                self.notConfiguredBlock.show();
                self.mainForm.hide();
                self.registrationForm.show();
            }

            self.originalState = catchState();
            self.btnRegister.click(register);
            self.mainForm.find('input[type=submit]').click(function (e) {
                onMainFormSubmit(e, isConfigured)
            });
            self.registrationForm.submit(function (e) {
                e.preventDefault();
                return false;
            });
            kea.info({action:"view", args:{configured:isConfigured}});


            // Interactive
            // If any input changed then button will be gray
            // inputs changed => gray
            // saved => green

            self.saveButton = self.mainForm.find(".kea-button");
            self.mainForm.find("input[name=token]").change(function () {
                self.saveButton.removeClass('kea-button-disabled');
            });
            self.mainForm.find("input[name=secret]").change(function () {
                self.saveButton.removeClass('kea-button-disabled');
            });
            self.mainForm.find("input[name=enabled]").change(function () {
                self.saveButton.removeClass('kea-button-disabled');
            });
        }

        function onMainFormSubmit(e, isConfigured) {
            e.preventDefault();
            var tokenError = self.mainForm.find('#token-error');
            var secretError = self.mainForm.find('#secret-error');
            var checkbox = self.mainForm.find("input[name=enabled]");
            checkbox.val((checkbox.prop("checked") == true) ? 1: 0);
            var state = catchState();
            var hasError = false;
            if (state.token == '' || state.token.length != 32) {
                tokenError.show();
                hasError = true;
            } else {
                tokenError.hide();
            }
            if (state.secret == '' || state.token.length != 32) {
                secretError.show();
                hasError = true;
            } else {
                secretError.hide();
            }
            if (!hasError) {
                kea.info({action: "config", args: state});
                if (!isConfigured) {
                    self.loading.hide();
                    self.mainForm.hide();
                    self.registrationForm.hide();
                    self.tabs.hide();
                    showCongratulations();
                }
                self.mainForm.submit();

                // Set save button inactive status
                self.saveButton.addClass('kea-button-disabled');
            }
            return false;
        }

        function catchState() {
            return {
                token: self.mainForm.find("input[name=token]").val().trim(),
                secret: self.mainForm.find("input[name=secret]").val().trim(),
                enabled: self.mainForm.find("input[name=enabled]").val().trim()
            }
        }

        function setState(state) {
            var keys = ["token", "secret", "enabled"];
            for (var i = 0; i < keys.length; i++) {
                var key = keys[i];
                if (state.hasOwnProperty(key)) {
                    if (key == "enabled") {
                        self.mainForm.find("input[name=" + key + "]").prop("checked", state[key] == "1");
                    } else {
                        self.mainForm.find("input[name=" + key + "]").val(state[key]);
                    }
                }
            }
        }

        function register(event) {
            if (self.controller.submit()) {
                self.loading.show();
                self.tabs.hide();
                self.mainForm.addClass("kea-box-shadow-0").removeClass("kea-box-shadow-6");
                self.registrationForm.children().css("visibility", "hidden");
            }
        }

        function showCongratulations() {
            self.notConfiguredBlock.hide();
            $('.kea-finished-registration').show();
            $(".kea-finished-button").click(function () {
                $('.kea-finished-registration').hide();
                self.mainForm.find('.kea-feature-list').hide();
                self.mainForm.addClass('kea-text-centered');
                self.mainForm.addClass("kea-box-shadow-0").removeClass("kea-box-shadow-6");
                self.mainForm.show();
            });
        }

        function showCongratulationsAuto() {
            $('.kea-auto-registation').show();
            $(".kea-auto-registation .kea-finished-button").click(function () {
                $('.kea-auto-registation').hide();
                self.mainForm.find('.kea-feature-list').hide();
                self.mainForm.addClass('kea-text-centered');
                self.mainForm.addClass("kea-box-shadow-0").removeClass("kea-box-shadow-6");
                self.mainForm.show();
            });
        }

        function registrationCallback(err, data) {
            self.loading.hide();
            self.mainForm.hide();
            self.registrationForm.hide();
            if (err == null) {
                showCongratulations();
                setState({token: data.token, secret: data.secret, enabled : "1" });
                self.mainForm.submit();
            } else {
                $('.kea-error').show();
                $('.kea-error-button').click(function () {
                    self.registrationForm.show();
                    $('.kea-error').hide();
                    self.tabs.show();
                    self.registrationForm.children().css("visibility", "visible");
                });
            }
        }

        /**
         * RegistrationController
         */
        function RegistrationController(uuid, form, globalCallback) {
            var self = this;
            self.submitted = false;
            self.inProgress = false;
            self.message = null;
            var fieldKeys = ["name", "email", "password", "password_confirm", "phone"];
            self.fields = buildFields();
            self.iHavePromoCode = false;

            function onSuccess(data) {
                self.inProgress = false;
                self.submitted = false;
                self.message = {text: 'Регистрация прошла успешно!', success: true, serverResponse: data};
                self.fields["password"].value = null;
                self.fields["password_confirm"].value = null;
                if (data.status === "ok") {
                    globalCallback(null, data);
                } else {
                    globalCallback("status isn't ok", data);
                }
            }

            self.defaultError = {text: 'Ошибка регистрации. Пожалуйста, проверьте заполненную форму', success: false};

            function onError(data, status) {
                self.inProgress = false;
                self.fields["password"].value = null;
                self.fields["password_confirm"].value = null;
                if (status != 400) {
                    self.message = $.extend(self.defaultError, {serverResponse: data});
                    kea.error("Registration error: " + status, data);
                } else {
                    self.message = {
                        text: 'Не удалось отправить запрос на сервер. Попробуйте пожалуйста позже. Если ошибка повториться, обратитесь пожалуйста в службу поддержки',
                        success: false,
                        serverResponse: data
                    };
                    kea.error("Registration error.code 400: " + status, data);
                }
                globalCallback(self.message, data);
            }

            function beanToRegistrationRequest(bean) {
                return {
                    platform: "webasyst",
                    plugin: "kealabs",
                    service: "recs",
                    plan: "0",
                    installationId: uuid,
                    firstName: self.fields["name"].value,
                    email: self.fields["email"].value,
                    phone: self.fields["phone"].value,
                    password: md5(self.fields["password"].value),
                    passwordConfirm: md5(self.fields["password_confirm"].value)
                    //promo: self.fields["promo"].value,
                    //shopName: bean.shopName,
                    //shopAddress: bean.shopAddress
                };
            }

            function buildFields() {
                var fields = {};
                for (var i = 0; i < fieldKeys.length; i++) {
                    var key = fieldKeys[i];
                    var block = form.find("#field_" + key);
                    fields[key] = {
                        id: key,
                        block: block,
                        input: block.find("input"),
                        error: block.find(".kea-field-error")
                    };
                }
                return fields;
            }

            self.validate = function () {
                var hasErrors = false;
                for (var i = 0; i < fieldKeys.length; i++) {
                    var key = fieldKeys[i];
                    var field = self.fields[key];
                    field.value = field.input.val().trim();
                    field.error.hide();
                    if (!field.value || field.value.length < 1) {
                        field.error.show();
                        hasErrors = true;
                    }
                }
                if (self.fields["password"].value !== self.fields["password_confirm"].value) {
                    hasErrors = true;
                    field.error.show()
                }
                return !hasErrors;
            };

            self.submit = function () {
                self.submitted = true;

                if (!self.validate()) {
                    self.message = self.defaultError;
                    return false;
                }
                self.inProgress = true;
                kea.doRegistration(beanToRegistrationRequest(self.bean),
                    onSuccess, onError
                );
                return true;
            };

            return self;
        }

        /**
         *
         */
        function KeaIntegration(baseUrl, uuid) {
            var self = this;
            var atomic = new Atomic();
            self.error = function (message, data) {
                console.error("[KEA_ERROR]:" + message + ((data != undefined) ? JSON.stringify(data) : ""));
                atomic.post(baseUrl + "api/api/log/plugin/error",
                    JSON.stringify(enrichData({message: message, data: data})),
                    {contentType: 'application/json'}
                );
            };
            self.info = function (data) {
                atomic.post(baseUrl + "api/log/plugin/info",
                    JSON.stringify(enrichData(data)),
                    {contentType: 'application/json'}
                );
            };
            self.doRegistration = function (data, onSuccess, onError) {
                if (TEST_MODE) {
                    setTimeout(function () {
                        //onError({"code":"1","details":"Email already registered. errorId\u003d2","message":"error"}, null);
                        onSuccess({
                                "details": "Successfully registered",
                                "shopId": "5828480831da90879ea05046",
                                "status": "ok",
                                "token": "7ca5048ed8b8461893cd042f556ca33b",
                                "secret": "8ca5048ed8b8461893cd042f556ca33b"
                            }
                        );
                    }, 1500);
                } else {
                    var request = atomic.post(baseUrl + "cabinet/register",
                        JSON.stringify(data),
                        {contentType: 'application/json'}
                    );
                    request.success(function (data, xhr) {
                        onSuccess(data);
                    });
                    request.error(function (data, xhr) {
                        onError(data, xhr);
                    });
                }
            };
            function enrichData(data) {
                return $.extend({
                    platform: "webasyst", plugin: "kealabs",
                    installationId: uuid, hostname: window.location.hostname
                }, data);
            }
            return self;
        }

        /**
         * Wrapper on top of XHR
         */
        function Atomic() {
            var exports = {};
            var parse = function parse(req) {
                var result;
                try {
                    result = JSON.parse(req.responseText);
                } catch (e) {
                    result = req.responseText;
                }
                return [result, req];
            };

            var xhr = function xhr(type, url, data, params) {
                var methods = {
                    success: function success() {
                    },
                    error: function error() {
                    }
                };
                var XHR = window.XMLHttpRequest || ActiveXObject;
                var request = new XHR('MSXML2.XMLHTTP.3.0');
                request.open(type, url, true);
                request.withCredentials = true;
                request.crossDomain = true;
                if (params != undefined && params.contentType != undefined) {
                    request.setRequestHeader('content-type', params.contentType);
                } else {
                    request.setRequestHeader('content-type', 'application/x-www-form-urlencoded');
                }
                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status === 200) {
                            methods.success.apply(methods, parse(request));
                        } else {
                            methods.error.apply(methods, parse(request));
                        }
                    }
                };
                request.send(data);
                return {
                    request: request, //handler
                    success: function success(callback) {
                        methods.success = callback;
                        return methods;
                    },
                    error: function error(callback) {
                        methods.error = callback;
                        return methods;
                    }
                };
            };

            exports['get'] = function (src, params) {
                return xhr('GET', src, params);
            };

            exports['put'] = function (url, data, params) {
                return xhr('PUT', url, data, params);
            };

            exports['post'] = function (url, data, params) {
                return xhr('POST', url, data, params);
            };

            exports['delete'] = function (url, params) {
                return xhr('DELETE', url, params);
            };
            return exports;
        }

    });
})(jQuery);