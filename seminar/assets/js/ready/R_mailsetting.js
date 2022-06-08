$(document).ready(function() {
    $('#mnEmail').css('color','#cc3300');
    // Load list table Seminar Email
    loadSeminarMailList();

    $("#FromMail").on("blur", function() {
        $(this).val( $(this).val().trim() );
    });

    $("#Username").on("blur", function() {
        $(this).val( $(this).val().trim() );
    });

    $("#MailTest").on("blur", function() {
        $(this).val( $(this).val().trim() );
    });

    $("#Port").keydown(function(e) {
        isNumber(e);
    });

    $('input[type=radio][name=EncriptionType]').change(function() {
        if (this.value == '0') {
            $("#Port").prop('disabled', true);
            $('#Port').val("");
        }
        else if (this.value == '1') {
            $("#Port").prop('disabled', false);
            $('#Port').val("465");
        }
        else if (this.value == '2') {
            $("#Port").prop('disabled', false);
            $('#Port').val("587");
        }
    });

    $('#checkSmtp').change(function() {
        if ($(this).is(':checked')) {
            $('#Username').removeAttr('disabled').removeClass('disable');
            $('#Password').removeAttr('disabled').removeClass('disable');
        }
        else {
            $('#Username').attr({'disabled':'true'}).addClass('disable').val('');
            $('#Password').attr({'disabled':'true'}).addClass('disable').val('');
        }
    });
});