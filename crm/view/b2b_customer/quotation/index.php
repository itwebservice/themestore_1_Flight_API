<?php
include "../../../model/model.php";
/*=======******Header******=========*/
require_once('../../layouts/admin_header.php');
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select branch_status from branch_assign where link='b2b_customer/quotation/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >
<?= begin_panel('B2B Quotation','') ?>
<?php if($setup_package == '4'){ ?>
<!--=======Header panel end======-->

<div class="app_panel_content">
    <div class="row">

	<div class="app_panel_content Filter-panel">
		<div class="row">
			<div class="col-md-4 col-sm-4 col-xs-12 mg_bt_10_xs">
				<select name="customer_id" id="customer_id" title="Select Agent" onchange="quotation_list_reflect()" style="width:100%">
					<option value="">Select Agent</option>
					<?php 
					$query = "select * from customer_master where type='B2B'";
					if($branch_status=='yes' && ($role!='Admin'||$role=='Branch Admin')){
							$query .= " and branch_admin_id = '$branch_admin_id'";
					}
					$query .= " order by customer_id desc";
					$sq_customer = mysqlQuery($query);
					while($row_customer = mysqli_fetch_assoc($sq_customer)){
						?>
						<option value="<?= $row_customer['customer_id'] ?>"><?= $row_customer['company_name'] ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12 mg_bt_10_xs">
				<input type="text" id="from_date_filter" name="from_date_filter" placeholder="From Date" title="From Date" onchange="get_to_date(this.id,'to_date_filter');quotation_list_reflect()">
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12 mg_bt_10_xs">
				<input type="text" id="to_date_filter" name="to_date_filter" placeholder="To Date" title="To Date" onchange="validate_validDate('from_date_filter','to_date_filter');quotation_list_reflect()">
			</div>
		</div>
</div>

	<div id="div_quotation_list_reflect" class="main_block">
		<div class="row mg_tp_30"> <div class="col-md-12"> <div class="table-responsive">
			<table id="b2bquot_table" class="table table-hover" style="margin: 20px 0 !important;"></table>
		</div></div></div>
	</div>
</div>
</div>
<?= end_panel() ?>

<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<script>
$('#customer_id').select2();
$('#from_date_filter,#to_date_filter').datetimepicker({timepicker:false, format:'d-m-Y' });
var columns = [
	{ title : "S_No"},
	{ title : "Quotation_Date"},
	{ title : "Agent_name"},
	{ title : "Guest_Name"},
	{ title : "Amount"},
	{ title : "Actions" , className:"text-center action_width"}
]
function quotation_list_reflect(){
	$('#div_quotation_list_reflect').append('<div class="loader"></div>');
	var from_date = $('#from_date_filter').val();
	var to_date = $('#to_date_filter').val();
	var customer_id = $('#customer_id').val();

	$.post('quotation_list_reflect.php', { from_date : from_date, to_date : to_date, customer_id : customer_id }, function(data){
		pagination_load(data, columns, false, false, 20, 'b2bquot_table');
		$('.loader').remove();
	})
}
quotation_list_reflect();

function delete_quotation(quotation_id){
	var base_url = $('#base_url').val();
	$('#vi_confirm_box').vi_confirm_box({
        message: 'Are you sure you want to delete?',
        callback: function(data1){
            if(data1=="yes"){
				$.post(base_url+'controller/b2b_customer/delete_quotation.php', {quotation_id : quotation_id }, function(data){
					var msg = data.split('--');
					if(msg[0] === "error"){
						error_msg_alert(msg[1]);
						return false;
					}else{
						msg_alert(msg[0]);
						quotation_list_reflect();
					}
				});
			}
		}
	});
}
</script>
<?php
/*======******Footer******=======*/
require_once('../../layouts/admin_footer.php'); 

} else{ ?>
<div class="alert alert-danger" role="alert">
	Please upgrade the subscription to use this feature.
</div>
<?php }?>
  <style>
    .action_width {
      display: flex;
    }
  </style>