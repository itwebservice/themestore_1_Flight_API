<?php
class b2b_customer{
    //Registration link send
    function reg_form_send(){
        
        global $model,$app_name;
        $email_id = $_POST['email_id'];
        $mobile_no = $_POST['mobile_no'];
        $content = '             
            <tr>
                <td>
                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <a style="font-weight:500;font-size:14px;display:block;color:#ffffff;background:#009898;text-decoration:none;padding:5px 10px;border-radius:25px;width: 95px;text-align: center;" href="'.BASE_URL.'view/b2b_customer/registration/registration.php">Register</a> 
                        </td> 
                    </tr>
                </table>
                </td>
            </tr>
        ';
        if($mobile_no != ''){
            $message = 'Dear Travel Partners,'. 
'Request you to fill the attached registration form. And will get back to you with your credentials soon. Use this link :- '.BASE_URL.'model/attractions_offers_enquiry/tour_enquiry.php';
            $model->send_message($mobile_no, $message);
        }

        $subject = "B2B Portal Registration Request : ".$app_name;
        $model->app_email_send('104','Travel Partners',$email_id, $content,$subject,'1');

        echo "The registration form has been successfully sent";
    }

    //Quotation mail send
    function quotation_send(){

        global $model;
        $quotation_id = $_POST['quotation_id'];
        $email_id = $_POST['email_id'];
        $url = $_POST['url'];
        
        $sq_quotation = mysqli_fetch_assoc(mysqlQuery("select register_id,pdf_data_array from b2b_quotations where quotation_id='$quotation_id'"));
        $pdf_data_array = ($sq_quotation['pdf_data_array']!='') ? json_decode($sq_quotation['pdf_data_array']):[];
        $cust_name = $pdf_data_array[0]->cust_name;

        $sq_reg = mysqli_fetch_assoc(mysqlQuery("select company_name from b2b_registration where register_id='$sq_quotation[register_id]'"));
        $content = '
            <tr>
                <td>
                <table style="width:100%">
                    <tr>
                        <td colspan="2">
                            <a style="font-weight:500;font-size:14px;color:#ffffff;background:#009898;text-decoration:none;padding:5px 10px;border-radius:25px;width: 95px;text-align: center;" href="'.$url.'">View Quotation</a> 
                        </td> 
                    </tr>
                </table>
                </td>
            </tr>
        ';
        $subject = 'New Quotation : '.$cust_name.' : ('.$sq_reg['company_name'].' )';
        $model->app_email_send('8',$cust_name,$email_id, $content,$subject,'1');

        echo "The quotation has been successfully sent";
    }
    function quotation_whatsapp(){
        
		$quotation_id = $_POST['quotation_id'];
        $url = $_POST['url'];
		
		$all_message = "";
        $sq_quotation = mysqli_fetch_assoc(mysqlQuery("select * from b2b_quotations where quotation_id='$quotation_id'"));
        $sq_reg = mysqli_fetch_assoc(mysqlQuery("select mobile_no from b2b_registration where register_id='$sq_quotation[register_id]'"));
        $mobile_no = $sq_reg['mobile_no'];
        $pdf_data_array = json_decode($sq_quotation['pdf_data_array']);
        $cust_name = $pdf_data_array[0]->cust_name;
        $contact_no = $pdf_data_array[0]->contact_no;

        $whatsapp_msg = 'Dear%20'.rawurlencode($cust_name).',%0aHope%20you%20are%20doing%20great.%20This%20is%20tour%20quotation%20details%20as%20per%20your%20request.%20We%20look%20forward%20to%20having%20you%20onboard%20with%20us.%0a'.'%0a*Link*%20:%20'.$url.'%0aPlease%20contact%20for%20more%20details%20:%20'.$mobile_no.'%0aThank%20you.%0a';
        $all_message .=$whatsapp_msg;

        $link = 'https://web.whatsapp.com/send?phone='.$contact_no.'&text='.$all_message;
		echo $link;
    }
    function password_reset(){
        global $model,$app_name,$encrypt_decrypt,$secret_key,$theme_color;
        $username = $_POST['username'];
        $agent_code = $_POST['agent_code'];
        $new_password = $_POST['new_password'];
        $email = $_POST['email'];
        $new_password1 = $encrypt_decrypt->fnEncrypt($new_password, $secret_key);

        $sq_query = mysqlQuery("update b2b_registration set password='$new_password1' where username='$username' and agent_code='$agent_code' and active_flag!='Inactive' and approval_status='Approved'");
        if($sq_query){
            $content = '             
            <tr>
            <td>
            <table style="width:100%">
                <tr>
                    <td colspan="2">
                        <p style="color:#888888 !important">Your Travel agent portal login password is reset successfully for '.$app_name.'</p>
                        <p style="color:#888888 !important">Your new password is '.$new_password.'</p>
                    </td> 
                </tr>
                <tr>
                    <td colspan="2">
                    <a style="font-weight:500;margin:10px auto;font-size:14px;display:block;color:#ffffff;background:'.$theme_color.';text-decoration:none;padding:5px 10px;border-radius:25px;width: 95px;text-align: center;" href="'.BASE_URL.'Tours_B2B/login.php">Click For Login!</a>
                    </td>
                </tr>
            </table>
            </td>
        </tr>  
            ';

            $subject = "Password Reset successfully!";
            $model->app_email_master($email, $content, $subject, '1');

            echo "Password Reset successfully, Login Again!";
        }
    }

