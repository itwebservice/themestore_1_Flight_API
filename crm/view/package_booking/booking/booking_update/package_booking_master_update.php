<?php
include "../../../../model/model.php"; 
include_once('../../../layouts/fullwidth_app_header.php'); 
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id']; 
$emp_id=$_SESSION['emp_id']; 
$branch_status = $_POST['branch_status'];
$sq=mysqlQuery("select emp_id,first_name,last_name from emp_master where emp_id='$emp_id'");
if($row=mysqli_fetch_assoc($sq)){
    $first_name=$row['first_name'];
    $last_name=$row['last_name'];
}
$booker_name = $first_name." ".$last_name; 

$booking_id = $_POST['booking_id'];
$sq_booking_info = mysqli_fetch_assoc(mysqlQuery("select * from package_tour_booking_master where booking_id='$booking_id' and delete_status='0'"));
$quot_id =$sq_booking_info['quotation_id'];
$reflections = json_decode($sq_booking_info['reflections']);

$sq_tcs = mysqli_fetch_assoc(mysqlQuery("select * from tcs_master where entry_id='2'"));
$tcs_readonly = ($sq_booking_info['tour_type']=='Domestic' || $sq_tcs['calc'] == '0') ? 'readonly' : '';

global $package_flight_switch,$package_cruise_switch,$package_train_switch;
$hide_flight = ($package_flight_switch == 'Yes') ? 'hidden' : '';
$hide_cruise = ($package_cruise_switch == 'Yes') ? 'hidden' : '';
$hide_train = ($package_train_switch == 'Yes') ? 'hidden' : '';
$all_tab = ($hide_flight=='hidden' && $hide_cruise=='hidden' && $hide_train=='hidden') ? 'hidden' : '';
?>
<input type="hidden" id="emp_id" name="emp_id" value="<?php echo $emp_id; ?>">
<input type="hidden" id="quotation_id1" name="quotation_id1" value="<?php echo $quot_id ?>">
<input type="hidden" id="booking_id" name="booking_id" value="<?php echo $booking_id; ?>">
<input type="hidden" id="hotel_sc" name="hotel_sc" value="<?php echo $reflections[0]->hotel_sc ?>">
<input type="hidden" id="hotel_markup" name="hotel_markup" value="<?php echo $reflections[0]->hotel_markup ?>">
<input type="hidden" id="hotel_taxes" name="hotel_taxes" value="<?php echo $reflections[0]->hotel_taxes ?>">
<input type="hidden" id="hotel_markup_taxes" name="hotel_markup_taxes" value="<?php echo $reflections[0]->hotel_markup_taxes ?>">
<input type="hidden" id="hotel_tds" name="hotel_tds" value="<?php echo $reflections[0]->hotel_tds ?>">
<input type="hidden" id="tcs" name="tcs" value="<?= $sq_tcs['tax_amount'] ?>">
<input type="hidden" id="tcs_apply" name="tcs_apply" value="<?= $sq_tcs['apply'] ?>">
<input type="hidden" id="tcs_calc" name="tcs_calc" value="<?= $sq_tcs['calc'] ?>">
<div class="bk_tab_head bg_light">
    <ul> 
        <li>
            <a href="javascript:void(0)" id="tab_1_head" class="active">
                <span class="num" title="Tour Details">1<i class="fa fa-check"></i></span><br>
                <span class="text">Tour Details</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab_2_head">
                <span class="num" title="Travelling">2<i class="fa fa-check"></i></span><br>
                <span class="text">Travelling</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab_3_head">
                <span class="num" title="Hotel & Transport">3<i class="fa fa-check"></i></span><br>
                <span class="text">Hotel & Transport</span>
            </a>
        </li>
        <li>
            <a href="javascript:void(0)" id="tab_4_head">
                <span class="num" title="Costing">4<i class="fa fa-check"></i></span><br>
                <span class="text">Costing</span>
            </a>
        </li>
    </ul>
</div>

<div class="bk_tabs">
    <div id="tab_1" class="bk_tab active">
        <?php include_once("tab_1/package_booking_master_update_tab1.php"); ?>  
    </div>
    <div id="tab_2" class="bk_tab">
        <?php include_once("tab_2/package_booking_master_update_tab2.php"); ?>
    </div>
    <div id="tab_3" class="bk_tab">
        <?php include_once("tab_3/package_booking_master_update_tab3.php"); ?>   
    </div>
    <div id="tab_4" class="bk_tab">
        <?php include_once("tab_4/package_booking_master_update_tab4.php"); ?>   
    </div>
</div>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>
<script src='../js/calculations.js'></script>
<script src='../js/business_rule_calculation.js'></script>
<?php
include_once('../../../layouts/fullwidth_app_footer.php');
?>