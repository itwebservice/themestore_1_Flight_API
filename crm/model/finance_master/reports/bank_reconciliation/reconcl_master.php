<?php 
class reconcl_master{

public function reconcl_master_save()
{
	$branch_admin_id = $_POST['branch_admin_id'];
	$bank_id = $_POST['bank_id'];
	$bank_book_amount = $_POST['bank_book_amount'];
	$system_bank_amount = $_POST['system_bank_amount'];
	$reconcl_amount = $_POST['reconcl_amount'];
	$diff_amount  = $_POST['diff_amount'];

	$debit_date_arr = isset($_POST['debit_date_arr'])?$_POST['debit_date_arr']:[];
	$debit_for_arr = isset($_POST['debit_for_arr']) ? $_POST['debit_for_arr']: [];
	$debit_amount_arr = isset($_POST['debit_amount_arr']) ? $_POST['debit_amount_arr']: [];
	$total_debit = isset($_POST['total_debit']) ? $_POST['total_debit']: 0;

	$credit_date_arr = isset($_POST['credit_date_arr'])?$_POST['credit_date_arr']:[];
	$credit_for_arr = isset($_POST['credit_for_arr']) ? $_POST['credit_for_arr']: [];
	$credit_amount_arr = isset($_POST['credit_amount_arr']) ? $_POST['credit_amount_arr']: [];	
	$total_credit = isset($_POST['total_credit']) ? $_POST['total_credit']: 0;

	$depos_date_arr = isset($_POST['depos_date_arr'])?$_POST['depos_date_arr']:[];
	$depos_chq_arr = isset($_POST['depos_chq_arr']) ? $_POST['depos_chq_arr']: [];
	$depos_sale_arr = isset($_POST['depos_sale_arr']) ? $_POST['depos_sale_arr']: [];	
	$depos_saleid_arr = isset($_POST['depos_saleid_arr']) ? $_POST['depos_saleid_arr']: [];
	$depos_amount_arr = isset($_POST['depos_amount_arr']) ? $_POST['depos_amount_arr']: [];	
	$total_depos = isset($_POST['total_depos']) ? $_POST['total_depos']: 0;		

	$pay_date_arr = isset($_POST['pay_date_arr'])?$_POST['pay_date_arr']:[];
	$pay_chq_arr = isset($_POST['pay_chq_arr']) ? $_POST['pay_chq_arr']: [];
	$pay_sale_arr = isset($_POST['pay_sale_arr']) ? $_POST['pay_sale_arr']: [];	
	$pay_saleid_arr = isset($_POST['pay_saleid_arr']) ? $_POST['pay_saleid_arr']: [];
	$pay_amount_arr = isset($_POST['pay_amount_arr']) ? $_POST['pay_amount_arr']: [];	
	$total_pay = isset($_POST['total_pay']) ? $_POST['total_pay']: 0;

	$date1 = date('Y-m-d');
	begin_t();

	//Master table
	$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from bank_reconcl_master"));
	$id = $sq_max['max'] + 1;
	$sq_cash = mysqlQuery("insert into bank_reconcl_master (id, bank_id, reconcl_date, branch_admin_id, book_balance, cheque_deposit, cheque_payment, bank_debit_amount, bank_credit_amount, reconcl_amount, bank_book_balance, diff_amount) values ('$id','$bank_id','$date1','$branch_admin_id','$system_bank_amount','$total_depos','$total_pay','$total_debit','$total_credit','$reconcl_amount','$bank_book_amount','$diff_amount')");

	if($sq_cash){
		//Cheque Deposited
		for($i = 0;$i<sizeof($depos_date_arr);$i++){
			if(isset($depos_amount_arr[$i]) && $depos_amount_arr[$i] != ''){
				$deposit_date = get_date_db($depos_date_arr[$i]);
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from bank_reconcl_receipt"));
				$entry_id = $sq_max['max'] + 1;
				$sq_deposit = mysqlQuery("insert into bank_reconcl_receipt (entry_id, id, date, cheque_no,sale,sale_id, amount) values ('$entry_id', '$id', '$deposit_date', '$depos_chq_arr[$i]', '$depos_sale_arr[$i]','$depos_saleid_arr[$i]','$depos_amount_arr[$i]')");
			}
			
		}

		//Cheque Payment
		for($i = 0;$i<sizeof($pay_date_arr);$i++){
			if(isset($depos_amount_arr[$i]) && $depos_amount_arr[$i] != ''){
				$pay_date = get_date_db($pay_date_arr[$i]);
				$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from bank_reconcl_payment"));
				$entry_id = $sq_max['max'] + 1;
				$sq_payment = mysqlQuery("insert into bank_reconcl_payment (entry_id, id, date, cheque_no,purchase,purchase_id, amount) values ('$entry_id', '$id', '$pay_date', '$pay_chq_arr[$i]', '$pay_sale_arr[$i]','$pay_saleid_arr[$i]','$pay_amount_arr[$i]')");
			}
		}

		//Bank Debits For
		for($i = 0;$i<sizeof($debit_date_arr);$i++){
			$debit_date = get_date_db($debit_date_arr[$i]);
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from bank_reconcl_debit_for"));
			$entry_id = $sq_max['max'] + 1;
			$sq_debit = mysqlQuery("insert into bank_reconcl_debit_for  (entry_id, id,date, debit_for, amount) values ('$entry_id', '$id', '$debit_date', '$debit_for_arr[$i]', '$debit_amount_arr[$i]')");
		}
		//Bank Credits For
		for($i = 0;$i<sizeof($credit_date_arr);$i++){
			$credit_date = get_date_db($credit_date_arr[$i]);
			$sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from bank_reconcl_credit_for"));
			$entry_id = $sq_max['max'] + 1;
			$sq_credit = mysqlQuery("insert into bank_reconcl_credit_for  (entry_id, id,date, credit_for, amount) values ('$entry_id', '$id', '$credit_date', '$credit_for_arr[$i]', '$credit_amount_arr[$i]')");
		}
		commit_t();
		echo "Bank Reconciliation Done!";
		exit;
	}
	else{
		rollback_t();
		echo "error--Sorry, Bank Reconciliation Not Done!";
		exit;
	}

}
//Approval save
public function reconcl_approval_save()
{
	$approve_status = $_POST['approve_status'];
	$cash_id = $_POST['cash_id'];

	$sq_approve = mysqlQuery("Update cash_reconcl_master set approval_status ='$approve_status' where id='$cash_id'");
	if($sq_approve){
		commit_t();
		echo "Approval Done!";
		exit;
	}
	else{
		rollback_t();
		echo "error--Sorry, Approval Not Done!";
		exit;
	}
}

}
?>