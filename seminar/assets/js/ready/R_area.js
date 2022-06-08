$(document).ready(function() {
	bindInputCodeData('txtAcode');
	$('#mnArea').css('color', '#cc3300');

	// Load list Area
	loadAreaList();
	
	$('#submit_del').click(function() {
		deleteArea();
	});
	
	$('#submit_del_ok').click(function () {
		$.fancybox.close();
		setTimeout(() => {
			$('.message').html('この領域を削除してもよろしいですか？');
			$('#submit_del_ok').hide();
			$('#submit_del').show();
			$('.btnClose').show();
		}, 500);
		loadAreaList();
	});

	// Order data	
	$('#up').click(function() {
		let ckRow = $('#checkRowId').val();
		if(ckRow != "" && ckRow != "undefined" ) {
			let thisRow = $('#'+ckRow).closest('tr');
			let prevRow = thisRow.prev();
			if (prevRow.length && prevRow.index() != 0) {
				prevRow.before(thisRow);
				let params = new Object();
				params.curId = ckRow;
				params.curIdx = prevRow.index();
				params.upIdx = thisRow.index();
				params.upId = prevRow.children().find('input:checkbox')[0].id;
				changeAreaRow(params);
			}
		}
	});

	$('#down').click(function() {
		let ckRow = $('#checkRowId').val();
		if( ckRow != "" && ckRow != "undefined" ) {
			let thisRow = $('#'+ckRow).closest('tr');
			let nextRow = thisRow.next();
			if (nextRow.length) {
				nextRow.after(thisRow);
				let params = new Object();
				params.curId = ckRow;
				params.curIdx = nextRow.index();
				params.upIdx = thisRow.index();
				params.upId = nextRow.children().find('input:checkbox')[0].id;
				changeAreaRow(params);
			}
		}
	});
});