    //B2B customer deposit update
    function customer_update(){
        $register_id = $_POST['register_id'];
        $credit_limit = $_POST['credit_limit'];
        $deposit = $_POST['deposit'];
        $old_deposit = $_POST['old_deposit'];
        $active_flag = $_POST['active_flag'];
        $agreement_url = $_POST['agreement_url'];
        $approve_status= $_POST['approve_status'];

        $payment_date = $_POST['payment_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'];
        $transaction_id = $_POST['transaction_id'];
        $bank_id= $_POST['bank_id'];

        $financial_year_id = $_SESSION['financial_year_id'];
        $branch_admin_id = $_SESSION['branch_admin_id'];
        $emp_id = $_SESSION['emp_id'];

        $clearance_status = ($payment_mode!="Cash") ? "Pending" : '';
        $payment_date = date('Y-m-d', strtotime($payment_date));

        //Agent Code creation
        $sq_approve = mysqli_fetch_assoc(mysqlQuery("select * from b2b_registration where register_id='$register_id'"));

        $sq_b2b = mysqlQuery("update b2b_registration set financial_year_id='$financial_year_id',emp_id='$emp_id',branch_admin_id='$branch_admin_id', payment_date='$payment_date', payment_mode='$payment_mode', bank_name='$bank_name', transaction_id='$transaction_id', bank_id='$bank_id', clearance_status='$clearance_status',deposite='$deposit',active_flag='$active_flag',agreement_url='$agreement_url',approval_status='$approve_status' where register_id='$register_id'");

        $status = ($approve_status=='Approved') ? '1' : '0';
        mysqlQuery("update `b2b_login` set `status`='$status' where agent_flag='1' and user_id='$register_id'");
        if(!$sq_b2b){
            echo "error--Sorry Status not updated!";
            exit;
        }
        else{
            if($approve_status=='Approved' && $sq_approve['mail_status']==''){
                $agent_company = trim(substr($sq_approve['company_name'],0,3));
                $mobile_noa = rtrim($sq_approve['mobile_no']);
                $agent_mobile = substr($mobile_noa,-4);
                $agent_code = $agent_company.$agent_mobile;
                global $encrypt_decrypt, $secret_key;
                $agent_mobile = $encrypt_decrypt->fnEncrypt($agent_mobile, $secret_key);
                $agent_company = $encrypt_decrypt->fnEncrypt($agent_company, $secret_key);

                $sq_b2b_temp = mysqlQuery("update b2b_registration set username='$agent_company',password='$agent_mobile',agent_code='$agent_code' where register_id='$register_id'");

                //B2B customer creation
                $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
                $customer_id = $sq_max['max'] + 1;
                $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$sq_approve[city]'"));
                $mobile_no1 = $encrypt_decrypt->fnEncrypt($mobile_noa, $secret_key);
                $email_id1 = $encrypt_decrypt->fnEncrypt($sq_approve['email_id'], $secret_key);

                $sq_customer = mysqlQuery("insert into customer_master (customer_id,type,first_name, middle_name, last_name, gender, birth_date, age, contact_no,landline_no, email_id,alt_email,company_name, address, address2, city, active_flag, created_at,service_tax_no,state_id,pan_no, branch_admin_id) values ('$customer_id','B2B', '$sq_approve[cp_first_name]', '', '$sq_approve[cp_last_name]', '', '', '', '$mobile_no1','$sq_approve[telephone]', '$email_id1','','$sq_approve[company_name]', '$sq_approve[address1]','$sq_approve[address2]','$sq_city[city_name]', 'Active', '$sq_approve[created_at]', '','$sq_approve[state]','$sq_approve[pan_card]','$sq_approve[branch_admin_id]')");

                //Ledger Creation
                $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(ledger_id) as max from ledger_master"));
                $ledger_id = $sq_max['max'] + 1;
                $ledger_name = $sq_approve['company_name'];
                $sq_ledger = mysqlQuery("insert into ledger_master (ledger_id, ledger_name, alias, group_sub_id, balance, dr_cr,customer_id,user_type) values ('$ledger_id', '$ledger_name', '', '20', '0','Dr','$customer_id','customer')");

                //Update customer id
                $sq_b2b = mysqlQuery("update b2b_registration set customer_id='$customer_id' where register_id='$register_id'");

                //Credit Limit insert
                $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2b_creditlimit_master"));
                $entry_id = $sq_max['max'] + 1;
                $sq_credit = mysqlQuery("insert into b2b_creditlimit_master (entry_id,register_id,credit_amount, reason_for_revise, raised_by, credit_type, approval_status, created_at) values ('$entry_id','$register_id', '$credit_limit', '', '', '', '','$sq_approve[created_at]')");

                //Login creation
                $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from b2b_login"));
                $login_id = $sq_max['max'] + 1;
                mysqlQuery("INSERT INTO `b2b_login`(`id`, `user_id`, `agent_flag`, `agent_code`, `username`, `password`, `status`) VALUES ('$login_id','$register_id','1','$agent_code','$agent_company','$agent_mobile','1')");

                //Finance Save
                $this->finance_save($register_id,$branch_admin_id);
                //Bank and Cash Book Save
                $this->bank_cash_book_save($register_id,$branch_admin_id);
            
                $this->mail_b2blogin_box($agent_company, $agent_mobile,$agent_code, $sq_approve['email_id'],$register_id);
                $sq_reg = mysqlQuery("update b2b_registration set mail_status='Sent' where register_id='$register_id'");
                
            }
            else{
                if($approve_status == 'Rejected'){
                    $this->rejection_mail_Send($sq_approve['email_id'],$sq_approve['company_name']);
                }
                if($sq_approve['reference']=='direct'){
                    $sq_dep_count = mysqli_fetch_assoc(mysqlQuery("select * from finance_transaction_master where module_name='B2b Deposit' and module_entry_id='$register_id'"));
                    if($sq_dep_count == 0 && $deposit != 0){
                        $this->finance_save($register_id,$branch_admin_id);
                        //Bank and Cash Book Save
                        $this->bank_cash_book_save($register_id,$branch_admin_id);
                    }
                    else{
                        //Finance update
                        $this->finance_update($register_id,$branch_admin_id);
                        //Bank and Cash Book update
                        $this->bank_cash_book_update($register_id,$branch_admin_id);
                    }
                }
            }
            echo "The Status has been successfully updated.";
            exit;
        }
    }

