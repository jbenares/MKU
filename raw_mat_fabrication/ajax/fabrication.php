<?php
require_once(dirname(__FILE__).'/../../conf/ucs.conf.php');
require_once(dirname(__FILE__).'/../../library/lib.php');
require_once(dirname(__FILE__).'/../../my_Classes/options.class.php');

if( !empty($_REQUEST['action']) ) call_user_func($_REQUEST['action'], $_REQUEST['data']);

function generateStockID($form_data){
	$options = new options();

	$aReturn = array();
	$aReturn['error_flag'] = 0;
	/*check the raw material of fabrication*/
	$sql = "
        select
            *
        from
            fabrication_raw_mat as f
            inner join productmaster as p on f.raw_mat_stock_id = p.stock_id
        where
            fabrication_id = '$form_data[fabrication_id]'
        and raw_mat_void = '0'
    ";

	$obj_raw_mat      = DB::conn()->query($sql)->fetch_object();
	$raw_mat_stock_id = $obj_raw_mat->raw_mat_stock_id;

	/*if no raw_mat_stock_id then error*/
	if( empty($raw_mat_stock_id) ){
		$aReturn['error_flag'] = 1;
		$aReturn['error']      = "No Raw Materials Found";
		echo json_encode($aReturn);
		return false;
	} else if( $form_data['excess_quantity'] <= 0 ){
		$aReturn['error_flag'] = 1;
		$aReturn['error']      = "Unable to generate waste cat material. Waste Cut Material should be more than 0";
		echo json_encode($aReturn);
		return false;
	}

	/*check if stock id exists */
	$sql = "
		select 
			*
		from 
			productmaster
		where
			fabrication_raw_mat_parent_id = '$raw_mat_stock_id'
		and stock_length = '$form_data[excess_length]'
	";
	
    $result = DB::conn()->query($sql);
    if( $result->num_rows >= 1 ){
    	$obj = $result->fetch_object();
    	$aReturn['stock'] = array("stock_id" => $obj->stock_id, "stock" => $obj->stock);
    	updateExcessStockOfFabrication(array("stock_id" => $obj->stock_id, "fabrication_id" => $form_data['fabrication_id'] ));

    } else {
    	/*create stock item*/
		$arr_stock              = lib::getTableAttributes("select * from productmaster where stock_id = '$raw_mat_stock_id'");
		$waste_cut_stock_name   = "WC-".$arr_stock['stock']." ( $form_data[excess_length] MTRS )";
		$waste_cut_stock_length = $form_data['excess_length'];
		$waste_cut_kg           = $form_data['excess_weight_per_unit'];

		$new_stockcode = $options->new_stockcode($arr_stock['categ_id1']);

		DB::conn()->query("
			insert into
				productmaster
			set
				stock = '$waste_cut_stock_name',
				stock_length = '$waste_cut_stock_length',
				kg = '$waste_cut_kg',
				stockcode = '$new_stockcode',
				fabrication_raw_mat_parent_id = '$raw_mat_stock_id'
		");

		$waste_cut_stock_id = DB::conn()->insert_id;
		$aReturn['stock'] = array("stock_id" => $waste_cut_stock_id, "stock" => $waste_cut_stock_name);
		updateExcessStockOfFabrication(array("stock_id" => $waste_cut_stock_id, "fabrication_id" => $form_data['fabrication_id'] ));
    }

    echo json_encode($aReturn);
}

function updateExcessStockOfFabrication($arr){
	DB::conn()->query("
		update
			fabrication
		set
			excess_stock_id = '$arr[stock_id]'		
		where
			fabrication_id = '$arr[fabrication_id]'
	");

}
?>