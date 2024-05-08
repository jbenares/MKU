<?php
require_once("my_Classes/depreciation.class.php");
?>
<style type="text/css">
input.ui-button{
	padding:3px 5px;
	font-size:10px;
}

</style>

<script type="text/javascript">
	j(document).ready(function(){
		j.ajaxSetup({
			beforeSend:
				function(){
					toggleBox('demodiv',1);
				},
			complete:
				function(){
					toggleBox('demodiv',0);
				}
		});
	});

	j(document).ready(function(){
		j("#header_form").validate({
			errorContainer: "#messageError",
			errorLabelContainer: "#messageError ul",
			wrapper: "li"
		});

		j("#_form").validate({
			errorContainer: "#messageError",
			errorLabelContainer: "#messageError ul",
			wrapper: "li"
		});

	});

	j(document).keyup(function(e){
			//alert(e.keyCode);
	});

	j(function(){
		j(".datepicker").each(function(){
				j(this).datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true
			});
		});

		j("#account_name").autocomplete({
			source: "customers.php",
			minLength: 2,
			select: function(event, ui) {
				j("#account_name").val(ui.item.value);
				j("#account_id").val(ui.item.id);
			}
		});

		j("#branding_num").autocomplete({
			source: "branding.php",
			minLength: 2,
			select: function(event, ui) {
				j("#branding_num").val(ui.item.value);
			}
		});

		j(".accounts").autocomplete({
			source: "dd_accounts.php",
			minLength: 1,
			select: function(event, ui) {
				j(this).val(ui.item.value);
				j(this).next().val(ui.item.id);
			}
		});

		j("#stock_name").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j("#stock_name").val(ui.item.value);
				j("#stock_id").val(ui.item.id);
				j("#price").val(ui.item.price);
				j("#cost").val(ui.item.cost);
				j("#unit").val(ui.item.unit);
				j("#buffer").val(ui.item.buffer);
				j("#amount,#quantity").val("");

				if(j("#div_warehouse_qty").length != 0){
					xajax_display_warehouse_qty(ui.item.id);
				}
			}
		});

		j(".stock_name").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j(this).val(ui.item.value);
				j(this).next().val(ui.item.id);
				j("#price1").val(ui.item.price1);

				jQuery("#rate_per_hour").val(ui.item.rate_per_hour);
				
				if(j("#div_warehouse_qty2").length != 0){
					xajax_display_warehouse_qty(ui.item.id,"warehouse_qty2");
				}
			}
		});

		j(".stock_name2").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j(this).val(ui.item.value);
				j(this).next().val(ui.item.id);
				j("#price1").val(ui.item.price1);

				jQuery("#rate_per_hour").val(ui.item.rate_per_hour);
				
				if(j("#div_warehouse_qty2").length != 0){
					xajax_display_warehouse_qty(ui.item.id,"warehouse_qty2");
				}
			}
		});

		j("#service_name").autocomplete({
			source: "dd_service.php",
			minLength: 2,
			select: function(event, ui) {
				j("#service_name").val(ui.item.value);
				j("#service_id").val(ui.item.id);
				j("#service_price").val(ui.item.price);
				j("#service_cost").val(ui.item.cost);
				j("#service_amount,#service_days,#service_rate,#service_quantity").val("");
			}
		});

		j("#equipment_name").autocomplete({
			source: "dd_equipment.php",
			minLength: 2,
			select: function(event, ui) {
				j("#equipment_name").val(ui.item.value);
				j("#equipment_id").val(ui.item.id);
				j("#equipment_price").val(ui.item.price);
				j("#equipment_cost").val(ui.item.cost);
				j("#equipment_amount,#equipment_days,#equipment_rate,#equipment_quantity").val("");
			}
		});

		j(".equipment_name").autocomplete({
			source: "dd_equipment.php",
			minLength: 2,
			select: function(event, ui) {
				j(this).val(ui.item.value);
				j(this).next().val(ui.item.id);
			}
		});


		j("#stock_name_pricelist").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j("#stock_name").val(ui.item.value);
				j("#stock_id").val(ui.item.id);
				j("#amount,#quantity").val("");
				xajax_getPriceListOfStock(ui.item.id);
			}
		});


		j("#main_name").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j("#main_name").val(ui.item.value);
				j("#main_id").val(ui.item.id);
			}
		});

		j("#product_name").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j("#product_name").val(ui.item.value);
				j("#product_id").val(ui.item.id);
			}
		});

		j("#piece_stock").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j("#piece_stock").val(ui.item.value);
				j("#piece_stock_id").val(ui.item.id);
			}
		});

		j("#pack_stock").autocomplete({
			source: "stocks.php",
			minLength: 2,
			select: function(event, ui) {
				j("#pack_stock").val(ui.item.value);
				j("#pack_stock_id").val(ui.item.id);
			}
		});

		j("#supplier_name").autocomplete({
			source: "dd_suppliers.php",
			minLength: 1,
			select: function(event, ui) {
				j("#supplier_name").val(ui.item.value);
				j("#account_id").val(ui.item.id);
				//j("#term").val(ui.item.term);
				if(j("#div_terms").length != 0){
					xajax_get_supplier_term(ui.item.id);
				}
			}
		});

		j("#supplier_name2").autocomplete({
			source: "dd_stock.php",
			minLength: 2,
			select: function(event, ui) {
				j("#supplier_name1").val(ui.item.value);
				j("#account_id").val(ui.item.id);
			}
		});

		j("#supplier_name2").autocomplete({
			source: "dd_suppliers.php",
			minLength: 1,
			select: function(event, ui) {
				j("#supplier_name2").val(ui.item.value);
				j("#account_id").val(ui.item.id);
			}
		});

		j(".supplier").autocomplete({
			source: "dd_suppliers.php",
			minLength: 1,
			select: function(event, ui) {
				j(this).val(ui.item.value);
				j(this).next().val(ui.item.id);
			}
		});

		j("#contractor_name").autocomplete({
			source: "dd_contractors.php",
			minLength: 1,
			select: function(event, ui) {
				j("#contractor_name").val(ui.item.value);
				j("#contractor_id").val(ui.item.id);
			}
		});

		j("#project_name").autocomplete({
			source: "dd_projects.php",
			minLength: 1,
			select: function(event, ui) {
				j("#project_name").val(ui.item.value);
				j("#project_id").val(ui.item.id);

				if(j("#div_scope_of_work").length != 0){
					xajax_update_scope_of_work(ui.item.id);
				}
			}
		});

		j(".project").autocomplete({
			source: "dd_projects.php",
			minLength: 1,
			select: function(event, ui) {
				j(this).val(ui.item.value);
				j(this).next().val(ui.item.id);
			}
		});

		j("#po_name").autocomplete({
			source: "dd_po.php",
			minLength: 1,
			select: function(event, ui) {
				j("#po_name").val(ui.item.value);
				j("#po_header_id").val(ui.item.id);
				j("#supplier_name").val(ui.item.supplier_name);
				j("#supplier_id").val(ui.item.supplier_id);

				xajax_getProjectFromPO(ui.item.id);
			}
		});

		j("#pr_name").autocomplete({
			source: "dd_purchase_request.php",
			minLength: 1,
			select: function(event, ui) {
				j("#pr_name").val(ui.item.value);
				j("#pr_header_id").val(ui.item.id);

				xajax_getProjectFromPR(ui.item.id);
			}
		});

		j("#radio").buttonset();

		//j( "input:submit, input:button, .buttons , input:reset").button();

		j("#dialog").dialog({autoOpen: false , modal:true , show: 'slide' , hide : 'slide' , width : 'auto', resizable : false, height : 'auto', maxHeight : 600});
		j("#dialog2").dialog({autoOpen: false , modal:false , show: 'fade' , hide : 'fade' , width : 'auto', resizable : false , maxHeight : 600});

	});

	function openDialog(){
		j("#dialog").dialog("open");
	}

	function closeDialog(){
		j("#dialog").dialog("close");
	}

	function openDialog2(){
		j("#dialog2").dialog("open");
	}

	function closeDialog2(){
		j("#dialog2").dialog("close");
	}

</script>

<div id="dialog" style="padding:0px;">
	<form id="dialog_form" name="dialog_form" onSubmit="return false;">
	<div id="dialog_content">

	</div>
	</form>
</div>

<div id="dialog2">
	<div id="dialog2_content">

	</div>
</div>
