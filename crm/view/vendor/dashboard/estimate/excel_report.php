<?php
include "../../../../model/model.php";
include_once('../../inc/vendor_generic_functions.php');
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
$branch_admin_id = $_SESSION['branch_admin_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$branch_status = $_GET['branch_status']; 
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];

$estimate_type = $_GET['estimate_type'];
$vendor_type = $_GET['vendor_type'];
$vendor_type_id = $_GET['vendor_type_id'];
$estimate_type_id = $_GET['estimate_type_id'];

if($vendor_type!=""){
    $vendor_str=$vendor_type;
}
else{
    $vendor_str = "";
}

if($estimate_type!=""){
    $est_str = $estimate_type;
}
else{
    $est_str = "";
}

if($estimate_type_id!=""){
    $est_str1 = get_estimate_type_name($estimate_type, $estimate_type_id);
}
else{
    $est_str1 = "";
}

if($vendor_type_id!=""){
    $vendor_str1 = get_vendor_name($vendor_type, $vendor_type_id);
}
else{
    $vendor_str1 = "";
}

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B2', 'Report Name')
    ->setCellValue('C2', 'Purchase Cost')
    ->setCellValue('B3', 'Purchase Type')
    ->setCellValue('C3', $est_str)
    ->setCellValue('B4', 'Supplier Type')
    ->setCellValue('C4', $vendor_str)
    ->setCellValue('B5', 'Purchase ID')
    ->setCellValue('C5', $est_str1)
    ->setCellValue('B6', 'Supplier Name')
    ->setCellValue('C6', $vendor_str1);

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

$row_count = 8;

$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "Sr. No")
        ->setCellValue('C'.$row_count, "Purchase Date")
        ->setCellValue('D'.$row_count, "Purchase Type")
        ->setCellValue('E'.$row_count, "Purchase ID")
        ->setCellValue('F'.$row_count, "Supplier Type")
        ->setCellValue('G'.$row_count, "Supplier Name")
        ->setCellValue('H'.$row_count, "Remark")
        ->setCellValue('I'.$row_count, "Amount")
        ->setCellValue('J'.$row_count, "Cncl_Amount")
        ->setCellValue('K'.$row_count, "Total_Amount")
        ->setCellValue('L'.$row_count, "Created By");

$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':L'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':L'.$row_count)->applyFromArray($borderArray);    

$row_count++;
$total_estimate_amt = 0;
$total_balance = 0;
$count = 0;
$query = "select * from vendor_estimate where financial_year_id='$financial_year_id' and delete_status='0'";
if($estimate_type!=""){
	$query .= "and estimate_type='$estimate_type'";
}
if($vendor_type!=""){
	$query .= "and vendor_type='$vendor_type'";
}
if($estimate_type_id!=""){
	$query .= "and estimate_type_id='$estimate_type_id'";
}
if($vendor_type_id!=""){
	$query .= "and vendor_type_id='$vendor_type_id'";
}

