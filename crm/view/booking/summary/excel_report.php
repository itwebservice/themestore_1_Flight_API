<?php
global $currency;
include "../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

//This function generates the background color
function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
        'rgb' => $color
        )
    ));
}

//This array sets the font atrributes
$header_style_Array = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Verdana'
    ));
$table_header_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    ));
$content_style_Array = array(
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    ));

//This is border array
$borderArray = array(
          'borders' => array(
              'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
              )
          )
      );

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                             ->setLastModifiedBy("Maarten Balliauw")
                             ->setTitle("Office 2007 XLSX Test Document")
                             ->setSubject("Office 2007 XLSX Test Document")
                             ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Test result file");


//////////////////////////****************Content start**************////////////////////////////////
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$branch_status = $_GET['branch_status'];
$customer_id = $_GET['customer_id'];
$booker_id = $_GET['booker_id'];
$branch_id = $_GET['branch_id'];
$id = $_GET['id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$cust_type = $_GET['cust_type'];
$company_name = (isset($_GET['company_name'])) ? $_GET['company_name'] : '';


if($customer_id!=""){
	$sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_customer_info['type'] == 'Corporate'||$sq_customer_info['type']=='B2B'){
		$cust_name = $sq_customer_info['company_name'];
	}else{
		$cust_name = $sq_customer_info['first_name'].' '.$sq_customer_info['last_name'];
	}
}
else{
	$cust_name = "";
}

if($id!=""){
    $row_booking = mysqli_fetch_assoc(mysqlQuery("select * from tourwise_traveler_details where id='$id' and delete_status='0'"));
	$booking_date = $row_booking['form_date'];
	$yr = explode("-", $booking_date);
	$year =$yr[0];
    $invoice_id = get_group_booking_id($id,$year);
} else{
    $invoice_id = ""; 
}

if($from_date!="" && $to_date!=""){
	$date_str = $from_date.' to '.$to_date;
}
else{
	$date_str = "";
}
if($company_name == 'undefined') { $company_name = ''; }


if($booker_id != '')
{
    $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$booker_id'"));
    if($sq_emp['first_name'] == '') { $emp_name='Admin';}
    else{ $emp_name = $sq_emp['first_name'].' '.$sq_emp['last_name']; }
}

if($branch_id != '') { 
    $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$branch_id'"));
    $branch_name = $sq_branch['branch_name']==''?'NA':$sq_branch['branch_name'];
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'Group Tour Summary')
            ->setCellValue('B3', 'Booking ID')
            ->setCellValue('C3', $invoice_id)
            ->setCellValue('B4', 'Customer')
            ->setCellValue('C4', $cust_name)
            ->setCellValue('B5', 'From-To Date')
            ->setCellValue('C5', $date_str)
            ->setCellValue('B6', 'Customer Type')
            ->setCellValue('C6', $cust_type)
            ->setCellValue('B7', 'Company Name')
            ->setCellValue('C7', $company_name)
            ->setCellValue('B8', 'Booked By')
            ->setCellValue('C8', $emp_name)
            ->setCellValue('B9', 'Branch')
            ->setCellValue('C9', $branch_name);

$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B4:C4')->applyFromArray($borderArray);    

$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B5:C5')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B6:C6')->applyFromArray($borderArray);

$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B7:C7')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B8:C8')->applyFromArray($borderArray); 

