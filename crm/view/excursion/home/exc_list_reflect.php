<?php
include "../../../model/model.php";

$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
$exc_id = $_POST['exc_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : '';
$company_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$financial_year_id = $_POST['financial_year_id'];
$branch_status = isset($_POST['branch_status']) ? $_POST['branch_status'] : '';
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];

$query = "select * from excursion_master where financial_year_id='$financial_year_id' and delete_status='0' ";
if($customer_id!=""){
	$query .= " and customer_id='$customer_id'";
}
if($exc_id!=""){
	$query .= " and exc_id='$exc_id'";
}
if($from_date!="" && $to_date!=""){
	$from_date = date('Y-m-d', strtotime($from_date));
	$to_date = date('Y-m-d', strtotime($to_date));
	$query .= " and created_at between '$from_date' and '$to_date'";
}
if($cust_type != ""){
	$query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
if($company_name != ""){
	$query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
include "../../../model/app_settings/branchwise_filteration.php";
$query .= " order by exc_id desc";

$count = 0;
$booking_amount = 0;
$cancelled_amount = 0;
$total_amount = 0;
$array_s = array();
$temp_arr = array();
$footer_data = array();
$sq_exc = mysqlQuery($query);		

while($row_exc = mysqli_fetch_assoc($sq_exc)){

	$sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_exc[emp_id]'"));
	$emp_name = ($row_exc['emp_id'] != 0) ? $sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';

	$pass_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$row_exc[exc_id]'"));
	$cancel_count = mysqli_num_rows(mysqlQuery("select * from excursion_master_entries where exc_id='$row_exc[exc_id]' and status='Cancel'"));
	if($pass_count==$cancel_count){
		$bg="danger";
		$update_btn = '';
		$delete_btn = '';
		$voucher_btn = '';
	}
	else {
		$bg="";
		$update_btn = '<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="exc_update_modal('. $row_exc['exc_id'] .')" title="Update Details" id="update_btn-'. $row_exc['exc_id'] .'"><i class="fa fa-pencil-square-o"></i></button>';
		$delete_btn = '<button class="'.$delete_flag.' btn btn-danger btn-sm" onclick="delete_entry('.$row_exc['exc_id'].')" title="Delete Entry"><i class="fa fa-trash"></i></button>';
		$voucher_btn = '<button data-toggle="tooltip" title="Download Service Voucher" onclick="voucher_display('.$row_exc['exc_id'].')" class="btn btn-info btn-sm" title="Download Invoice"><i class="fa fa-print"></i></button>';
	}
	$date = $row_exc['created_at'];
	$yr = explode("-", $date);
	$year =$yr[0];

	$customer_info_name = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id = '$row_exc[customer_id]'"));
	$contact_no = $encrypt_decrypt->fnDecrypt($customer_info_name['contact_no'], $secret_key);
	$email_id = $encrypt_decrypt->fnDecrypt($customer_info_name['email_id'], $secret_key); 
	if($customer_info_name['type']=='Corporate'||$customer_info_name['type'] == 'B2B'){
		$customer_name = $customer_info_name['company_name'];
	}else{
		$customer_name = $customer_info_name['first_name'].' '.$customer_info_name['last_name'];
	}
	//Get Total activity cost
	$exc_total_amount=$row_exc['exc_total_cost'];

	//Get total refund amount
	$cancel_amount=$row_exc['cancel_amount'];
	if($cancel_amount==""){	$cancel_amount=0; }

	$total_paid = 0;
	$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(`credit_charges`) as sumc from exc_payment_master where exc_id='$row_exc[exc_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
	$credit_card_charges = $sq_paid_amount['sumc'];

	$exc_total_amount = $exc_total_amount + $credit_card_charges;

	$total_paid =  $sq_paid_amount['sum'];       
	$total_exc_amount=$exc_total_amount-$cancel_amount;

	$total_paid = ($total_paid == '') ? '0' : $total_paid;
	//calculate total amounts
	$booking_amount=$booking_amount+$exc_total_amount;
	$cancelled_amount=$cancelled_amount+$cancel_amount;
	$total_amount=$total_amount+$total_exc_amount;

	$invoice_no = get_exc_booking_id($row_exc['exc_id'],$year);
	$booking_id = $row_exc['exc_id'];
	$invoice_date = date('d-m-Y',strtotime($row_exc['created_at']));
	$customer_id = $row_exc['customer_id'];
	$service_name = "Activity Invoice";
	//**Service Tax
	$service_charge = $row_exc['service_charge'];
	$service_tax = $row_exc['service_tax_subtotal'];
	//**Basic Cost
	$basic_cost = $row_exc['exc_issue_amount'];
	$net_amount = $row_exc['exc_total_cost'];
	if($bg != ''){
		$balance_amount = ($total_paid > $cancel_amount) ? 0 : floatval($cancel_amount) - floatval($total_paid) ;
	}else{
	
		$balance_amount = floatval($net_amount) - floatval($total_paid);
	}
	$sq_sac = mysqli_fetch_assoc(mysqlQuery("select * from sac_master where service_name='Excursion'"));   
	$sac_code = $sq_sac['hsn_sac_code'];
	if($app_invoice_format == 4)
	$url1 = BASE_URL."model/app_settings/print_html/invoice_html/body/tax_invoice_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=&service_tax_per=&net_amount=$net_amount&service_charge=$service_charge&total_paid=$total_paid&balance_amount=$balance_amount&sac_code=$sac_code&branch_status=$branch_status&booking_id=$booking_id&pass_count=$pass_count&credit_card_charges=$credit_card_charges";
	else
	$url1 = BASE_URL."model/app_settings/print_html/invoice_html/body/excursion_body_html.php?invoice_no=$invoice_no&invoice_date=$invoice_date&customer_id=$customer_id&service_name=$service_name&basic_cost=$basic_cost&taxation_type=&service_tax_per=&net_amount=$net_amount&service_charge=$service_charge&total_paid=$total_paid&balance_amount=$balance_amount&sac_code=$sac_code&branch_status=$branch_status&booking_id=$booking_id&credit_card_charges=$credit_card_charges&currency_code=$row_exc[currency_code]&canc_amount=$cancel_amount&bg=$bg";

	//Currency conversion
	$currency_amount1 = currency_conversion($currency,$row_exc['currency_code'],$total_exc_amount);
	if($row_exc['currency_code'] !='0' && $currency != $row_exc['currency_code']){
		$currency_amount = ' ('.$currency_amount1.')';
	}else{
		$currency_amount = '';
	}

	$temp_arr = array( "data" => array(
		$row_exc['invoice_pr_id'],
		get_exc_booking_id($row_exc['exc_id'],$year),
		$customer_name,
		$contact_no,
		number_format($exc_total_amount, 2),
		$cancel_amount,
		number_format($total_exc_amount, 2).'<br/>'.$currency_amount,
		$emp_name,
		$invoice_date,
		'<a onclick="loadOtherPage(\''.$url1 .'\')" class="btn btn-info btn-sm" title="Download Invoice"><i class="fa fa-print"></i></a>
		'.$voucher_btn.$update_btn.'
		<button data-toggle="tooltip" class="btn btn-info btn-sm" onclick="exc_display_modal('. $row_exc['exc_id'] .')" title="View Details" id="view_btn-'. $row_exc['exc_id'] .'"><i class="fa fa-eye" aria-hidden="true"></i></button>'.$delete_btn
		), "bg" =>$bg );
		array_push($array_s,$temp_arr); 
}
$footer_data = array("footer_data" => array(
	'total_footers' => 4,
	'foot0' => "Total",
	'col0' => 4,
	'class0' => "text-right",
	'foot1' => number_format($booking_amount, 2),
	'col1' => 1,
	'class1' => "info",
	'foot2' =>  number_format($cancelled_amount, 2),
	'col2' => 1,
	'class2' => "danger",
	'foot3' => number_format($total_amount, 2),
	'col3' => 1,
	'class3' => "success",
	)
);
array_push($array_s, $footer_data);	
echo json_encode($array_s);
?>	