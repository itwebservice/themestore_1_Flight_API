<?php
include "../../../model/model.php";

$vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : 0;

$sq_vehicle_entries = mysqlQuery("select * from car_rental_vendor_vehicle_entries where vendor_id='$vendor_id'");
while($row_veh = mysqli_fetch_assoc($sq_vehicle_entries)){
	?>
	<option value="<?= $row_veh['vehicle_id'] ?>"><?= $row_veh['vehicle_name'].'('.$row_veh['vehicle_no'].')' ?></option>
	<?php
}
?>