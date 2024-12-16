<?php
include "../../../../model/model.php";

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../../../../classes/PHPExcel-1.8/Classes/PHPExcel.php';

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
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$branch_status = $_GET['branch_status'];
$customer_id = $_GET['customer_id'];
$train_ticket_id = $_GET['train_ticket_id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$cust_type = $_GET['cust_type'];
$company_name = (isset($_GET['company_name'])) ? $_GET['company_name'] : '';

$sql_booking_date = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master where train_ticket_id = '$train_ticket_id' and delete_status='0'")) ;
$booking_date = $sql_booking_date['created_at'];
$yr = explode("-", $booking_date);
$year =$yr[0];

if($customer_id!=""){
    $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
    if($sq_customer_info['type']=='Corporate'||$sq_customer_info['type'] == 'B2B'){
        $cust_name = $sq_customer_info['company_name'];
    }else{
        $cust_name = $sq_customer_info['first_name'].' '.$sq_customer_info['last_name'];
    }
}
else{
    $cust_name = "";
}

if($from_date!="" && $to_date!=""){
    $date_str = $from_date.' to '.$to_date;
}
else{
    $date_str = "";
}

$invoice_id = ($train_ticket_id!="") ? get_train_ticket_booking_id($train_ticket_id,$year): "";
if($company_name == 'undefined') { $company_name = ''; }
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'Train Ticket Booking')
            ->setCellValue('B3', 'Booking ID')
            ->setCellValue('C3', $invoice_id)
            ->setCellValue('B4', 'Customer')
            ->setCellValue('C4', $cust_name)
             ->setCellValue('B5', 'From-To Date')
            ->setCellValue('C5', $date_str)
            ->setCellValue('B6', 'Customer Type')
            ->setCellValue('C6', $cust_type)
            ->setCellValue('B7', 'Company Name')
            ->setCellValue('C7', $company_name);

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

$query = "select * from train_ticket_master where financial_year_id='$financial_year_id' and delete_status='0'";
if($customer_id!=""){
    $query .= " and customer_id='$customer_id'";
}
if($train_ticket_id!="")
{
    $query .= " and train_ticket_id='$train_ticket_id'";
}
if($from_date!="" && $to_date!=""){
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date = date('Y-m-d', strtotime($to_date));
    $query .= " and created_at between '$from_date' and '$to_date'";
}
if($company_name != ""){
$query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
if($cust_type != ""){
$query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
include "../../../../model/app_settings/branchwise_filteration.php";
$query .= " order by train_ticket_id desc";
$row_count = 9;
$count = 0;
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "Invoice No")
        ->setCellValue('C'.$row_count, "Booking ID")
        ->setCellValue('D'.$row_count, "Customer Name")
        ->setCellValue('E'.$row_count, "Mobile No")
        ->setCellValue('F'.$row_count, "Train No")
        ->setCellValue('G'.$row_count, "Trip Type")
        ->setCellValue('H'.$row_count, "Travel Date & Time")
        ->setCellValue('I'.$row_count, "Ticket Amount")
        ->setCellValue('J'.$row_count, "Cancellation Amount")
        ->setCellValue('K'.$row_count, "Total Amount")
        ->setCellValue('L'.$row_count, "Paid Amount")
        ->setCellValue('M'.$row_count, "Created By")
        ->setCellValue('N'.$row_count, "Booking Date");
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':N'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':N'.$row_count)->applyFromArray($borderArray);    

$row_count++;
 $sq_ticket = mysqlQuery($query);
while($row_ticket = mysqli_fetch_assoc($sq_ticket)){
//Paid
$query = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum,sum(credit_charges) as sumc from train_ticket_payment_master where train_ticket_id='$row_ticket[train_ticket_id]' and clearance_status != 'Pending' and clearance_status != 'Cancelled'"));
$paid_amount = $query['sum'] + $query['sumc'];
$paid_amount = ($paid_amount == '') ? '0' : $paid_amount;
    $sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_ticket[emp_id]'"));
    $emp_name = ($row_ticket['emp_id'] != 0) ? $sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';

    $date = $row_ticket['created_at'];
    $yr = explode("-", $date);
    $year =$yr[0];
    $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_ticket[customer_id]'"));
	$contact_no = $encrypt_decrypt->fnDecrypt($sq_customer_info['contact_no'], $secret_key);
    if($sq_customer_info['type']=='Corporate'||$sq_customer_info['type'] == 'B2B'){
        $customer_name = $sq_customer_info['company_name'];
    }else{
        $customer_name = $sq_customer_info['first_name'].' '.$sq_customer_info['last_name'];
    }

	$sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum,sum(credit_charges) as sumc from train_ticket_payment_master where train_ticket_id='$row_ticket[train_ticket_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
    $credit_card_charges = $sq_paid_amount['sumc'];
    
    $sq_train_info = mysqli_fetch_assoc(mysqlQuery("select * from train_ticket_master_trip_entries where train_ticket_id='$row_ticket[train_ticket_id]'"));

    $sale_amount = $row_ticket['net_total']-$row_ticket['cancel_amount'];
    $cancel_amt = $row_ticket['cancel_amount'];
    if($cancel_amt == ""){ $cancel_amt = 0;}

    $total_sale = $total_sale + $row_ticket['net_total']+$credit_card_charges;
    $total_cancelation_amount = $total_cancelation_amount + $cancel_amt;
    $total_balance = $total_balance+$sale_amount+$credit_card_charges;	

	$invoice_date = date('d-m-Y',strtotime($row_ticket['created_at']));
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, $row_ticket['invoice_pr_id'])
        ->setCellValue('C'.$row_count, get_train_ticket_booking_id($row_ticket['train_ticket_id'],$year))
        ->setCellValue('D'.$row_count, $customer_name)
        ->setCellValue('E'.$row_count, $contact_no)
        ->setCellValue('F'.$row_count, $sq_train_info['train_no'])
        ->setCellValue('G'.$row_count, $row_ticket['type_of_tour'])
        ->setCellValue('H'.$row_count, get_datetime_user($sq_train_info['travel_datetime']))
        ->setCellValue('I'.$row_count, number_format($row_ticket['net_total']+$credit_card_charges,2))
        ->setCellValue('J'.$row_count, number_format($cancel_amt,2))
        ->setCellValue('K'.$row_count, number_format($row_ticket['net_total']-$row_ticket['cancel_amount']+$credit_card_charges,2))
        ->setCellValue('L'.$row_count,$paid_amount)
        ->setCellValue('M'.$row_count,$emp_name)
        ->setCellValue('N'.$row_count,$invoice_date);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':N'.$row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':N'.$row_count)->applyFromArray($borderArray);    


    $row_count++;

    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B'.$row_count, "")
    ->setCellValue('C'.$row_count, "")
    ->setCellValue('D'.$row_count, "")
    ->setCellValue('E'.$row_count, "")
    ->setCellValue('F'.$row_count, "")
    ->setCellValue('G'.$row_count, "")
    ->setCellValue('H'.$row_count, "Total")
    ->setCellValue('I'.$row_count, number_format($total_sale,2))
    ->setCellValue('J'.$row_count, number_format($total_cancelation_amount,2))
    ->setCellValue('K'.$row_count, number_format($total_balance,2))
    ->setCellValue('L'.$row_count, "")
    ->setCellValue('M'.$row_count, "")
    ->setCellValue('N'.$row_count, "");

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':N'.$row_count)->applyFromArray($header_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':N'.$row_count)->applyFromArray($borderArray);
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
header('Content-Disposition: attachment;filename="Train Ticket Report('.date('d-m-Y H:i').').xls"');
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
