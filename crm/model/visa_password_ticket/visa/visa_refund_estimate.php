<?php 
$flag = true;
class visa_refund_estimate{

public function refund_estimate_update()
{
  $row_spec="sales";
  $visa_id = $_POST['visa_id'];
  $cancel_amount = $_POST['cancel_amount'];
  $total_refund_amount = $_POST['total_refund_amount'];
  $tax_value = $_POST['tax_value'];
  $tour_service_tax_subtotal = $_POST['tour_service_tax_subtotal'];
  $cancel_amount_exc = $_POST['cancel_amount_exc'];

  begin_t();

  $sq_refund = mysqlQuery("update visa_master set cancel_amount='$cancel_amount', total_refund_amount='$total_refund_amount',cancel_flag='1',`tax_value`='$tax_value', `tax_amount`='$tour_service_tax_subtotal', `cancel_amount_exc`='$cancel_amount_exc' where visa_id='$visa_id'");
  if($sq_refund)
  {
  	//Finance save
    $this->finance_save($visa_id,$row_spec);

  	if($GLOBALS['flag']){
  		commit_t();
  		echo "Refund estimate has been successfully saved.";
  		exit;
  	}
  	else{
  		rollback_t();
  		exit;
  	}
  }
  else{
  	rollback_t();
  	echo "Cancellation not saved!";
  	exit;
  }


}

public function finance_save($visa_id,$row_spec)
{
	$visa_id = $_POST['visa_id'];
	$cancel_amount = $_POST['cancel_amount'];
  $ledger_posting = $_POST['ledger_posting'];
  $cancel_amount_exc = $_POST['cancel_amount_exc'];
  $tour_service_tax_subtotal_cancel = $_POST['tour_service_tax_subtotal'];

	$created_at = date("Y-m-d");
	$year1 = explode("-", $created_at);
	$yr1 =$year1[0];

	$sq_sq_visa_info = mysqli_fetch_assoc(mysqlQuery("select * from visa_master where visa_id='$visa_id'"));
  $customer_id = $sq_sq_visa_info['customer_id'];
  $service_tax_subtotal = $sq_sq_visa_info['service_tax_subtotal'];
  $markup = $sq_sq_visa_info['markup'];
  $service_tax_markup = $sq_sq_visa_info['markup_tax'];
  $roundoff = $sq_sq_visa_info['roundoff'];
  $reflections = json_decode($sq_sq_visa_info['reflections']);

  //Getting customer Ledger
  $sq_cust = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$customer_id' and user_type='customer'"));
  $cust_gl = $sq_cust['ledger_id'];

  $visa_sale_amount = $sq_sq_visa_info['visa_issue_amount'];
  global $transaction_master;
  
    //////////Sales/////////////

    $module_name = "Visa Booking";
    $module_entry_id = $visa_id;
    $transaction_id = "";
    $payment_amount = $visa_sale_amount;
    $payment_date = $created_at;
    $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
    $ledger_particular = '';
    $gl_id = 141;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');

    /////////Service Charge////////
    $module_name = "Visa Booking";
    $module_entry_id = $visa_id;
    $transaction_id = "";
    $payment_amount = $sq_sq_visa_info['service_charge'];
    $payment_date = $created_at;
    $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
    $ledger_particular = '';
    $gl_id = ($reflections[0]->hotel_sc != '') ? $reflections[0]->hotel_sc : 188;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
    
    /////////Service Charge Tax Amount////////
    $service_tax_subtotal = explode(',',$service_tax_subtotal);
    $tax_ledgers = explode(',',$reflections[0]->hotel_taxes);
    for($i=0;$i<sizeof($service_tax_subtotal);$i++){

      $service_tax = explode(':',$service_tax_subtotal[$i]);
      $tax_amount = $service_tax[2];
      $ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

      $module_name = "Visa Booking";
      $module_entry_id = $visa_id;
      $transaction_id = "";
      $payment_amount = $tax_amount;
      $payment_date = $created_at;
      $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1),$customer_id);
      $ledger_particular = '';
      $gl_id = $ledger;
      $payment_side = "Debit";
      $clearance_status = "";
      $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
    }

    ///////////Markup//////////
    $module_name = "Visa Booking";
    $module_entry_id = $visa_id;
    $transaction_id = "";
    $payment_amount = $markup;
    $payment_date = $created_at;
    $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
    $ledger_particular = '';
    $gl_id = ($reflections[0]->hotel_markup != '') ? $reflections[0]->hotel_markup : 200;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');

    /////////Markup Tax Amount////////
    $service_tax_markup = explode(',',$service_tax_markup);
    $tax_ledgers = explode(',',$reflections[0]->hotel_markup_taxes);
    for($i=0;$i<sizeof($service_tax_markup);$i++){

      $service_tax = explode(':',$service_tax_markup[$i]);
      $tax_amount = $service_tax[2];
      $ledger = isset($tax_ledgers[$i]) ? $tax_ledgers[$i] : '';

      $module_name = "Visa Booking";
      $module_entry_id = $visa_id;
      $transaction_id = "";
      $payment_amount = $tax_amount;
      $payment_date = $created_at;
      $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
      $ledger_particular = '';
      $gl_id = $ledger;
      $payment_side = "Debit";
      $clearance_status = "";
      $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'1', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
	}

  ////////Customer Sale Amount//////
  $module_name = "Visa Booking";
  $module_entry_id = $visa_id;
  $transaction_id = "";
  $payment_amount =$roundoff;
  $payment_date = $created_at;
  $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
  $ledger_particular = '';
  $gl_id = 230;
  $payment_side = "Debit";
  $clearance_status = "";
  $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');    

    ////////Customer Sale Amount//////
    $module_name = "Visa Booking";
    $module_entry_id = $visa_id;
    $transaction_id = "";
    $payment_amount = $sq_sq_visa_info['visa_total_cost'];
    $payment_date = $created_at;
    $payment_particular =  get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id, '',$payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');    

    /////////Service Charge Tax Amount////////
    $service_tax_subtotal = explode(',',$tour_service_tax_subtotal_cancel);
    $tax_ledgers = explode(',',$ledger_posting);
    for($i=0;$i<sizeof($service_tax_subtotal);$i++){

      $service_tax = explode(':',$service_tax_subtotal[$i]);
      $tax_amount = $service_tax[2];
      $ledger = $tax_ledgers[$i];

      $module_name = "Visa Booking";
      $module_entry_id = $visa_id;
      $transaction_id = "";
      $payment_amount = $tax_amount;
      $payment_date = $created_at;
      $payment_particular = get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1),$customer_id);
      $ledger_particular = '';
      $gl_id = $ledger;
      $payment_side = "Credit";
      $clearance_status = "";
      $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');
    }

    ////////Cancel Amount//////
    $module_name = "Visa Booking";
    $module_entry_id = $visa_id;
    $transaction_id = "";
    $payment_amount = $cancel_amount_exc;
    $payment_date = $created_at;
    $payment_particular =  get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
    $ledger_particular = '';
    $gl_id = 161;
    $payment_side = "Credit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND');    

    ////////Customer Cancel Amount//////
    $module_name = "Visa Booking";
    $module_entry_id = $visa_id;
    $transaction_id = "";
    $payment_amount = $cancel_amount;
    $payment_date = $created_at;
    $payment_particular =  get_cancel_sales_particular(get_visa_booking_id($visa_id,$yr1), $customer_id);
    $ledger_particular = '';
    $gl_id = $cust_gl;
    $payment_side = "Debit";
    $clearance_status = "";
    $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,'',$ledger_particular,'REFUND'); 

}


}

?>