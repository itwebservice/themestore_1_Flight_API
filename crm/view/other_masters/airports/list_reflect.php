<?php include_once("../../../model/model.php");
$count = 0;
$status = $_POST['status'];

$array_s = array();
$temp_arr = array();
$query = "select * from airport_master where 1 ";
if($status != ''){
	$query .= " and flag='$status'";
}
$sq_airport = mysqlQuery($query);
while($row=mysqli_fetch_assoc($sq_airport)){
	$count++;
	$bg = ($row['flag']=="Inactive") ? "danger" : "";
	$sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row[city_id]'"));
	$row_airport_nam = clean($row['airport_name']);
	$airport_id = $row['airport_id'];
	$city = $sq_city['city_name'];
	$status = $row['flag'];
		$temp_arr = array( "data" => array(
			(int)($airport_id),$city,$row_airport_nam,strtoupper($row['airport_code']),$status,'<button class="btn btn-info btn-sm" data-toggle="tooltip" onclick="update_modal('. $row['airport_id'] .')" id="airport_update-'. $row['airport_id'] .'" title="Update Details"><i class="fa fa-pencil-square-o"></i></button>'), "bg" => $bg
		);
		array_push($array_s,$temp_arr); 
}
echo json_encode($array_s);
?>