function isNumber(e) {
    var ctrlKey = 17, vKey = 86, cKey = 67;
    // Allow: backspace, delete, tab, escape, enter, ctrl+A,left -, right -, and .
    if (!($.inArray(e.keyCode, [8, 46, 9, 27, 109, 173, 13]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39) ||
        // Allow: number 0-9: numpad
        (e.keyCode >= 96 && e.keyCode <= 105) ||
        // Allow: ctrl c, ctrlv, ctrl a
        ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: number 0-9 main key
        (e.keyCode >= 48 && e.keyCode <= 57))
    ) {
        e.preventDefault();
    }
}

function serialFormat(e) {
    var ctrlKey = 17, vKey = 86, cKey = 67;
    // Allow: backspace, delete, tab, escape, enter, ctrl+A,left -, right -, and . and -
    if (!($.inArray(e.keyCode, [8, 46, 9, 27, 109, 173, 13, 189]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39) ||
        // Allow: number 0-9: numpad
        (e.keyCode >= 96 && e.keyCode <= 105) ||
        // Allow: ctrl c, ctrlv, ctrl a
        ((e.keyCode == 65 || e.keyCode == 86 || e.keyCode == 67) && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: number 0-9 main key
        (e.keyCode >= 48 && e.keyCode <= 57))
    ) {
        e.preventDefault();
    }
}
