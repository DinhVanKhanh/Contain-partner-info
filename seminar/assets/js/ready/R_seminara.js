$(document).ready(function () {
    $('#mnSearch').css('color', '#cc3300');
    bindInputCodeData('SeminarName');
    bindInputCodeData('VenueName');
    bindInputCodeData('VenueAddress');
    bindInputCodeData('scDate');
    bindInputCodeData('TimeStart');
    bindInputCodeData('TimeEnd');
    bindInputCodeData('SeminarFees');
    bindInputCodeData('VenueStation');
    bindInputCodeData('CountPerson');
    bindInputCodeDataClass('html_editor');
    bindInputContact("ContactTel", contact);
	bindInputContact("ContactFax", contact);
	bindInputContact("CountPerson", number);
    $('.fancyboxSC').fancybox({
        width: 900,
        height: 460
    });

    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.replace(new RegExp(search, 'g'), replacement);
    };

    // Load list Seminar A
    loadSeminarList();

    $('#btnImport').click(function () {
        uploadSeminarA();
    });

    // Date
	$('#scDate').click(function () {
		$('#scDate').select();
	});

	$('#scDate').datepicker({
		onSelect: function () {
			appdate();
		},
		dateFormat: 'yy-m-d',
	});

	$("#scDate").keydown(function (e) {
		if (e.keyCode == 13) {
			appdate();
		}
	});

	$("#scDate").blur(function () {
		appdate();
		dateFormat: 'yy-m-d';
	});

    $('#TimeStart').mask('99:99');
    $('#TimeEnd').mask('99:99');
    $('#TimeStart').timepicker();
    $('#TimeEnd').timepicker();
    $('#datepair .time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i'
    });

    // just enter Fax number by number , not text
    $("#SeminarFees").keydown(function (e) {
        $(this).val($(this).val().replace(',', ''));
        bindInputContact(e, number);
    });

    $("#SeminarFees").blur(function (e) {
        let $this = $(this);
		let val = $this.val();
        if ( val != '' ) {
            val = Number( val.replaceAll(',', '') );
            $(this).val( numeral(val).format('0,0') );
        }
    });

	// Person
	$("#CountPerson").blur(function (e) {
		let $this = $(this);
		let val = $this.val();
		if (val != '') {
			$this.val(parseInt(val));
		}
	});

    $('#scDate').datepicker();
    $('#scDate').datepicker("option", "dateFormat", 'yy-mm-dd');
    $('#scDate').datepicker("option", "minDate", 0);
});