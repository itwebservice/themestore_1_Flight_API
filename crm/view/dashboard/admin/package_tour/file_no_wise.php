<?php 
include "../../../../model/model.php";
require_once('../../../../classes/tour_booked_seats.php');
require_once('../../../layouts/app_functions.php');

$booking_id = $_POST['booking_id'];

$sq_booking = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' and delete_status='0'"));

$sq_total_tour_paid_amount = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum from package_payment_master where booking_id='$booking_id' and payment_for='tour' "));
$sq_total_travel_paid_amount = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum from package_payment_master where booking_id='$booking_id' and payment_for='traveling' "));
?>
<div class="row">
	
<div class="col-md-6">

	<div class="panel panel-default panel-body mg_bt_0 mg_bt_10_sm_xs" style="height:270px;">

		<div class="row"> <div class="col-md-12"> <div class="table-responsive">

			<table class="table table-bordered table-hover mg_bt_0">
				<thead>
					<th>No</th>
					<th>Name</th>
					<th>Booking Date</th>
					<th>Mobile</th>
				</thead>
				<tbody>
				<?php
				$count = 0;
				$sq_traveler = mysqlQuery("select * from package_travelers_details where booking_id='$booking_id'");
				while($row_traveler = mysqli_fetch_assoc($sq_traveler)){
				?>
				<tr>
					<td><?= ++$count ?></td>
					<td><?= $row_traveler['m_honorific'].' '.$row_traveler['first_name'].' '.$row_traveler['last_name'] ?></td>
					<td><?= date('d-m-Y', strtotime($sq_booking['booking_date'])) ?></td>
					<td><?= $sq_booking['mobile_no'] ?></td>
				</tr>
				<?php
				}
				?>			
				</tbody>
			</table>

		</div> </div> </div>

	</div>
	
</div>

<div class="col-md-6">
	
	<div class="panel panel-default panel-body mg_bt_0" style="height:270px;">
	<div class="row">
		<div class="col-md-6 col-sm-6">
	        <?php     
	            begin_widget();
	                $title_arr = array("Travel Fee", "Paid");
	                $content_arr = array($sq_booking['total_travel_expense'], $sq_total_travel_paid_amount['sum']);
	                $percent = ($sq_total_travel_paid_amount['sum']/$sq_booking['total_travel_expense'])*100;
	                $percent = round($percent, 2);
	                $label = "Travel Fee Paid";
	                widget_element($title_arr, $content_arr, $percent, $label);
	            end_widget();
	        ?>
	    </div>
	    <div class="col-md-6 col-sm-6">
	        <?php     
	            begin_widget();
	                $title_arr = array("Tour Fee", "Tour Paid");
	                $content_arr = array($sq_booking['actual_tour_expense'], $sq_total_tour_paid_amount['sum']);
	                $percent = ($sq_total_tour_paid_amount['sum']/$sq_booking['actual_tour_expense'])*100;
	                $percent = round($percent, 2);
	                $label = "Tour Fee Paid";
	                widget_element($title_arr, $content_arr, $percent, $label);
	            end_widget();
	        ?>
	    </div> 
	</div>

	</div>

</div>

</div>

<script>
	$(".dash_file_no .panel").mCustomScrollbar();
</script>