$(document).ready(function () {
    $('#mnSeminar').css('color', '#cc3300');
    bindInputCodeData('SeminarName');
    bindInputCodeData('VenueName');
    bindInputCodeData('VenueAddress');
    bindInputCodeData('scDate');
    bindInputCodeData('TimeStart');
    bindInputCodeData('TimeEnd');
    $('.fancyboxSC').fancybox({
        width: 900,
        height: 460
    });

    // Load list Seminar B
    loadSeminarList();

    $('#btnImport').click(function () {
        uploadSeminarB();
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

    $('#scDate').datepicker();
    $('#scDate').datepicker("option", "dateFormat", 'yy-mm-dd');
    $('#scDate').datepicker("option", "minDate", 0);
});