/**
 * Created by wuwentao on 2015/12/30.
 */

/**
 * jQuery plugins
 */
(function($) {
    $.fn.extend({
        // bind enter button
        bindEnter: function(callback) {
            if (callback) {
                $(this).keydown(function (e) {
                    if (e.keyCode == 13) {
                        callback();
                    }
                })
            }
        },
        // redirect to url after sec seconds
        redirectAfter: function(url, sec) {
            if (sec > 0) {
                var div = $(this);
                div.html('<p>Remain <span style="color: #8B0000;" id="span_sec">' + Math.ceil(sec) + '</span> s to redirect.</p>');
                var d = setInterval(function() {
                    sec--;
                    div.find('#span_sec').text(Math.ceil(sec));
                    if (sec <= 0) {
                        clearInterval(d);
                        location.href = url;
                    }
                }, 1000);
            } else {
                location.href = url;
            }
        },
        // load content
        loadContent: function(options) {
            var opts = $.extend({}, loadContentDefault, options);
            if (!opts.hasOwnProperty('context')) {
                opts['context'] = $(this);
            }
            $.ajax(opts);
        }
    });
    var loadContentDefault = {

    };
}(jQuery));

/**
 * Redirect to home.
 */
function toHome() {
    location.href = '/';
}


/**
 * Check if value is array.
 *
 * @param value
 * @returns {boolean}
 */
function isArrayFn(value){
    if (typeof Array.isArray === "function") {
        return Array.isArray(value);
    }else{
        return Object.prototype.toString.call(value) === "[object Array]";
    }
}

/**
 * Check if value is object.
 * @param value
 * @returns {boolean}
 */
function isObjectFn(value){
    return (Object.prototype.toString.call(value) == "[object Object]");
}

/**
 * Check if value is null or undefined.
 *
 * @param value
 * @returns {boolean}
 */
function isNullOrUndefined(value) {
    return (Object.prototype.toString.call(value) == "[object Null]" || Object.prototype.toString.call(value) == "[object Undefined]");
}

/**
 * Check if value is empty.
 *
 * @param obj
 * @returns {boolean}
 */
function isEmptyObject(obj) {
    for (var i in obj) {
        return false;
    }
    return true;
}


/**
 *
 * Remove item in array, return new array.
 *
 * @param arr array
 * @param ele
 * @returns {*}
 */
function removeItem(arr, ele) {
    var pos = arr.indexOf(ele);
    if (pos >= -1) {
        var arro = [];
        for (var i in arr) {
            if (pos != i) {
                arro.push(arr[i]);
            }
        }
        return arro;
    }
    return arr;
}

/**
 * Get length of array or json object.
 *
 * @param jsonData
 * @returns {number}
 */
function getJsonLength(jsonData){
    var jsonLength = 0;
    for(var item in jsonData){
        jsonLength++;
    }
    return jsonLength;
}

function ajaxHttp(settings, method) {
    if ('headers' in settings) {
        var h = settings['headers'];
        h['X-HTTP-Method-Override'] = method;
    } else {
        h = {'X-HTTP-Method-Override': method};
    }
    settings['headers'] = h;
    $.ajax(settings)
}

function ajaxPut(settings) {
    ajaxHttp(settings, 'PUT');
}

function ajaxPatch(settings) {
    ajaxHttp(settings, 'PATCH');
}

function ajaxDelete(settings) {
    ajaxHttp(settings, 'DELETE');
}

/**
 *
 * @param form_id
 * @param ajax_settings
 * @returns {boolean}
 */
function postForm(form_id, ajax_settings)
{
    var form = $('#' + form_id);
    if (form.valid()) {
        var fd = new FormData(document.getElementById(form_id));
        var default_setting = {
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST'
        };
        ajax_settings = $.extend(default_setting, ajax_settings);
        $.ajax(ajax_settings);
        return true;
    } else {
        return false;
    }
}

function putForm(form_id, ajax_settings)
{
    var form = $('#' + form_id);
    if (form.valid()) {
        var fd = new FormData(document.getElementById(form_id));
        var default_setting = {
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST'
        };
        ajax_settings = $.extend(default_setting, ajax_settings);
        ajaxPut(ajax_settings);
        return true;
    } else {
        return false;
    }
}

function patchForm(form_id, ajax_settings)
{
    var form = $('#' + form_id);
    if (form.valid()) {
        var fd = new FormData(document.getElementById(form_id));
        var default_setting = {
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST'
        };
        ajax_settings = $.extend(default_setting, ajax_settings);
        ajaxPatch(ajax_settings);
        return true;
    } else {
        return false;
    }
}

/**
 * Post data and redirect to url.
 *
 * @param url
 * @param pdata
 * @param new_window
 */
function postDirect(url, pdata, new_window)
{
    var id = 'form_' + new Date().getTime();
    var form = $("<form method='post' id='" + id + "' class='hidden'></form>");
    form.attr({"action":url});
    if (new_window) {
        form.prop('target', '_blank');
    }
    for (var arg in pdata)
    {
        var input = $("<input type='hidden'>");
        input.attr({"name": arg});
        input.val(pdata[arg]);
        form.append(input);
    }
    var bt_submit = $('<input type="submit" id="' + id + '_submit">');
    form.append(bt_submit);
    $(document.body).append(form);
    $('#' + id + '_submit').click();
}


/**
 * Check if it is integer.
 *
 * @param val
 * @returns {boolean}
 */
function isInteger(val) {
    return Math.floor(val) == val;
}

/**
 * Check email.
 *
 * @param val
 * @returns {boolean}
 */
function isEmail(val) {
    return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(val);
}

/**
 * Check telephone.
 *
 * @param val
 * @returns {boolean}
 */
function isTelephone(val) {
    return /^[1][0-9][0-9]{9}$/.test(val);
}

/**
 * Check if is json.
 *
 * @param obj
 * @returns {boolean}
 */
function isJson(obj) {
    return typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
}

/**
 *
 * @param form_id
 * @returns {{}}
 */
function getFormParams(form_id) {
    var params = {};
    $('#' + form_id + ' input').each(function() {
        if ($.trim($(this).val()) && $.trim($(this).attr('name'))) {
            params[$(this).attr('name')] = $(this).val();
        }
    });
    return params;
}