    //Approved b2b customer save
    function b2b_customer_save(){

        //Basic Details
        $company_name = $_POST['company_name'];
        $acc_name = $_POST['acc_name'];
        $iata_status = $_POST['iata_status'];
        $iata_reg = $_POST['iata_reg'];
        $nature = $_POST['nature'];
        $currency = $_POST['currency'];
        $telephone = $_POST['telephone'];
        $latitude= $_POST['latitude'];
        $turnover_slab = $_POST['turnover_slab'];
        $skype_id = $_POST['skype_id'];
        $website = $_POST['website'];
        $company_logo = $_POST['company_logo'];
        //Address Details
        $address1 = $_POST['address1'];
        $address2 = $_POST['address2'];
        $city = $_POST['city'];
        $pincode = $_POST['pincode'];
        $state = $_POST['state'];
        $timezone = $_POST['timezone'];
        $address_upload_url = $_POST['address_upload_url'];
        //Address Details
        $contact_personf = $_POST['contact_personf'];
        $contact_personl = $_POST['contact_personl'];
        $email_id = $_POST['email_id'];
        $mobile_no = $_POST['mobile_no'];
        $whatsapp_no = $_POST['whatsapp_no'];
        $designation = $_POST['designation'];
        $pan_card = $_POST['pan_card'];
        $photo_upload_url = $_POST['photo_upload_url'];
        // Account Details
        $bank_name = $_POST['bank_name'];
        $bank_account_name = $_POST['bank_acc_name'];
        $acc_name1 = $_POST['acc_name1'];
        $bank_acc_no = $_POST['bank_acc_no'];
        $bank_branch_name = $_POST['bank_branch_name'];
        $bank_ifsc_code = $_POST['bank_ifsc_code'];

        //Access Details
        $username = $_POST['username'];
        $password = $_POST['password'];

        global $encrypt_decrypt, $secret_key;
        $username = $encrypt_decrypt->fnEncrypt($username, $secret_key);
        $password = $encrypt_decrypt->fnEncrypt($password, $secret_key);

        $financial_year_id = $_SESSION['financial_year_id'];
        $branch_admin_id = $_SESSION['branch_admin_id'];
        $emp_id = $_SESSION['emp_id'];
        $created_at = date("Y-m-d");

        $company_name = addslashes($company_name);
        $address1 = addslashes($address1);
        $address2 = addslashes($address2);
        $company_count = mysqli_num_rows(mysqlQuery("select * from b2b_registration where company_name='$company_name'")); 
        if($company_count>0){
            echo "error--Sorry, The Company has already been taken.";
            exit;
        }
        
        //Agent Code creation
        $agent_company = substr($company_name,0,3);
        $mobile_noa = rtrim($mobile_no);
        $agent_mobile = substr($mobile_noa,-4);
        $agent_code = $agent_company.$agent_mobile;

        //Registration
        $sq_max1 = mysqli_fetch_assoc(mysqlQuery("select max(register_id) as max from b2b_registration"));
        $register_id = $sq_max1['max'] + 1;
        $sq_insert = mysqlQuery("INSERT INTO `b2b_registration`(`register_id`, `emp_id`, `company_name`, `accounting_name`, `iata_status`, `iata_reg_no`, `nature_of_business`, `currency`, `telephone`, `latitude`, `turnover`, `skype_id`, `website`, `cp_first_name`, `cp_last_name`, `mobile_no`, `email_id`, `whatsapp_no`, `designation`, `pan_card`, `id_proof_url`, `address1`, `address2`, `city`, `pincode`, `timezone`, `address_proof_url`, `username`, `password`, `branch_admin_id`, `financial_year_id`, `active_flag`, `approval_status`, `agent_code`, `created_at`, `mail_status`, `approval_date`,`reference`,`company_logo`,`state`,`b_bank_name`, `b_acc_name`, `b_acc_no`, `b_branch_name`, `b_ifsc_code`,`b_bank_account_name`) VALUES ('$register_id','$emp_id','$company_name','$acc_name','$iata_status','$iata_reg','$nature','$currency','$telephone','$latitude','$turnover_slab','$skype_id','$website','$contact_personf','$contact_personl','$mobile_noa','$email_id','$whatsapp_no','$designation','$pan_card','$photo_upload_url','$address1','$address2','$city','$pincode','$timezone','$address_upload_url','$username','$password','$branch_admin_id','$financial_year_id','Active','Approved','$agent_code','$created_at','Sent','$created_at','direct','$company_logo','$state','$bank_name', '$acc_name1', '$bank_acc_no', '$bank_branch_name', '$bank_ifsc_code','$bank_account_name')");

        ///////////////////
        if($sq_insert){
            
            if($company_name != ''){
                $company_count = mysqli_num_rows(mysqlQuery("select * from customer_master where company_name='$company_name' and type not in('Corporate','B2B')"));
            }
            if($company_count>0){
                echo "error--Sorry, The Company has already been taken.";
                exit;
            }
            $mobile_no1 = $encrypt_decrypt->fnEncrypt($mobile_noa, $secret_key);
            //B2B customer creation
            $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(customer_id) as max from customer_master"));
            $customer_id = $sq_max['max'] + 1;
            $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$city'"));
            $email_id1 = $encrypt_decrypt->fnEncrypt($email_id, $secret_key);
            $sq_customer = mysqlQuery("insert into customer_master (customer_id,type,first_name, middle_name, last_name, gender, birth_date, age, contact_no,landline_no, email_id,alt_email,company_name, address, address2, city, active_flag, created_at,service_tax_no,state_id,pan_no, branch_admin_id) values ('$customer_id','B2B', '$contact_personf', '', '$contact_personl', '', '', '', '$mobile_no1','$telephone', '$email_id1','','$company_name', '$address1','$address2','$sq_city[city_name]', 'Active', '$created_at', '','$state','$pan_card','$branch_admin_id')");

            //Ledger Creation
            $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(ledger_id) as max from ledger_master"));
            $ledger_id = $sq_max['max'] + 1;
            $ledger_name = $company_name;
            $sq_ledger = mysqlQuery("insert into ledger_master (ledger_id, ledger_name, alias, group_sub_id, balance, dr_cr,customer_id,user_type) values ('$ledger_id', '$ledger_name', '', '20', '0','Dr','$customer_id','customer')");
            
            //Update customer id
            $sq_b2b = mysqlQuery("update b2b_registration set customer_id='$customer_id' where register_id='$register_id'");

            //Credit Limit insert
            $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2b_creditlimit_master"));
            $entry_id = $sq_max['max'] + 1;
            $sq_credit = mysqlQuery("insert into b2b_creditlimit_master (entry_id,register_id,credit_amount, reason_for_revise, raised_by, credit_type, approval_status, created_at) values ('$entry_id','$register_id', '0', '', '', '', '','$created_at')");
            //Login creation
            $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from b2b_login"));
            $login_id = $sq_max['max'] + 1;
            mysqlQuery("INSERT INTO `b2b_login`(`id`, `user_id`, `agent_flag`, `agent_code`, `username`, `password`, `status`) VALUES ('$login_id','$register_id','1','$agent_code','$username','$password','1')");

            if($sq_ledger && $sq_credit){
                //Send Acknowledgement Mails
                $this->mail_b2blogin_box($username, $password,$agent_code, $email_id,$register_id);
            }
            echo "The information has been successfully saved.";
            exit;
        }
        else{
            echo "error-The information has not been saved.";
            exit;
        }
    }
    
