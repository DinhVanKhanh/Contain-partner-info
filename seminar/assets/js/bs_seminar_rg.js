// フォームの入力チェック１
function check_sub(mytype, obj, txt) {
    if (mytype == -1) {
        obj.innerHTML = txt;
    }
    else if (mytype == 0) {
        obj.innerText = txt;
    }
    else {
        obj.textContent = txt;
    }
}

// フォームの入力チェック２
function checkForm(obj) {

    var rtn = true;
    var mytype;
    var tmp = document.getElementById("id_UserCompany");
    if((document.getElementById) && navigator.appName.indexOf("Netscape") > -1) {
        mytype = -1;
    }
    else if (typeof tmp.innerText != "undefined") {
        mytype = 0;
    }
    else {
        mytype = 1;
    }

    // 貴社名
    tmp = document.getElementById("id_UserCompany")
    if (tmp) {
        if (obj.user_company.value == "") {
            check_sub(mytype, tmp, "＊必須項目です");
            rtn = false;
        }
        else { check_sub(mytype, tmp, ""); }
    }


    // 受講者氏名
    tmp = document.getElementById("id_UserName")
    if (tmp) {
        if (obj.user_name.value == "") {
            check_sub(mytype, tmp, "＊必須項目です");
            rtn = false;
        }
        else { check_sub(mytype, tmp, ""); }
    }

    // お電話番号
    tmp = document.getElementById("id_UserTel")
    if (tmp) {
        if (obj.user_tel.value == "") {
            check_sub(mytype, tmp, "＊必須項目です");
            rtn = false;
        }
        else { check_sub(mytype, tmp, ""); }
    }


    // ご住所
    tmp = document.getElementById("id_UserAddress")
    if (tmp) {
        if (obj.user_postcode1.value == "" | obj.user_postcode2.value == "" | obj.user_address.value == "") {
            check_sub(mytype, tmp, "＊郵便番号と住所はすべて必須項目です");
            rtn = false;
        }
        else { check_sub(mytype, tmp, ""); }
    }


    // ご住所
    tmp = document.getElementById("id_UserSerialNo")
    if (tmp) {
        if (obj.user_serialno.value == "") {
            check_sub(mytype, tmp, "＊必須項目です");
            rtn = false;
        }
        else { check_sub(mytype, tmp, ""); }
    }


    //【最終処理】 問題があればfalseを返す。なければsubmitする。
    if (rtn == false) {
        alert("恐れ入りますがもう一度入力内容をご確認ください。");
        return rtn;
    }
    document.inputform.submit();
}