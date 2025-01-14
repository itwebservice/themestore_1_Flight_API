<?php
include "../../../model/model.php";

/*======******Header******=======*/
require_once('../../layouts/admin_header.php');
require_once('../../../classes/tour_booked_seats.php');
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$emp_id = $_SESSION['emp_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$sq = mysqli_fetch_assoc(mysqlQuery("select branch_status from branch_assign where link='package_booking/booking/index.php'"));
$branch_status = $sq['branch_status'];
?>
<?=  begin_panel('Package Booking',50) ?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >

<div class="row text-center mg_bt_20">
	<div class="col-md-12">
		<label for="rd_booking" class="app_dual_button active">
	        <input type="radio" id="rd_booking" name="rd_app" checked onchange="package_content_load()">
	        &nbsp;&nbsp;Booking
	    </label>
	    <label for="rd_payment" class="app_dual_button">
	        <input type="radio" id="rd_payment" name="rd_app" onchange="package_content_load()">
	        &nbsp;&nbsp;Receipt
	    </label>
		<label for="rd_visa" class="app_dual_button">
	        <input type="radio" id="rd_visa" name="rd_app" onchange="package_content_load()">
	        &nbsp;&nbsp;Visa Status
	    </label>
	</div>   
</div>

<div id="div_package_booking_content"></div>

<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>                    

<script>
function package_content_load()
{
	var id = $('input[name="rd_app"]:checked').attr('id');
    var branch_status = $('#branch_status').val();
	if(id=="rd_booking"){
		$.post('booking_home.php', {branch_status : branch_status}, function(data){
			$('#div_package_booking_content').html(data);
		});
	}
	if(id=="rd_payment"){
		$.post('../payments/index.php', {branch_status : branch_status}, function(data){
			$('#div_package_booking_content').html(data);
		});
	}
	if(id=="rd_visa"){
		$.post('../../visa_status/package_tour/index.php', {branch_status : branch_status}, function(data){
			$('#div_package_booking_content').html(data);
		});
    }
    
}
package_content_load();
</script>
<?php
/*======******Footer******=======*/
require_once('../../layouts/admin_footer.php'); 
?>