<?php 
include_once('../../../../../model/model.php');

$group_id = $_POST['group_id'];

$costing_info_arr =array();
$sq_group = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$group_id'"));

$row_cost = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$sq_group[tour_id]'"));

$costing_info_arr['adult_cost'] = $row_cost['adult_cost'];
$costing_info_arr['children_wb_cost'] = $row_cost['child_without_cost'];
$costing_info_arr['infant_cost'] = $row_cost['infant_cost'];
$costing_info_arr['with_bed_cost'] = $row_cost['child_with_cost'];
$costing_info_arr['single_person_cost'] = $row_cost['single_person_cost'];



echo json_encode($costing_info_arr);
?>