    // Registration Request by b2b customer
    function reg_customer_save(){

        //Basic Details
        $company_name = $_POST['company_name'];
        $acc_name = $_POST['acc_name'];
        $iata_status = $_POST['iata_status'];
        $iata_reg = $_POST['iata_reg'];
        $nature = $_POST['nature'];
        $currency = $_POST['currency'];
        $telephone = $_POST['telephone'];
        $latitude= $_POST['latitude'];
        $turnover_slab = $_POST['turnover_slab'];
        $skype_id = $_POST['skype_id'];
        $website = $_POST['website'];
        //Address Details
        $address1 = $_POST['address1'];
        $address2 = $_POST['address2'];
        $city = $_POST['city'];
        $pincode = $_POST['pincode'];
        $state = $_POST['state'];
        $timezone = $_POST['timezone'];
        $address_upload_url = $_POST['address_upload_url'];
        //Address Details
        $contact_personf = $_POST['contact_personf'];
        $contact_personl = $_POST['contact_personl'];
        $email_id = $_POST['email_id'];
        $mobile_no = $_POST['mobile_no'];
        $whatsapp_no = $_POST['whatsapp_no'];
        $designation = $_POST['designation'];
        $pan_card = $_POST['pan_card'];
        $photo_upload_url = $_POST['photo_upload_url'];
        $company_logo = $_POST['company_logo'];
        //Access Details
        $username = substr($company_name,0,3);
        $password = substr($mobile_no,-4);
        global $encrypt_decrypt,$secret_key;
        $username = $encrypt_decrypt->fnEncrypt($username, $secret_key);
        $password = $encrypt_decrypt->fnEncrypt($password, $secret_key);
        $financial_year_id = $_SESSION['financial_year_id'];
        $branch_admin_id = $_SESSION['branch_admin_id'];
        $emp_id = $_SESSION['emp_id'];
        $created_at = date("Y-m-d");

        $company_name = addslashes($company_name);
        $address1 = addslashes($address1);
        $address2 = addslashes($address2);
        $company_count = mysqli_num_rows(mysqlQuery("select * from b2b_registration where company_name='$company_name'")); 
        if($company_count>0){
        echo "error--Sorry, The Company has already been taken.";
        exit;
        }
        //Registration
        $sq_max1 = mysqli_fetch_assoc(mysqlQuery("select max(register_id) as max from b2b_registration"));
        $register_id = $sq_max1['max'] + 1;
        $sq_insert = mysqlQuery("INSERT INTO `b2b_registration`(`register_id`, `emp_id`, `company_name`, `accounting_name`, `iata_status`, `iata_reg_no`, `nature_of_business`, `currency`, `telephone`, `latitude`, `turnover`, `skype_id`, `website`, `cp_first_name`, `cp_last_name`, `mobile_no`, `email_id`, `whatsapp_no`, `designation`, `pan_card`, `id_proof_url`, `address1`, `address2`, `city`, `pincode`,  `timezone`, `address_proof_url`, `username`, `password`, `branch_admin_id`, `financial_year_id`, `active_flag`, `approval_status`, `agent_code`, `created_at`, `mail_status`, `approval_date`,`company_logo`,`state`) VALUES ('$register_id','$emp_id','$company_name','$acc_name','$iata_status','$iata_reg','$nature','$currency','$telephone','$latitude','$turnover_slab','$skype_id','$website','$contact_personf','$contact_personl','$mobile_no','$email_id','$whatsapp_no','$designation','$pan_card','$photo_upload_url','$address1','$address2','$city','$pincode','$timezone','$address_upload_url','$username','$password','','','Active','','','$created_at','','$created_at','$company_logo','$state')");

        if($sq_insert){    
            //Send Acknowledgement Mails
            $this->acknowledgement_mail_Send($email_id,$company_name);
        
            echo "The Registration form has been successfully sent";
            exit;
        }
        else{
            echo "error-Registration form has not been sent.";
            exit;
        }
    }
    /////////////////////////////////////
    //B2B customer update
    function b2b_customer_update(){
        $register_id = $_POST['register_id'];
        //Basic Details
        $company_old = $_POST['company_old'];
        $company_name = $_POST['company_name'];
        $acc_name = $_POST['acc_name'];
        $iata_status = $_POST['iata_status'];
        $iata_reg = $_POST['iata_reg'];
        $nature = $_POST['nature'];
        $currency = $_POST['currency'];
        $telephone = $_POST['telephone'];
        $latitude= $_POST['latitude'];
        $turnover_slab = $_POST['turnover_slab'];
        $skype_id = $_POST['skype_id'];
        $website = $_POST['website'];
        $company_logo = $_POST['company_logo'];
        //Address Details
        $address1 = $_POST['address1'];
        $address2 = $_POST['address2'];
        $city = $_POST['city'];
        $pincode = $_POST['pincode'];
        // $country = $_POST['country'];
        $state = $_POST['state'];
        $timezone = $_POST['timezone'];
        $address_upload_url = $_POST['address_upload_url'];
        //Address Details
        $contact_personf = $_POST['contact_personf'];
        $contact_personl = $_POST['contact_personl'];
        $email_id = $_POST['email_id'];
        $mobile_no = $_POST['mobile_no'];
        $whatsapp_no = $_POST['whatsapp_no'];
        $designation = $_POST['designation'];
        $pan_card = $_POST['pan_card'];
        $photo_upload_url = $_POST['photo_upload_url'];
        // Account Details
        $bank_name = $_POST['bank_name'];
        $bank_acc_name = $_POST['bank_acc_name'];
        $acc_name1 = $_POST['acc_name1'];
        $bank_acc_no = $_POST['bank_acc_no'];
        $bank_branch_name = $_POST['bank_branch_name'];
        $bank_ifsc_code = $_POST['bank_ifsc_code'];
        //Access Details
        $username = $_POST['username'];
        $password = $_POST['password'];
        $active_flag = $_POST['active_flag'];

        global $encrypt_decrypt, $secret_key;
        $username = $encrypt_decrypt->fnEncrypt($username, $secret_key);
        $password = $encrypt_decrypt->fnEncrypt($password, $secret_key);
        $company_name = addslashes($company_name);
        $company_old = addslashes($company_old);
        $address1 = addslashes($address1);
        $address2 = addslashes($address2);

        //Agent Code creation
        $agent_company = substr($company_name,0,3);
        $agent_mobile = substr($mobile_no,-4);
        $agent_code = $agent_company.$agent_mobile;

        $sq_old = mysqli_fetch_assoc(mysqlQuery("select * from b2b_registration where register_id='$register_id'")); 
        $company_count = mysqli_num_rows(mysqlQuery("select * from b2b_registration where company_name='$company_name' and register_id!='$register_id'"));
        if($company_count>0){
            echo "error--Sorry, The Company has already been taken.";
            exit;
        }
        
        if($company_name != ''){
            $company_count = mysqli_num_rows(mysqlQuery("select * from customer_master where company_name='$company_name' and type not in('Corporate','B2B') and customer_id!='$sq_old[customer_id]'"));
        }
        if($company_count>0){
            echo "error--Sorry, The Company has already been taken.";
            exit;
        }
        $mobile_no1 = $encrypt_decrypt->fnEncrypt($mobile_no, $secret_key);
        //B2B customer updatation
        $sq_cust = mysqli_fetch_assoc(mysqlQuery("select customer_id from customer_master where company_name='$company_old'"));
        $sq_city = mysqli_fetch_assoc(mysqlQuery("select city_name from city_master where city_id='$city'"));
        $email_id1 = $encrypt_decrypt->fnEncrypt($email_id, $secret_key);

        $sq_customer = mysqlQuery("update customer_master set first_name='$contact_personf', last_name='$contact_personl', contact_no='$mobile_no1',landline_no='$telephone', email_id='$email_id1',company_name='$company_name', address='$address1', address2='$address2', city='$sq_city[city_name]',pan_no='$pan_card',state_id='$state' where customer_id='$sq_cust[customer_id]'");

        //Ledger updation
        $ledger_name = $company_name;
        $sq_ledger = mysqlQuery("update ledger_master set ledger_name='$ledger_name' where ledger_name='$company_old' and customer_id='$sq_cust[customer_id]' and user_type='customer'");

        $status = ($active_flag == 'Active') ? 1 : 0;
        mysqlQuery("update `b2b_login` set `username`='$username', `password`='$password', `status`='$status' where agent_flag='1' and user_id='$register_id'");
        if($active_flag == 'Inactive'){

            $sq_user = mysqlQuery("select id from b2b_users where register_id ='$register_id'");
            while($row_user = mysqli_fetch_assoc($$sq_user)){
                mysqlQuery("update `b2b_login` set `status`='0' where agent_flag='0' and user_id='$row_user[id]'");
            }
        }

        ////////////////////////////

        if($sq_ledger){

            //Registration
            $sq_update = mysqlQuery("UPDATE `b2b_registration` SET `company_name`='$company_name',`accounting_name`='$acc_name',`iata_status`='$iata_status',`iata_reg_no`='$iata_reg',`nature_of_business`='$nature',`currency`='$currency',`telephone`='$telephone',`latitude`='$latitude',`turnover`='$turnover_slab',`skype_id`='$skype_id',`website`='$website',`cp_first_name`='$contact_personf',`cp_last_name`='$contact_personl',`mobile_no`='$mobile_no',`email_id`='$email_id',`whatsapp_no`='$whatsapp_no',`designation`='$designation',`pan_card`='$pan_card',`id_proof_url`='$photo_upload_url',`address1`='$address1',`address2`='$address2',`city`='$city',`pincode`='$pincode',`timezone`='$timezone',`address_proof_url`='$address_upload_url',`username`='$username',`password`='$password',`active_flag`='$active_flag',company_logo='$company_logo',agent_code='$agent_code',`state`='$state',`b_bank_name`='$bank_name',`b_acc_name`='$acc_name1',`b_acc_no`='$bank_acc_no',`b_branch_name`= '$bank_branch_name',`b_ifsc_code`='$bank_ifsc_code',`b_bank_account_name`='$bank_acc_name' WHERE register_id='$register_id'");
            if($sq_old['approval_status'] == 'Approved' && $sq_old['username'] != $username || $sq_old['password']!=$password){
                //Send Acknowledgement Mails
                $this->mail_b2blogin_box($username, $password,$agent_code, $email_id,$register_id);
            }
            echo "The information has been successfully updated.";
            exit;
        }
        else{
            echo "error-The information has not been updated";
            exit;
        }
    }

