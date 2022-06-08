$(document).ready(function () {
	$('#mnSeminarD').css('color', '#cc3300');
	bindInputCodeData('SeminarName');
	bindInputCodeData('CompanyName');
	bindInputCodeData('VenueName');
	bindInputCodeData('VenueAddress');
	bindInputCodeData('scDate');
	bindInputCodeData('AppDate');
	bindInputCodeData('TimeStart');
	bindInputCodeData('TimeEnd');
	bindInputCodeData('CountPerson');
	bindInputCodeDataClass('html_editor');
	bindInputDate("scDate");
	bindInputDate("day");
	bindInputDate("month");
	bindInputDate("year");
	bindInputContact("ContactTel", contact);
	bindInputContact("ContactFax", contact);
	bindInputContact("CountPerson", number);
	$('.fancyboxSC').fancybox({
		width: 900,
		height: 460
	});

	// Load list Seminar D
	loadSeminarList();

	$('#btnImport').click(function () {
		uploadSeminarD();
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

	// PDF
	$('#pdf').on('change', function () {
		$('#multiple_upload_form').ajaxForm({
			target: '#images_preview',
			beforeSubmit: function (e) {
				$('.uploading').show();
			},
			success: function (e) {
				$('.uploading').hide();
			},
			error: function (e) {}
		}).submit();
	});

	// Person
	$("#CountPerson").blur(function (e) {
		let $this = $(this);
		let val = $this.val();
		if (val != '') {
			$this.val(parseInt(val));
		}
	});
});