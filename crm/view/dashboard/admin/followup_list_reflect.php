<?php
include_once("../../../model/model.php");
$financial_year_id = $_SESSION['financial_year_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
?>
<div class="dashboard_table dashboard_table_panel main_block">
    <div class="col-md-12 no-pad table_verflow">
    <div class="table-responsive">
        <table class="table table-hover" style="margin: 0 !important;border: 0;">
        <thead>
            <tr class="table-heading-row">
            <th>S_No.</th>
            <th>enquiry_No</th>
            <th>Customer_Name</th>
            <th>Tour_Type</th>
            <th>Tour_Name</th>
            <th>Mobile</th>
            <th>Followup_D/T&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Allocate_To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Followup_Type</th>
            <th>History</th>
            <th>Followup</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $count = 0;
            $rightnow = date('Y-m-d');
            $add7days = date('Y-m-d', strtotime('+7 days'));
            $query = "SELECT * FROM `enquiry_master` where status!='Disabled'";
            $sq_enquiries = mysqlQuery($query);
            while($row = mysqli_fetch_assoc($sq_enquiries)){

            $cust_user_name = '';
            if($row['user_id'] != 0){ 
                $row_user = mysqli_fetch_assoc(mysqlQuery("Select name from customer_users where user_id ='$row[user_id]'"));
                $cust_user_name = ' ('.$row_user['name'].')';
            }
            $date = $row['enquiry_date'];
            $yr = explode("-", $date);
            $year =$yr[0];
            if($from_date==''){
                $sq_fquery = "select * from enquiry_master_entries where entry_id =(select max(entry_id) as entry_id from enquiry_master_entries where enquiry_id='$row[enquiry_id]') and followup_date between '$rightnow' and '$add7days'";
            }
            else{
                $from_date = get_datetime_db($from_date);
                $to_date = get_datetime_db($to_date);
                $sq_fquery = "select * from enquiry_master_entries where entry_id =(select max(entry_id) as entry_id from enquiry_master_entries where enquiry_id='$row[enquiry_id]') and followup_date between '$from_date' and '$to_date'";
            }
            
            $sq3 = mysqlQuery($sq_fquery);
            while ($row_sq_4=mysqli_fetch_assoc($sq3)){
                $count++;
                $assigned_emp_id = $row['assigned_emp_id'];
                $sq_emp = mysqli_fetch_assoc(mysqlQuery("select * from emp_master where emp_id='$assigned_emp_id'"));
                $enquiry_content = $row['enquiry_content'];
                $enquiry_content_arr1 = json_decode($enquiry_content, true);
                foreach($enquiry_content_arr1 as $enquiry_content_arr2){
                    if(isset($enquiry_content_arr2['name'])){
                    if($enquiry_content_arr2['name']=="tour_name"){ $sq_c['tour_name'] = $enquiry_content_arr2['value']; }
                    }
                }
                $enquiry_status1 = mysqli_fetch_assoc(mysqlQuery("select followup_date,followup_reply,followup_status from enquiry_master_entries where enquiry_id='$row[enquiry_id]' order by entry_id DESC"));
                $followup_status=$enquiry_status1['followup_status'];
                if($followup_status != 'Dropped'){

                    $status_count = mysqli_num_rows(mysqlQuery("select * from enquiry_master_entries where enquiry_id='$row[enquiry_id]' and followup_status!='Dropped' "));
                    if($status_count>0){
                        $enquiry_status = mysqli_fetch_assoc(mysqlQuery("select * from enquiry_master_entries where entry_id=(select max(entry_id) from enquiry_master_entries where enquiry_id='$row[enquiry_id]' and followup_status!='Dropped') "));
                        $bg = ($enquiry_status['followup_status']=='Converted') ? "success" : "";
                        $bg = ($enquiry_status['followup_status']=='Dropped') ? "danger" : $bg;
                        $bg = ($enquiry_status['followup_status']=='Active') ? "warning" : $bg;
                    }
                    else{
                        $bg = "";
                    }
                    $status_count1 = mysqli_num_rows(mysqlQuery("select * from enquiry_master_entries where enquiry_id='$row[enquiry_id]' and followup_type='' and followup_status!='Dropped' "));
                    if($status_count1==1){
                        $followup_date1 = $row['followup_date'];
                    }
                    else{
                        $enquiry_status1 = mysqli_fetch_assoc(mysqlQuery("select * from enquiry_master_entries where entry_id=(select max(entry_id) from enquiry_master_entries where enquiry_id='$row[enquiry_id]' and followup_status!='Dropped') "));
                        $followup_date1 = $enquiry_status1['followup_date'];
                    }
                    if($enquiry_status['followup_type']!=''){
                        $status = $enquiry_status['followup_type'];
                        $back_color = 'background: #40dbbc !important';
                    }else{
                        $status = 'Not Done';
                        $back_color = 'background: #ffc674 !important';
                    }
                    if($row['enquiry_type']=='Package Booking' || $row['enquiry_type']=='Group Booking')
                        $tour_name = $sq_c['tour_name'];
                    else 
                        $tour_name = 'NA';
                    ?>
                    <tr class="<?= $bg ?>">
                        <td><?php echo $count; ?></td>
                        <td><?= get_enquiry_id($row['enquiry_id'],$year) ?></td>
                        <td><?php echo $row['name'].$cust_user_name; ?></td>
                        <td><?php echo($row['enquiry_type']) ?></td>
                        <td><?php echo $tour_name; ?></td>
                        <td><?php echo $row['mobile_no']; ?></td>
                        <td><?= get_datetime_user($followup_date1); ?></td>
                        <td><?php echo ($sq_emp['first_name']!='')?$sq_emp['first_name'].' '.$sq_emp['last_name'] : 'Admin'; ?></td>
                        <td><div style='<?=$back_color?>' class="table_side_widget_text widget_blue_text table_status"><?= $status ?></div></td>
                        <td><button class="btn btn-info btn-sm" onclick="display_history('<?php echo $row['enquiry_id']; ?>');" title="Display History" id="history-<?php echo $row['enquiry_id']; ?>"><i class="fa fa-history"></i></button></td>
                        <td><button class="btn btn-info btn-sm" onclick="Followup_update('<?php echo $row['enquiry_id']; ?>');" title="Update Followup" id="update-<?php echo $row['enquiry_id']; ?>" target="_blank"><i class="fa fa-reply-all"></i></button></td>
                    </tr>
                <?php }
                }
            } ?>
        </tbody>
        </table>
    </div> 
    </div>
</div>