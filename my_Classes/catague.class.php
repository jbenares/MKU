<?php
	include_once("payroll/payroll_classes/randy.options.php");
	include_once("payroll/payroll_classes/employees.class.php");
	include_once("payroll/payroll_classes/dependents.class.php");

	$randy_options = new randy_options();

	// Start Ron Classes
		# Budget Details and Sections
		include_once("budget_details/classes/sections.class.php");
		include_once("budget_details/classes/budget_details.class.php");
		include_once("budget_details/classes/bd_options.class.php");
		# Petty Cash
		include_once("petty_cash/classes/petty_cash.class.php");
		include_once("petty_cash/classes/pc_options.class.php");
		# Work Type
		include_once("work_type/classes/work_type.class.php");
		# Labor Purchase Request
		include_once("labor_budget/classes/purchaserequest.class.php");
		# Asset Circulation
		include_once("asset_circulation/classes/circulation.class.php");
		include_once("asset_circulation/classes/empc_options.class.php");
	// End Ron Classes
	
	include_once("vehicle_pass/vehicle_pass.class.php");
	
	error_reporting(E_ALL ^ E_NOTICE);
?>