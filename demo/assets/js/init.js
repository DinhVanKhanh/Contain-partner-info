/**
 * Show message thong bao
 * @param: message
 * @param: title
 * @param: msType: success, info, warning, danger
 * @param: msPos : top-left, top-right, bottom-left, bottom-right, center
 * @author: HauPhan
 * @since: 07/04/2016
 */

/**
 * Filter data input HashMap
**/
function HashMap() {
    // members
    this.keyArray = new Array(); // Keys
    this.valArray = new Array(); // Values

    // methods
    this.put = put;
    this.get = get;
    this.size = size;
    this.clear = clear;
    this.keySet = keySet;
    this.valSet = valSet;
    this.showMe = showMe; // returns a string with all keys and values in map.
    this.findIt = findIt;
    this.remove = remove;
}

function put(key, val) {
    var elementIndex = this.findIt(key);

    if (elementIndex == (-1)) {
        this.keyArray.push(key);
        this.valArray.push(val);
    } else {
        this.valArray[elementIndex] = val;
    }
}

function get(key) {
    var result = null;
    var elementIndex = this.findIt(key);

    if (elementIndex != (-1)) {
        result = this.valArray[elementIndex];
    }

    return result;
}

function remove(key) {
    index = this.keyArray.indexOf(key);
    this.valArray.splice(index, 1);
    this.keyArray.splice(index, 1);
}

function size() {
    return (this.keyArray.length);
}

function clear() {
    for (var i = 0; i < this.keyArray.length; i++) {
        this.keyArray.pop();
        this.valArray.pop();
    }
}

function keySet() {
    return (this.keyArray);
}

function valSet() {
    return (this.valArray);
}

function showMe() {
    var result = "";

    for (var i = 0; i < this.keyArray.length; i++) {
        result += "Key: " + this.keyArray[i] + "\tValues: " + this.valArray[i] + "\n";
    }
    return result;
}

function findIt(key) {
    var result = (-1);

    for (var i = 0; i < this.keyArray.length; i++) {
        if (this.keyArray[i] == key) {
            result = i;
            break;
        }
    }
    return result;
}

function showMessage(message, title, msType, msPos, isAutoClose) {
    timeClose = 3000;
    if (msPos == "" || msPos == null) {
        msPos = "top-right";
    }
    if (msType == "" || msType == null) {
        msType = "danger";
    }
    if (isAutoClose == "" || isAutoClose == null) {
        isAutoClose = true;
    } else {
        timeClose = 0;
    }
    $.alert(message, {
        title: title,
        closeTime: timeClose,
        autoClose: isAutoClose,
        position: [msPos],
        withTime: false,
        type: msType,
        isOnly: true
    });
}

/**
 * Bind input value: Ignoor Japanese character, specials character
 * @author HauPhan
 * @since 2016/05/18
 */
function bindInputCodeData(inputId) {
    $("#" + inputId).on("keydown", function (event) {
        // Ignore controls such as backspace
        var arr = [8, 9, 16, 17, 20, 35, 36, 37, 38, 39, 40, 45, 46, 109, 173];
        // Allow letters
        for (var i = 65; i <= 90; i++) {
            arr.push(i);
        }
        for (var i = 96; i <= 105; i++) {
            arr.push(i);
        }
        for (var i = 48; i <= 57; i++) {
            arr.push(i);
        }
        if (jQuery.inArray(event.which, arr) === -1) {
            event.preventDefault();
        }

    });

    $("#" + inputId).on("input", function () {
        var regexp = /[^a-zA-Z0-9-_]/g;
        if ($(this).val().match(regexp)) {
            $(this).val($(this).val().replace(regexp, ''));
        }
    });
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function unescapeHtml(safe) {
    if (safe != null && safe != "" && safe != undefined) {
        return safe.replace(/&amp;/g, '&')
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&quot;/g, '"')
            .replace(/&#039;/g, "'");
    }
}

function encrypt(string) {
    let text = btoa('qweadszxc') + btoa( string );
    return text;
}