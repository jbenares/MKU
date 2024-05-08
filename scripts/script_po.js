// JavaScript Document
j(document).ready(function(){
	j("#stock_id").change(function(){
		xajax_po_getCostOfStock(xajax.getFormValues('header_form'));
	});
});
