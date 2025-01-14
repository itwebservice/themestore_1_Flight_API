<?php 
include "../../../../../../model/model.php";

$customer_id = $_SESSION['customer_id'];
?>
    <!-- Filter-panel -->

    <div class="app_panel_content Filter-panel">
		<div class="row">
			<div class="col-md-3">
				<select name="exc_id_filter" id="exc_id_filter" style="width:100%" onchange="exc_customer_list_reflect()">
			        <option value="">Select Booking</option>
			        <?php 
			        $sq_exc = mysqlQuery("select * from excursion_master where customer_id='$customer_id' and delete_status='0'");
			        while($row_exc = mysqli_fetch_assoc($sq_exc)){
						$date = $row_exc['created_at'];
						$yr = explode("-", $date);
						$year =$yr[0];
			          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_exc[customer_id]'"));
			          ?>
			          <option value="<?= $row_exc['exc_id'] ?>"><?= get_exc_booking_id($row_exc['exc_id'],$year).' : '.$sq_customer['first_name'].' '.$sq_customer['last_name'] ?></option>
			          <?php
			        }
			        ?>
			    </select>
			</div>
		</div>
	</div>

<div id="div_exc_customer_list_reflect" class="main_block"></div>
<div id="div_exc_content_display" class="main_block"></div>
<script>
$('#exc_id_filter').select2();
	function exc_customer_list_reflect()
	{
		var exc_id = $('#exc_id_filter').val()
		$.post('bookings/excursion/home/exc_list_reflect.php', { exc_id : exc_id }, function(data){
			$('#div_exc_customer_list_reflect').html(data);
		});
	}
	exc_customer_list_reflect();
	
	function exc_display_modal(exc_id)
	{	
		$('#exc-'+exc_id).button('loading');
		$.post('bookings/excursion/home/view/index.php', { exc_id : exc_id }, function(data){
			$('#div_exc_content_display').html(data);
			$('#exc-'+exc_id).button('reset');
		});
	}
function voucher_display(booking_id) {
    var base_url = $('#base_url').val();

    var booking_type = 'excursion';
    var url1 = base_url + 'model/app_settings/print_html/voucher_html/excursion_voucher.php?booking_id=' + booking_id +
        '&booking_type=' + booking_type;
    loadOtherPage(url1);
}
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>