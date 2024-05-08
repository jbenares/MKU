<?php
require_once(dirname(__FILE__).'/../conf/ucs.conf.php');
require_once(dirname(__FILE__).'/../library/lib.php');
class Fabrication{

	public static function getRawMaterialsUsed($from_date,$to_date,$project_id,$mat_type="ALL"){
		$sql = "
			select
				d.*,project_name, stock, h.date
			from
				fabrication as h
				inner join fabrication_raw_mat as d on h.fabrication_id = d.fabrication_id
				inner join productmaster as p on p.stock_id = d.raw_mat_stock_id
				left join projects as pr on pr.project_id = h.to_project_id
			where
				h.status != 'C'			
			and date between '$from_date' and '$to_date'
			and raw_mat_void = '0'
		";		

		if( !empty($project_id) ) $sql .= " and to_project_id = '$project_id'";

		if( $mat_type == "RM" ){
			$sql .= "
				-- and raw_mat_stock_id not in ( select excess_stock_id as raw_mat_stock_id from fabrication )
				and p.categ_id1 != '38'
			";
		} else if( $mat_type == "WC" ) {
			$sql .= "
				-- and raw_mat_stock_id in ( select excess_stock_id as raw_mat_stock_id from fabrication )
				and p.categ_id1 = '38'
			";
		}

		return lib::getArrayDetails($sql);

	}
	public static function getWasteCutMaterials(){
		$sql = "
			select
				p.stock_id, p.stock
			from
				fabrication as f
			inner join productmaster as p on f.excess_stock_id = p.stock_id
			where
				f.status != 'C'
			and excess_stock_id != '0'
			and stock not like '%( 0.0000 MTRS )%'
			group by p.stock_id
			order by stock asc
		";

		return lib::getArrayDetails($sql);
	}	

	public static function getWasteCutBalance($date){
		$arr = self::getWasteCutMaterials();

		foreach($arr as &$r){
			$r['wc_produced'] = self::getWasteCutQuantityProduced($r['stock_id'],$date);
			$r['wc_used']     = self::getWasteCutQuantityUsed($r['stock_id'],$date);
			$r['wc_balance']  = $r['wc_produced'] - $r['wc_used'];
		}

		return $arr;
	}

	public static function getWasteCutQuantityProduced($stock_id,$date){
		$sql = "
			select
				ifnull(sum(excess_quantity),0) as quantity
			from
				fabrication 			
			where
				status != 'C'
			and excess_stock_id = '$stock_id'
			and date <= '$date'
		";

		return DB::conn()->query($sql)->fetch_object()->quantity;
	}

	public static function getWasteCutQuantityUsed($stock_id,$date){
		$sql = "
			select
				ifnull(sum(raw_mat_quantity),0) as quantity
			from
				fabrication as h
				inner join fabrication_raw_mat as d on h.fabrication_id = d.fabrication_id
			where
				h.status != 'C'
			and raw_mat_stock_id = '$stock_id'
			and date <= '$date'
			and raw_mat_void = '0'
		";

		return DB::conn()->query($sql)->fetch_object()->quantity;
	}
}
?>