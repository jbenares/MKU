// JavaScript Document

j(document).ready(function(){
	
				
	j("#addButton").click(function(){
		xajax_addDRDetails(xajax.getFormValues('header_form'));
	});
	
	j("#stock_id").change(function(){
		xajax_getSRP(xajax.getFormValues('header_form'));	
		//xajax_displayCurrentBalance(xajax.getFormValues('header_form'));
	});
	
	j("#package_id").change(function(){
		xajax_getSRP(xajax.getFormValues('header_form'));	
	});
	
	j("#quantity,#discount_detail,#price").keyup(function(){
		xajax_getSRP(xajax.getFormValues('header_form'));	
	});
	
	j("#returnButton").click(function(){
		xajax_addDRReturns(xajax.getFormValues('header_form'));
	});
	
	j("#adjustmentButton").click(function(){
		xajax_addDRAdjustments(xajax.getFormValues('header_form'));
	});
});

