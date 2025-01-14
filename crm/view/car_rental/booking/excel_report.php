<?php
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
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$branch_status = $_GET['branch_status'];
$customer_id = $_GET['customer_id'];
$from_date = $_GET['from_date'];
$to_date = $_GET['to_date'];
$cust_type = $_GET['cust_type'];
$company_name = (isset($_GET['company_name'])) ? $_GET['company_name'] : '';
$booking_id_filter = $_GET['booking_id_filter'];

if($customer_id!=""){
    $sq_customer_info = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$customer_id'"));
	if($sq_customer_info['type'] == 'Corporate' || $sq_customer_info['type']=='B2B'){
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
if($company_name == 'undefined') { $company_name = ''; }
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B2', 'Report Name')
            ->setCellValue('C2', 'Car Rental Booking')
            ->setCellValue('B3', 'Customer')
            ->setCellValue('C3', $cust_name)
            ->setCellValue('B4', 'From-To Date')
            ->setCellValue('C4', $date_str)
            ->setCellValue('B5', 'Customer Type')
            ->setCellValue('C5', $cust_type)
            ->setCellValue('B6', 'Company Name')
            ->setCellValue('C6', $company_name);

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

$query = "select * from car_rental_booking where financial_year_id='$financial_year_id' and delete_status='0'";
if($customer_id!=""){
    $query .= " and customer_id='$customer_id'";
}
if($booking_id_filter!=''){
    $query .= " and booking_id='$booking_id_filter'";
}
if($from_date!='' && $to_date!=''){
    $traveling_date_from = get_date_db($from_date);
    $traveling_date_to = get_date_db($to_date);

    $query .=" and date(created_at) between '$traveling_date_from' and '$traveling_date_to'";
}
if($company_name != ""){
    $query .= " and customer_id in (select customer_id from customer_master where company_name = '$company_name')";
}
if($cust_type != ""){
    $query .= " and customer_id in (select customer_id from customer_master where type = '$cust_type')";
}
include "../../../model/app_settings/branchwise_filteration.php";
    $count = 1;
    $row_count = 8;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B'.$row_count, "Invoice No")
            ->setCellValue('C'.$row_count, "Booking ID")
            ->setCellValue('D'.$row_count, "Customer Name")
            ->setCellValue('E'.$row_count, "Mobile No")
            ->setCellValue('F'.$row_count, "Email ID")
            ->setCellValue('G'.$row_count, "No Of Pax")
            ->setCellValue('H'.$row_count, "Amount")
            ->setCellValue('I'.$row_count, "CNCL_Amount")
            ->setCellValue('J'.$row_count, "Total")
            ->setCellValue('K'.$row_count, "Paid Amount")
            ->setCellValue('L'.$row_count, "Created By")
            ->setCellValue('M'.$row_count, "Booking Date");


    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':M'.$row_count)->applyFromArray($header_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':M'.$row_count)->applyFromArray($borderArray);    

    $row_count++;
    $total_sale = 0;
    $total_cancelation_amount = 0;
    $total_balance = 0;
    $sq_booking = mysqlQuery($query);
    while($row_booking = mysqli_fetch_assoc($sq_booking)){
        //Paid
$query = mysqli_fetch_assoc(mysqlQuery("SELECT sum(payment_amount) as sum ,sum(credit_charges) as sumc from car_rental_payment where booking_id='$row_booking[booking_id]' and clearance_status != 'Pending' and clearance_status != 'Cancelled'"));
$paid_amount = $query['sum'] + $query['sumc'];
$paid_amount = ($paid_amount == '')?'0':$paid_amount;

        $sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_booking[emp_id]'"));
        $emp_name = ($row_booking['emp_id'] != 0) ? $sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';

        $date = $row_booking['created_at'];
                    $yr = explode("-", $date);
                    $year =$yr[0];
        $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$row_booking[customer_id]'"));
        $contact_no = $encrypt_decrypt->fnDecrypt($sq_customer['contact_no'], $secret_key);
        $email_id = $encrypt_decrypt->fnDecrypt($sq_customer['email_id'], $secret_key); 
       
        $sq_paid_amount = mysqli_fetch_assoc(mysqlQuery("SELECT sum(`credit_charges`) as sumc from car_rental_payment where booking_id='$row_booking[booking_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
        $credit_card_charges = $sq_paid_amount['sumc'];
        
        if($sq_customer['type']=='Corporate'||$sq_customer['type'] == 'B2B'){
            $customer_name = $sq_customer['company_name'];
        }else{
            $customer_name = $sq_customer['first_name'].' '.$sq_customer['last_name'];
        }
        
        $total_sale = $total_sale+$row_booking['total_fees'] + $credit_card_charges;
        $total_cancelation_amount = $total_cancelation_amount+$row_booking['cancel_amount'];
        $total_balance = $total_sale-$total_cancelation_amount;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, $row_booking['invoice_pr_id'])
        ->setCellValue('C'.$row_count, get_car_rental_booking_id($row_booking['booking_id'],$year))
        ->setCellValue('D'.$row_count, $customer_name)
        ->setCellValue('E'.$row_count, $contact_no)
        ->setCellValue('F'.$row_count, $email_id)
        ->setCellValue('G'.$row_count, $row_booking['total_pax'])
        ->setCellValue('H'.$row_count, number_format(($row_booking['total_fees'] + $credit_card_charges),2))
        ->setCellValue('I'.$row_count, number_format($row_booking['cancel_amount'],2))
        ->setCellValue('J'.$row_count, number_format(($row_booking['total_fees'] + $credit_card_charges-$row_booking['cancel_amount']),2))
        ->setCellValue('K'.$row_count, $paid_amount)
        ->setCellValue('L'.$row_count, $emp_name)
        ->setCellValue('M'.$row_count, date('d-m-Y',strtotime($row_booking['created_at'])));

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':M'.$row_count)->applyFromArray($content_style_Array);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':M'.$row_count)->applyFromArray($borderArray);    

    $row_count++;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "")
        ->setCellValue('C'.$row_count, "")
        ->setCellValue('D'.$row_count, "")
        ->setCellValue('E'.$row_count, "")
        ->setCellValue('F'.$row_count, "")
        ->setCellValue('G'.$row_count, "")
        ->setCellValue('H'.$row_count, 'Amount : '.number_format($total_sale,2))
        ->setCellValue('I'.$row_count, 'CNCL_Amount : '.number_format($total_cancelation_amount,2))
        ->setCellValue('J'.$row_count, 'Total : '.number_format($total_balance,2));

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':J'.$row_count)->applyFromArray($borderArray);

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
header('Content-Disposition: attachment;filename="CarRentalBooking('.date('d-m-Y H:i').').xls"');
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
