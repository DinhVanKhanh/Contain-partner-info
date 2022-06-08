$(document).ready(function(){
	$('#mnItems').css('color','#cc3300');	
	bindInputCodeData('TypeCode');
	//load table danh sach Types
	loadItemsList();
	$('#delItem').click(function(){
		deleteItem();
	});	
	
	$('input[name="submit"]').click(function(){	
		if($('#TypeCode').val()==''){
			$('.error_inline0').html('項目コードは未入力です。');	
			$('#TypeCode').focus();
			return false;
		}
		else if($('#TypeName').val()==''){
			$('.error_inline0').html('項目名は未入力です。');	
			$('#TypeName').focus();
			return false;
		}
		else if($('#Type').val()==''){
			$('.error_inline0').html('終類は未入力です。');	
			$('#txtAcode').focus();
			return false;
		}
		else if($('#TypeCode').val().length>12){
			$('.error_inline0').html('項目コードを12文字以内で入力してください。');	
			$('#TypeCode').focus();
			return false;
		}
		else if($('#TypeName').val().length>30){
			$('.error_inline0').html('項目名を30文字以内で入力してください。');	
			$('#TypeName').focus();
			return false;
		}
		var params = new Object();
		params.Type = $('#Type').val();
		params.TypeCode = $('#TypeCode').val();
		params.TypeName = $('#TypeName').val();
		params.isEdit = $('#isEdit').val();
		params.TypeId = $('#TypeId').val();
		saveItems(params);
	});
});