$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B9:C9')->applyFromArray($borderArray); 

        $query = "select * from tourwise_traveler_details where 1 and delete_status='0' ";
        if($customer_id!=""){
            $query .= " and customer_id='$customer_id'";
        }
        if($id!=""){
            $query .= " and id='$id'";
        }
        if($from_date!="" && $to_date!=""){
            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));
            $query .= " and form_date between '$from_date' and '$to_date'";
        }
        if($cust_type != ""){
            $query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
        }
        if($company_name != ""){
            $query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
        }
        if($booker_id!=""){
            $query .= " and emp_id='$booker_id'";
        }
        if($branch_id!=""){
            $query .= " and emp_id in(select emp_id from emp_master where branch_id = '$branch_id')";
        }
        if($branch_status=='yes'){
            if($role=='Branch Admin' || $role=='Accountant' || $role_id>'7'){
              $query .= " and branch_admin_id = '$branch_admin_id'";
            }
            elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
              $query .= " and emp_id='$emp_id' and branch_admin_id = '$branch_admin_id'";
            }
          }
          elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
            $query .= " and emp_id='$emp_id'";
          }
        $query .= " order by id desc";

        $row_count = 11;

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B'.$row_count, "Sr. No")
                ->setCellValue('C'.$row_count, "Booking ID")
                ->setCellValue('D'.$row_count, "Customer Name")
                ->setCellValue('E'.$row_count, "Mobile")
                ->setCellValue('F'.$row_count, "EMAIL ID")
                ->setCellValue('G'.$row_count, "Total Pax")
                ->setCellValue('H'.$row_count, "Booking Date")
                ->setCellValue('I'.$row_count, "Tour Name")
                ->setCellValue('J'.$row_count, "Tour Date")
                ->setCellValue('K'.$row_count, "Basic Amount")
                ->setCellValue('L'.$row_count, "Tax")
                ->setCellValue('M'.$row_count, "TCS")
                ->setCellValue('N'.$row_count, "Credit card Charges")
                ->setCellValue('O'.$row_count, "Sale")
                ->setCellValue('P'.$row_count, "Cancel")
                ->setCellValue('Q'.$row_count, "Total")
                ->setCellValue('R'.$row_count, "Paid")
                ->setCellValue('S'.$row_count, "Outstanding Balance")
                ->setCellValue('T'.$row_count, "Due Date")
                ->setCellValue('U'.$row_count, "Purchase")
                ->setCellValue('V'.$row_count, "Purchased From")
                ->setCellValue('W'.$row_count, "Branch")
                ->setCellValue('X'.$row_count, "Booked By")
                ->setCellValue('Y'.$row_count, "Incentive");


        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Y'.$row_count)->applyFromArray($header_style_Array);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Y'.$row_count)->applyFromArray($borderArray);    

        $row_count++;

        $count = 0;
        $total_balance=0;
        $total_refund=0;        
        $cancel_total =0;
        $sale_total = 0;
        $paid_total = 0;
        $balance_total = 0;
        $vendor_name1 = '';

        $sq_package = mysqlQuery($query);
        while($row_booking = mysqli_fetch_assoc($sq_package))
        {
            
            $pass_count = mysqli_num_rows(mysqlQuery("select * from  travelers_details where traveler_group_id='$row_booking[traveler_group_id]'"));
            $cancelpass_count = mysqli_num_rows(mysqlQuery("select * from  travelers_details where traveler_group_id='$row_booking[traveler_group_id]' and status='Cancel'"));    
            $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$row_booking[emp_id]'"));
            if($sq_emp['first_name'] == '') { $emp_name='Admin';}
            else{ $emp_name = $sq_emp['first_name'].' '.$sq_emp['last_name']; }

            $sq_branch = mysqli_fetch_assoc(mysqlQuery("select * from branches where branch_id='$sq_emp[branch_id]'"));
            $branch_name = $sq_branch['branch_name']==''?'NA':$sq_branch['branch_name'];
            
            $sq_total_member = mysqli_num_rows(mysqlQuery("select traveler_group_id from travelers_details where traveler_group_id = '$row_booking[traveler_group_id]'"));
            $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
            $contact_no = $encrypt_decrypt->fnDecrypt($sq_customer_info['contact_no'], $secret_key);
            $email_id = $encrypt_decrypt->fnDecrypt($sq_customer_info['email_id'], $secret_key);
            if($sq_customer_info['type'] == 'Corporate'||$sq_customer_info['type'] == 'B2B'){
                $customer_name = $sq_customer_info['company_name'];
            }else{
                $customer_name = $sq_customer_info['first_name'].' '.$sq_customer_info['last_name'];
            }

            $sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("select sum(amount) as sum,sum(credit_charges) as sumc from payment_master where tourwise_traveler_id='$row_booking[id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
            $paid_amount = $sq_paid_amount['sum'];
            $credit_card_charges = $sq_paid_amount['sumc'];

            $sq_est_count = mysqli_num_rows(mysqlQuery("select * from refund_tour_estimate where tourwise_traveler_id='$row_booking[id]'"));
            if($sq_est_count!='0'){
                $sq_tour_refund = mysqli_fetch_assoc(mysqlQuery("select * from refund_tour_estimate where tourwise_traveler_id='$row_booking[id]'"));
                $cancel_amount = $sq_tour_refund['cancel_amount'];
            }
            else{
                $sq_tour_refund = mysqli_fetch_assoc(mysqlQuery("select * from refund_traveler_estimate where tourwise_traveler_id='$row_booking[id]'"));
                $cancel_amount = $sq_tour_refund['cancel_amount'];
            }
            //sale amount
	        $tour_fee = $row_booking['net_total'];
            $cancel_amount = ($cancel_amount == '')?'0':$cancel_amount;
            $total_amount = $tour_fee - $cancel_amount;
        
            if($row_booking['tour_group_status'] == 'Cancel'){
                if($cancel_amount > $paid_amount){
                    $total_balance = $cancel_amount - $paid_amount;
                }
                else{
                    $total_balance = 0;
                }
            }else{
                if($pass_count==$cancelpass_count){
                    if($cancel_amount > $paid_amount){
                        $total_balance = $cancel_amount - $paid_amount;
                    }
                    else{
                        $total_balance = 0;
                    }
                }
                else{
                    $total_balance = $total_amount - $paid_amount;
                }
            }
            //Footer
            $cancel_total = $cancel_total + $cancel_amount;
            $sale_total = $sale_total + $total_amount;
            $paid_total = $paid_total + $sq_paid_amount['sum'];
            $balance_total = $balance_total + $total_balance;

            /////// Purchase ////////
            $total_purchase = 0;
            $purchase_amt = 0;
            $i=0;
            $sq_purchase_count = mysqli_num_rows(mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Group Tour' and estimate_type_id='$row_booking[tour_group_id]' and delete_status='0'"));
            if($sq_purchase_count == 0){  $p_due_date = 'NA'; }
            $sq_purchase = mysqlQuery("select * from vendor_estimate where status!='Cancel' and estimate_type='Group Tour' and estimate_type_id='$row_booking[tour_group_id]' and delete_status='0'");
            while($row_purchase = mysqli_fetch_assoc($sq_purchase)){	
                if($row_purchase['purchase_return'] == 0){
                    $total_purchase += $row_purchase['net_total'];
                }
                else if($row_purchase['purchase_return'] == 2){
                    $cancel_estimate = json_decode($row_purchase['cancel_estimate']);
                    $p_purchase = ($row_purchase['net_total'] - floatval($cancel_estimate[0]->net_total));
                    $total_purchase += $p_purchase;
                }
                $vendor_name = get_vendor_name_report($row_purchase['vendor_type'], $row_purchase['vendor_type_id']);
                if($vendor_name != ''){ $vendor_name1 .= $vendor_name.','; }
            }
            $vendor_name1 = substr($vendor_name1, 0, -1);

            ///Tour
            $sq_tour = mysqli_fetch_assoc(mysqlQuery("select * from tour_master where tour_id='$row_booking[tour_id]'"));

            $sq_group = mysqli_fetch_assoc(mysqlQuery("select * from tour_groups where group_id='$row_booking[tour_group_id]'"));
            $tour = $sq_tour['tour_name'];

            $group = get_date_user($sq_group['from_date']).' To '.get_date_user($sq_group['to_date']);   
            //Service Tax and Markup Tax
            $service_tax_amount = 0;
            if($row_booking['service_tax'] !== 0.00 && ($row_booking['service_tax']) !== ''){
                $service_tax_subtotal1 = explode(',',$row_booking['service_tax']);
                for($i=0;$i<sizeof($service_tax_subtotal1);$i++){
                $service_tax = explode(':',$service_tax_subtotal1[$i]);
                $service_tax_amount +=  $service_tax[2];
                }
            }
            $booking_date = $row_booking['form_date'];
            $yr = explode("-", $booking_date);
            $year =$yr[0];
            // currency conversion
            $currency_amount1 = currency_conversion($currency,$row_booking['currency_code'],$total_amount);
            if($row_booking['currency_code'] !='0' && $currency != $row_booking['currency_code']){
                $currency_amount = ' ('.$currency_amount1.')';
            }else{
                $currency_amount = '';
            }
    	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$row_count, ++$count)
            ->setCellValue('C'.$row_count, get_group_booking_id($row_booking['id'],$year))
            ->setCellValue('D'.$row_count, $customer_name)
            ->setCellValue('E'.$row_count, $contact_no)
            ->setCellValue('F'.$row_count, $email_id)
            ->setCellValue('G'.$row_count, $sq_total_member)
            ->setCellValue('H'.$row_count, get_date_user($row_booking['form_date']))
            ->setCellValue('I'.$row_count, $tour)
            ->setCellValue('J'.$row_count, $group)
            ->setCellValue('K'.$row_count, number_format($row_booking['basic_amount'],2))
            ->setCellValue('L'.$row_count, number_format($service_tax_amount,2))
            ->setCellValue('M'.$row_count, number_format($row_booking['tcs_tax'],2))
            ->setCellValue('N'.$row_count, number_format($credit_card_charges,2))
            ->setCellValue('O'.$row_count, number_format($tour_fee,2))
            ->setCellValue('P'.$row_count, number_format($cancel_amount,2))
            ->setCellValue('Q'.$row_count, number_format($total_amount,2).$currency_amount)
            ->setCellValue('R'.$row_count, number_format($sq_paid_amount['sum'],2))
            ->setCellValue('S'.$row_count, number_format($total_balance,2))
            ->setCellValue('T'.$row_count, get_date_user($row_booking['balance_due_date']))
            ->setCellValue('U'.$row_count, number_format($total_purchase,2))
            ->setCellValue('V'.$row_count, $vendor_name1)
            ->setCellValue('W'.$row_count, $branch_name)
            ->setCellValue('X'.$row_count, $emp_name)
            ->setCellValue('Y'.$row_count, number_format(0,2));

        $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Y'.$row_count)->applyFromArray($content_style_Array);
    	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':Y'.$row_count)->applyFromArray($borderArray);    

		$row_count++;

        $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "")
        ->setCellValue('C'.$row_count, "")
        ->setCellValue('D'.$row_count, "")
        ->setCellValue('E'.$row_count, "")
        ->setCellValue('F'.$row_count, "")
        ->setCellValue('G'.$row_count, "")
        ->setCellValue('H'.$row_count, "")
        ->setCellValue('J'.$row_count, "")
        ->setCellValue('K'.$row_count, "")
        ->setCellValue('M'.$row_count, "")
        ->setCellValue('L'.$row_count, "")
        ->setCellValue('N'.$row_count, "")
        ->setCellValue('O'.$row_count, 'TOTAL CANCEL :'.number_format($cancel_total,2))
        ->setCellValue('P'.$row_count, 'TOTAL SALE :'.number_format($sale_total,2))
        ->setCellValue('Q'.$row_count, 'TOTAL PAID :'.number_format($paid_total,2))
        ->setCellValue('R'.$row_count, 'TOTAL BALANCE :'.number_format($balance_total,2));

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':R'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':R'.$row_count)->applyFromArray($borderArray);

}
	

//////////////////////////****************Content End**************////////////////////////////////
	

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


for($col = 'A'; $col !== 'N'; $col++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
}


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="GroupTourSummary('.date('d-m-Y H:i').').xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
