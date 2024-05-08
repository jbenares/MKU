// JavaScript Document
j(document).ready(function(){	
	j("#po_header_id").change(function(){
		xajax_getPODetails(xajax.getFormValues('header_form'));
	});
	
});
	
	