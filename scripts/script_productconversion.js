// JavaScript Document
	
j(function(){
	j("#header_form").validate({
		errorContainer: "#messageError",
		errorLabelContainer: "#messageError ul",
		wrapper: "li"
	});
	
	/*j("#finishedproduct_id").change(function(){
		queryPackageType();
	});*/
	
	j("#packagetype").change(function(){
		//queryPackageQty();
		computeConversionQty();
	});
	
	/*j("#locale_id").change(function(){
		queryPackageQty();
	})*/
	
	j("#packqty").keyup(function(){
		computeConversionQty();
	});
});	


	
function queryPackageType(){
	var form_data={
		query:"queryPackageType",
		finishedproduct_id:j("#finishedproduct_id").val(),
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#packagetype").html(data);
			}
	});		
}

function queryPackageQty(){
	
	if(j("#locale_id").val()==''){
		alert("Select Location First");
		return 0;
	}
	
	var form_data={
		query:"queryPackageQty",
		finishedproduct_id:j("#finishedproduct_id").val(),
		packagetype:j("#packagetype").val(),
		locale_id:j("#locale_id").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#packqty").val(data);
				computeConversionQty();
			}
	});		
}

function computeConversionQty(){
	
	var form_data={
		query:"computeConversionQty",
		packqty:j("#packqty").val(),
		packagetype:j("#packagetype").val()
	};
		
	j.ajax({
		url: "ajax.php",
		data: form_data,
		type: 'POST',
		success:
			function(data){
				j("#qty").val(data);
			}
	});		
}