    function finance_save($entry_id,$branch_admin_id){
        $row_spec = 'b2b deposit';
        $deposit = $_POST['deposit'];
        $bank_id = $_POST['bank_id'];
        $payment_date = $_POST['payment_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'];
        $transaction_id = $_POST['transaction_id'];

        $payment_date1 = date('Y-m-d', strtotime($payment_date));
        //Getting cash/Bank Ledger
        if($payment_mode == ''){
            $pay_gl = 20;
            $payment_date1 = date('Y-m-d');
        }
        else{
            if($payment_mode == 'Cash') {  $pay_gl = 20; $type='CASH RECEIPT'; }
            else{ 
                $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
                $pay_gl = $sq_bank['ledger_id'];
                $type='BANK RECEIPT';
            }
        }
        
        global $transaction_master;
        ////////////Basic deposit Amount/////////////
        $module_name = "B2b Deposit";
        $module_entry_id = $entry_id;
        $transaction_id = $transaction_id;
        $payment_amount = $deposit;
        $payment_date = $payment_date1;
        $payment_particular = get_b2b_deposit_particular($bank_id,$deposit);    
        $ledger_particular = get_ledger_particular('By','Cash/Bank');
        $gl_id = 34;
        $payment_side = "Credit";
        $clearance_status = "";
        $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,$type);