if($branch_status=='yes' && $role!='Admin'){
    $query .= " and branch_admin_id = '$branch_admin_id'";
}
elseif($role!='Admin' && $role!='Branch Admin' && $role_id!='7' && $role_id<'7'){
$query .= " and emp_id='$emp_id'";
}
$sq_estimate = mysqlQuery($query);
while($row_estimate = mysqli_fetch_assoc($sq_estimate)){
    
	$sq_emp =  mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id = '$row_estimate[emp_id]'"));
	$emp_name = ($row_estimate['emp_id'] != 0) ? $sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin';
	$date = $row_estimate['purchase_date'];
	$yr = explode("-", $date);
	$year = $yr[0];
	$total_cancel_amt += $row_estimate['cancel_amount'];
	$total_estimate_amt += $row_estimate['net_total'];

	$estimate_type_val = get_estimate_type_name($row_estimate['estimate_type'], $row_estimate['estimate_type_id']);
	$vendor_type_val = get_vendor_name($row_estimate['vendor_type'], $row_estimate['vendor_type_id']);

	$purchase_amount = $row_estimate['net_total'] - $row_estimate['cancel_amount'];
	$total_purchase_amt += $purchase_amount;

	$sq_paid_amount_query = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as sum from vendor_payment_master where estimate_id='$row_estimate[estimate_id]' and clearance_status!='Pending' and clearance_status!='Cancelled'"));
	$paid_amount = $sq_paid_amount_query['sum'];
	$total_paid_amt += $paid_amount;
	if($total_paid_amt==""){ $total_paid_amt = 0; }

	$cancel_amount = $row_estimate['cancel_amount'];
	if($row_estimate['purchase_return'] == '1'){
		if($paid_amount > 0){
			if($cancel_amount >0){
				if($paid_amount > $cancel_amount){
					$balance_amount = 0;
				}else{
					$balance_amount = $cancel_amount - $paid_amount;
				}
			}else{
				$balance_amount = 0;
			}
		}
		else{
			$balance_amount = $cancel_amount;
		}
	}else if($row_estimate['purchase_return'] == '2'){
		$cancel_estimate = json_decode($row_estimate['cancel_estimate']);
		$balance_amount = (($row_estimate['net_total'] - floatval($cancel_estimate[0]->net_total)) + $cancel_amount) - $paid_amount;
	}
	else{
		$balance_amount = $row_estimate['net_total'] - $paid_amount;
	}

	$total_balance += $balance_amount;
	// Currency conversion
	$currency_amount1 = currency_conversion($currency,$row_estimate['currency_code'],$purchase_amount);
	if($row_estimate['currency_code'] !='0' && $currency != $row_estimate['currency_code']){
		$currency_amount = ' ('.$currency_amount1.')';
	}else{
		$currency_amount = '';
	}
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, $row_estimate['estimate_id'])
        ->setCellValue('C'.$row_count, get_date_user($row_estimate['purchase_date']))
        ->setCellValue('D'.$row_count, $row_estimate['estimate_type'])
        ->setCellValue('E'.$row_count, $estimate_type_val)
        ->setCellValue('F'.$row_count, $row_estimate['vendor_type'])
        ->setCellValue('G'.$row_count, $vendor_type_val)
        ->setCellValue('H'.$row_count, $row_estimate['remark'])
        ->setCellValue('I'.$row_count, number_format($row_estimate['net_total'],2))
        ->setCellValue('J'.$row_count, ($row_estimate['cancel_amount']=="") ? 0 : number_format($row_estimate['cancel_amount'],2))
        ->setCellValue('K'.$row_count,  number_format($purchase_amount,2).$currency_amount)
        ->setCellValue('L'.$row_count, $emp_name);
	

    $objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':L'.$row_count)->applyFromArray($content_style_Array);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':L'.$row_count)->applyFromArray($borderArray);    

	$row_count++;

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B'.$row_count, "")
        ->setCellValue('C'.$row_count, "")
        ->setCellValue('D'.$row_count, "")
        ->setCellValue('E'.$row_count, "")
        ->setCellValue('F'.$row_count, "")
        ->setCellValue('G'.$row_count, "Total Amount : ".number_format($total_estimate_amt, 2))
        ->setCellValue('H'.$row_count, "Total Cancel : ".number_format($total_cancel_amt, 2))
        ->setCellValue('I'.$row_count, "Total Purchase : ".number_format($total_purchase_amt, 2))
        ->setCellValue('J'.$row_count, "Total Paid : ".number_format($total_paid_amt, 2))
        ->setCellValue('K'.$row_count, "Balance : ".number_format($total_balance, 2))
        ->setCellValue('L'.$row_count, "");
        
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':L'.$row_count)->applyFromArray($header_style_Array);
$objPHPExcel->getActiveSheet()->getStyle('B'.$row_count.':L'.$row_count)->applyFromArray($borderArray);
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
header('Content-Disposition: attachment;filename="Purchase Cost Report('.date('d-m-Y H:i').').xls"');
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
