<?php  
include "../../../model/model.php";
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_POST['branch_status'];
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >
  <div class="row mg_bt_20">
    <div class="col-sm-12 text-right text_left_sm_xs">
    <button class="btn btn-info btn-sm ico_left" id="btn_vsave_modal" onclick="save_modal()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Status</button>
    </div>
  </div>
<div class="app_panel_content Filter-panel">
     <div class="row"> 
        <div class="col-sm-4 col-sm-offset-4">
          <select name="package_id_filter1" id="package_id_filter1" style="width:100%" title="Booking ID" onchange="load_visa_report(this.id,'package_tour','package_status_div')">
            <option value="">Booking ID</option>
            <?php 
            
            $query = "select * from package_tour_booking_master where 1 and delete_status='0' ";
            
            if($branch_status=='yes' && $role!='Admin'){
                $query .=" and branch_admin_id = '$branch_admin_id'";
              } 
            $query .= " order by booking_id desc";
            $sq_booking = mysqlQuery($query);
            while($row_booking = mysqli_fetch_assoc($sq_booking)){

              $date = $row_booking['booking_date'];
              $yr = explode("-", $date);
              $year =$yr[0];

              $pass_count= mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]'"));
	$cancle_count= mysqli_num_rows(mysqlQuery("select * from package_travelers_details where booking_id='$row_booking[booking_id]' and status='Cancel'"));
              $statusBooking = $pass_count == $cancle_count ? "(cancelled)" : "";
          $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
          if($sq_customer['type'] == 'Corporate'||$sq_customer['type'] == 'B2B'){
           ?>
           <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['company_name'].$statusBooking; ?></option>
           <?php }
           else{ ?> 
           <option value="<?php echo $row_booking['booking_id'] ?>"><?php echo get_package_booking_id($row_booking['booking_id'],$year)."-"." ".$sq_customer['first_name']." ".$sq_customer['last_name'].$statusBooking; ?></option>
           <?php    
         }
             
            }
            ?>
          </select>
        </div>
    </div>
</div>

<div id="save_div"></div>
  <div id="package_status_div" class="main_block"></div>

<script>
$('#package_id_filter1').select2();
function save_modal()
{
    $('#btn_vsave_modal').prop('disabled',true);
    $('#btn_vsave_modal').button('loading');
    var branch_status = $('#branch_status').val();
    $.post( '../../visa_status/package_tour/save_modal.php' , {branch_status : branch_status} , function ( data ) {
        $("#save_div").html(data);
        $('#btn_vsave_modal').prop('disabled',false);
        $('#btn_vsave_modal').button('reset');
    });
}

  function load_passenger(booking_id)
  {
    var booking_id = $('#'+booking_id).val();
    $.post( base_url()+"view/visa_status/inc/load_package_passenger.php" , {booking_id : booking_id} , function ( data ) {
          $("#cmb_traveler_id1").html(data);
     });
  }

  function load_visa_status(traveler_id,offset)
  {
     var booking_type = $('#booking_type').val();
     var traveler_id = $('#'+traveler_id).val();
     $.post( base_url()+"view/visa_status/visa_tracking_report.php" , {booking_type : booking_type, traveler_id : traveler_id } , function ( data ) {
          $ ("#doc_status"+offset).html(data) ;
     });
  }
  function load_visa_report(booking_id,booking_type,result_div)
{
    var booking_id = $('#'+booking_id).val();

    $.post( base_url()+"view/visa_status/inc/get_visa_status_report.php" , {booking_id : booking_id, booking_type : booking_type } , function ( data ) {
        $ ("#"+result_div).html(data) ;
    });
}
</script>