        /////////bank////////
        $module_name = "B2b Deposit";
        $module_entry_id = $entry_id;
        $transaction_id = $transaction_id;
        $payment_amount = $deposit;
        $payment_date = $payment_date1;
        $payment_particular = get_b2b_deposit_particular($bank_id,$deposit);
        $ledger_particular = get_ledger_particular('By','Cash/Bank');
        $gl_id = $pay_gl;
        $payment_side = "Debit";
        $clearance_status = "";
        $transaction_master->transaction_save($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular, $gl_id,'', $payment_side, $clearance_status, $row_spec,$branch_admin_id,$ledger_particular,$type);

    }
    public function bank_cash_book_save($register_id,$branch_admin_id)
    {
        global $bank_cash_book_master;
    
        $deposit = $_POST['deposit'];
        $bank_id = $_POST['bank_id'];
        $payment_date = $_POST['payment_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'];
        $transaction_id = $_POST['transaction_id'];

        $payment_date1 = date('Y-m-d', strtotime($payment_date));

        $module_name = "B2b Deposit";
        $module_entry_id = $register_id;
        $payment_date = $payment_date1;
        $payment_amount = $deposit;
        $payment_mode = $payment_mode;
        $bank_name = $bank_name;
        $transaction_id = $transaction_id;
        $bank_id = $bank_id;
        $particular = get_b2b_deposit_particular($bank_id,$deposit);
        $clearance_status = ($payment_mode=="Cheque") ? "Pending" : "";
        $payment_side = "Debit";
        $payment_type = ($payment_mode=="Cash") ? "Cash" : "Bank";
    
        $bank_cash_book_master->bank_cash_book_master_save($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type,'');
        

    }   
    
    function finance_update($entry_id,$branch_admin_id){
        $row_spec = 'b2b deposit';
        $deposit = $_POST['deposit'];
        $bank_id = $_POST['bank_id'];
        $payment_date = $_POST['payment_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'];
        $transaction_id = $_POST['transaction_id'];

        $payment_date1 = date('Y-m-d', strtotime($payment_date));

        //Getting cash/Bank Ledger
        if($payment_mode == 'Cash') {  $pay_gl = 20; }
        else{ 
            $sq_bank = mysqli_fetch_assoc(mysqlQuery("select * from ledger_master where customer_id='$bank_id' and user_type='bank'"));
            $pay_gl = $sq_bank['ledger_id'];
        }
        
        global $transaction_master;

        ////////////Basic deposit Amount/////////////
        $module_name = "B2b Deposit";
        $module_entry_id = $entry_id;
        $transaction_id = $transaction_id;
        $payment_amount = $deposit;
        $payment_date = $payment_date1;
        $payment_particular = get_b2b_deposit_particular($bank_id,$deposit);    
        $ledger_particular = get_ledger_particular('By','Cash/Bank');
        $gl_id = $old_gl_id = 34;
        $payment_side = "Credit";
        $clearance_status = "";
        $transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular,$old_gl_id, $gl_id,'', $payment_side, $clearance_status, $row_spec,$ledger_particular,'INVOICE');

        /////////bank////////
        $module_name = "B2b Deposit";
        $module_entry_id = $entry_id;
        $transaction_id = $transaction_id;
        $payment_amount = $deposit;
        $payment_date = $payment_date1;
        $payment_particular = get_b2b_deposit_particular($bank_id,$deposit);
        $ledger_particular = get_ledger_particular('By','Cash/Bank');
        $gl_id = $old_gl_id = $pay_gl;
        $payment_side = "Debit";
        $clearance_status = "";
        $transaction_master->transaction_update($module_name, $module_entry_id, $transaction_id, $payment_amount, $payment_date, $payment_particular,$old_gl_id, $gl_id,'', $payment_side, $clearance_status, $row_spec,$ledger_particular,'INVOICE');

    }
    public function bank_cash_book_update($register_id,$branch_admin_id)
    {
        global $bank_cash_book_master;
    
        $deposit = $_POST['deposit'];
        $bank_id = $_POST['bank_id'];
        $payment_date = $_POST['payment_date'];
        $payment_mode = $_POST['payment_mode'];
        $bank_name = $_POST['bank_name'];
        $transaction_id = $_POST['transaction_id'];

        $payment_date1 = date('Y-m-d', strtotime($payment_date));

        $module_name = "B2b Deposit";
        $module_entry_id = $register_id;
        $payment_date = $payment_date1;
        $payment_amount = $deposit;
        $payment_mode = $payment_mode;
        $bank_name = $bank_name;
        $transaction_id = $transaction_id;
        $bank_id = $bank_id;
        $particular = get_b2b_deposit_particular($bank_id,$deposit);
        $clearance_status = ($payment_mode=="Cheque") ? "Pending" : "";
        $payment_side = "Debit";
        $payment_type = ($payment_mode=="Cash") ? "Cash" : "Bank";
    
        $bank_cash_book_master->bank_cash_book_master_update($module_name, $module_entry_id, $payment_date, $payment_amount, $payment_mode, $bank_name, $transaction_id, $bank_id, $particular, $clearance_status, $payment_side, $payment_type);
        
    }

    function customer_delete(){	
        $register_id = $_POST['register_id'];

        $sq_b2b = mysqlQuery("delete from b2b_registration where register_id='$register_id'");
        $sq_b2b = mysqlQuery("delete from b2b_creditlimit_master where register_id='$register_id'");

        if(!$sq_b2b){
            echo "error--Sorry The agent not deleted!";
            exit;
        }
        else{
            echo "The agent has been successfully deleted.";
            exit;
        }
    }

    function mail_b2blogin_box($username, $password,$agent_code, $email_id,$register_id){

        global $model,$theme_color,$app_name,$encrypt_decrypt, $secret_key;
        $link = BASE_URL.'Tours_B2B/login.php';

        $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from b2b_registration where register_id='$register_id'"));
        
        $content = '';
        $password = $encrypt_decrypt->fnDecrypt($password, $secret_key);
        $username = $encrypt_decrypt->fnDecrypt($username, $secret_key);
        if($sq_customer['deposite'] != 0){
            $bank_details = ($sq_customer['payment_mode'] == 'Cash')? '':'('.$sq_customer['bank_name'].'--'.$sq_customer['transaction_id'].')';
            $content .= '<tr>
                        <table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
                            <tr>
                                <td colspan=2><span>Thank you for your confirmation with deposit fees of '.$sq_customer['deposite'].' towards '.$app_name.' on '.get_date_user($sq_customer['payment_date']).' by '.$sq_customer['payment_mode'].' '.$bank_details.'</span></td>
                            </tr>
                        </table>
                        </tr>';
        }
        $content .= '
            <tr>
                <table width="85%" cellspacing="0" cellpadding="5" style="color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
                    <tr>
                        <td colspan=2><h3>Your Login Details!</h3></td>
                    </tr>
                    <tr><td style="text-align:left;border: 1px solid #888888;">Agent Code</td>   <td style="text-align:left;border: 1px solid #888888;text-decoration:none !important;color:#888888 !important;">'.$agent_code.'</td></tr>
                    <tr><td style="text-align:left;border: 1px solid #888888;">Username</td>   <td style="text-align:left;border: 1px solid #888888;text-decoration:none !important;color:#888888 !important;">'.$username.'</td></tr>
                    <tr><td style="text-align:left;border: 1px solid #888888;">Password</td>   <td style="text-decoration:none !important;text-align:left;border: 1px solid #888888;color:#888888 !important;">'.$password.'</td></tr>
                    <tr><td style="text-align:left;border: 1px solid #888888;">Login</td>   <td style="text-align:left;border: 1px solid #888888;"><a href="'.$link.'" style="text-decoration: none !important;color: '.$theme_color.'!important;">Click For Login!</a></td></tr>
                </table>
            </tr>';

        $subject = 'B2B Customer Login : '.$app_name;
        $model->app_email_send('105',$sq_customer['company_name'],$email_id, $content,$subject,'');
    }

    function acknowledgement_mail_Send($email_id,$fname){
        
        global $model,$app_name;
        $subject = 'B2B Portal Registration Acknowledgement! : '.$app_name;
        $model->app_email_send('113',$fname,$email_id,'','');
    }

    function rejection_mail_Send($email_id,$name){

        global $model,$app_name;
        $subject = 'B2B Travel Agent Request is declined! : '.$app_name;
        $model->app_email_send('112',$name,$email_id,'','');
    }

   //B2B Protal Profile Save
    function b2b_profile_save(){
        $register_id = $_POST['register_id'];
        $col_data_array = json_decode($_POST['col_data_array']);
        if($col_data_array[0]->form == 'basic_info'){
            $company_name = $col_data_array[0]->company_name;
            $accounting_name = $col_data_array[0]->accounting_name;
            $iata_status = $col_data_array[0]->iata_status;
            $iata_reg_no = $col_data_array[0]->iata_reg_no;
            $nature_of_business = $col_data_array[0]->nature_of_business;
            $currency = $col_data_array[0]->currency;
            $telephone = $col_data_array[0]->telephone;
            $latitude = $col_data_array[0]->latitude;
            $turnover = $col_data_array[0]->turnover;
            $skype_id = $col_data_array[0]->skype_id;
            $website = $col_data_array[0]->website;
            $company_logo = $col_data_array[0]->company_logo;
            $q1="UPDATE `b2b_registration` SET `company_name`='$company_name',`accounting_name`='$accounting_name',`iata_status`='$iata_status',`iata_reg_no`='$iata_reg_no',`nature_of_business`='$nature_of_business',`currency`='$currency',`telephone`='$telephone',`latitude`='$latitude',`turnover`='$turnover',`skype_id`='$skype_id',`website`='$website',company_logo='$company_logo' WHERE register_id='$register_id'";
            $sq_update = mysqlQuery($q1);
            echo "Basic Information updated successfully";
            exit;
        }
        else if($col_data_array[0]->form == 'address_info'){
            $city = $col_data_array[0]->city;
            $address1 = $col_data_array[0]->address1;
            $address2 = $col_data_array[0]->address2;
            $pincode = $col_data_array[0]->pincode;
            $country = $col_data_array[0]->country;
            $timezone = $col_data_array[0]->timezone;
            $address_proof_url = $col_data_array[0]->address_proof_url;

            $q1="UPDATE `b2b_registration` SET `address1`='$address1',`address2`='$address2',`city`='$city',`pincode`='$pincode',`timezone`='$timezone',`address_proof_url`='$address_proof_url',state='$country' WHERE register_id='$register_id'";
            $sq_update = mysqlQuery($q1);
            echo "Address Information updated successfully";
            exit;
        }
        else if($col_data_array[0]->form == 'pcontact_info'){
            $cp_first_name = $col_data_array[0]->cp_first_name;
            $cp_last_name = $col_data_array[0]->cp_last_name;
            $mobile_no = $col_data_array[0]->mobile_no;
            $email_id = $col_data_array[0]->email_id;
            $whatsapp_no = $col_data_array[0]->whatsapp_no;
            $designation = $col_data_array[0]->designation;
            $pan_card = $col_data_array[0]->pan_card;
            $id_proof_url = $col_data_array[0]->id_proof_url;

            $sq_update = mysqlQuery("UPDATE `b2b_registration` SET `cp_first_name`='$cp_first_name',`cp_last_name`='$cp_last_name',`mobile_no`='$mobile_no',`email_id`='$email_id',`whatsapp_no`='$whatsapp_no',`designation`='$designation',`pan_card`='$pan_card',`id_proof_url`='$id_proof_url' WHERE register_id='$register_id'");
            echo "Contact Person Information updated successfully";
            exit;
        }
        else if($col_data_array[0]->form == 'password_info'){
            $username = $col_data_array[0]->username;
            $password = $col_data_array[0]->password;
            global $encrypt_decrypt, $secret_key;
            $username = $encrypt_decrypt->fnEncrypt($username, $secret_key);
            $password = $encrypt_decrypt->fnEncrypt($password, $secret_key);

            $q1="UPDATE `b2b_registration` SET `username`='$username',`password`='$password' WHERE register_id='$register_id'";
            $sq_update = mysqlQuery($q1);
            echo "Password Information updated successfully";
            exit;
        }
        else if($col_data_array[0]->form == 'account_info'){
            $b_bank_name = $col_data_array[0]->b_bank_name;
            $b_acc_name = $col_data_array[0]->b_acc_name;
            $b_acc_no = $col_data_array[0]->b_acc_no;
            $b_branch_name = $col_data_array[0]->b_branch_name;
            $b_ifsc_code = $col_data_array[0]->b_ifsc_code;

            $q1="UPDATE `b2b_registration` SET `b_bank_name`='$b_bank_name',`b_acc_name`='$b_acc_name',`b_acc_no`='$b_acc_no',`b_branch_name`='$b_branch_name',b_ifsc_code='$b_ifsc_code' WHERE register_id='$register_id'";
            $sq_update = mysqlQuery($q1);
            echo "Account Information updated successfully";
            exit;
        }
        else if($col_data_array[0]->form == 'create_user'){

            $full_name = $col_data_array[0]->full_name;
            $email_id = $col_data_array[0]->email_id;
            $mobile_no = $col_data_array[0]->mobile_no;
            $username = $col_data_array[0]->username;
            $password = $col_data_array[0]->password;
            $register_id = $col_data_array[0]->register_id;
            $created_at = date('Y-m-d');
            
            $sq_agent = mysqli_fetch_assoc(mysqlQuery("select agent_code from b2b_registration where register_id='$register_id'"));
            $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from b2b_users"));
            $user_id = $sq_max['max'] + 1;
            
            global $encrypt_decrypt, $secret_key;
            $username = $encrypt_decrypt->fnEncrypt($username, $secret_key);
            $password = $encrypt_decrypt->fnEncrypt($password, $secret_key);

            $sq_update = mysqlQuery("insert into `b2b_users` (`id`,`register_id`,`full_name`,`email_id`,`mobile_no`,`username`,`password`,`agent_code`,`created_at`,`status`) values ('$user_id','$register_id','$full_name','$email_id','$mobile_no','$username','$password','$sq_agent[agent_code]','$created_at','Active')");
            if($sq_update){
                $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(id) as max from b2b_login"));
                $login_id = $sq_max['max'] + 1;
                mysqlQuery("INSERT INTO `b2b_login`(`id`, `user_id`, `agent_flag`, `agent_code`, `username`, `password`, `status`) VALUES ('$login_id','$user_id','0','$sq_agent[agent_code]','$username','$password','1')");
            }
            echo "User Information added successfully";
            exit;
        }
        else if($col_data_array[0]->form == 'create_markup'){

            $markup_in = $col_data_array[0]->markup_in;
            $markup_amount = $col_data_array[0]->markup_amount;
            $tax_in = $col_data_array[0]->tax_in;
            $tax_amount = $col_data_array[0]->tax_amount;
            $register_id = $col_data_array[0]->register_id;

            $sq_update = mysqlQuery("update b2b_registration set markup_in='$markup_in',markup_amount='$markup_amount',tax_in='$tax_in',tax_amount='$tax_amount' where register_id='$register_id'");
            if($sq_update){
                echo "Markup and Tax information saved successfully";
                exit;
            }
        }
    }
    function b2b_profile_update(){
        
        $user_id = $_POST['user_id'];
        $col_data_array = json_decode($_POST['col_data_array']);

        $full_name = $col_data_array[0]->full_name;
        $email_id = $col_data_array[0]->email_id;
        $mobile_no = $col_data_array[0]->mobile_no;
        $username = $col_data_array[0]->username;
        $password = $col_data_array[0]->password;
        $status = $col_data_array[0]->status;
        $login_status = ($status == 'Active') ? '1' : '0';
        global $encrypt_decrypt, $secret_key;
        $username = $encrypt_decrypt->fnEncrypt($username, $secret_key);
        $password = $encrypt_decrypt->fnEncrypt($password, $secret_key);

        $sq_update = mysqlQuery("update b2b_users set full_name='$full_name',email_id='$email_id',mobile_no='$mobile_no',username='$username',password='$password',status='$status' where id='$user_id'");
        if($sq_update){
            $sq_update = mysqlQuery("update b2b_login set username='$username', password='$password',status='$login_status' where user_id='$user_id' and agent_flag='0'");
            echo "User Information updated successfully";
            exit;
        }
    }

    function quotation_save(){
        $unique_timestamp = $_POST['unique_timestamp'];
        $register_id = $_POST['register_id'];
        $pdf_data_array = $_POST['pdf_data_array'];
        $cart_list_arr = $_POST['cart_list_arr'];
        $quotation_currency = $_POST['quotation_currency'];
        $agent_flag = $_SESSION['agent_flag'];
        $user_id = $_SESSION['user_id'];

        $created_at = date("Y-m-d");
        $pdf_data_array = json_encode($pdf_data_array,JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        $cart_list_arr = json_encode($cart_list_arr,JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP|JSON_UNESCAPED_UNICODE);
        $_SESSION['pdf_data_array'] = $pdf_data_array;
        $_SESSION['cart_list_arr'] = $cart_list_arr;

        $timestamp_count = mysqli_num_rows(mysqlQuery("select quotation_id from b2b_quotations where unique_timestamp='$unique_timestamp'"));
        if($timestamp_count>0){
            $sq_update = mysqlQuery("update b2b_quotations set pdf_data_array='$pdf_data_array' where register_id='$register_id' and unique_timestamp='$unique_timestamp'");
        }
        else{
            $sq_max1 = mysqli_fetch_assoc(mysqlQuery("select max(quotation_id) as max from b2b_quotations"));
            $quotation_id = $sq_max1['max'] + 1;
            $sq_insert = mysqlQuery("INSERT INTO `b2b_quotations`(`quotation_id`,`register_id`,`pdf_data_array`,`cart_list_arr`,`created_at`,`unique_timestamp`,`currency`,`agent_flag`,`user_id`)values('$quotation_id','$register_id','$pdf_data_array','$cart_list_arr','$created_at','$unique_timestamp','$quotation_currency','$agent_flag','$user_id')");
        }
    }
    function quotation_delete(){
        $quotation_id = $_POST['quotation_id'];
        $sq_delete = mysqlQuery("update b2b_quotations set status='disabled' where quotation_id='$quotation_id'");

        if($sq_delete){
            echo 'Quotation has been deleted successfully';
            exit;
        }else{
            echo 'error--Quotation has not been deleted!';
            exit;
        }

    }

    function cart_data_update(){

        $user_id = $_SESSION['user_id'];
        $agent_flag = $_SESSION['agent_flag'];
        $cart_list_arr = json_encode($_POST['cart_list_arr']);

        $sq_update = mysqlQuery("UPDATE `b2b_login` SET `cart_data`='$cart_list_arr' WHERE user_id='$user_id' and agent_flag='$agent_flag'");
    }
    function quotation_direct_checkout(){
        
        $quotation_id = $_POST['quotation_id'];
        $user_id = $_SESSION['user_id'];
        $agent_flag = $_SESSION['agent_flag'];

        $sq_quotation = mysqli_fetch_assoc(mysqlQuery("select cart_list_arr from b2b_quotations where quotation_id='$quotation_id'"));
        mysqlQuery("UPDATE `b2b_login` SET `cart_data`='$sq_quotation[cart_list_arr]' WHERE user_id='$user_id' and agent_flag='$agent_flag'");
        echo BASE_URL ."Tours_B2B/checkout_pages/cartPage.php".'=='.$sq_quotation['cart_list_arr'];
    }

}