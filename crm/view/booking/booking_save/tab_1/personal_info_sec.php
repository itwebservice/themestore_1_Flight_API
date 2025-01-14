<div class="row mg_tp_20">

    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <select name="customer_id" id="customer_id_p" class="app_select2 customer_dropdown" style="width:100%"
            onchange="get_auto_values('txt_date','basic_amount','payment_mode','txt_tour_fee','markup','save');customer_info_load(this.id)"
            title="Customer Name">
            <option value="">Select Customer</option>
            <option value="ncust">New Customer</option>
            <?php

            if ($branch_status == 'yes' && $role != 'Admin') {
                $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' and branch_admin_id='$branch_admin_id' order by customer_id desc");
                while ($row_cust = mysqli_fetch_assoc($sq_query)) {
                    if ($row_cust['type'] == 'Corporate' || $row_cust['type'] == 'B2B') { ?>
            <option value="<?php echo $row_cust['customer_id']; ?>"><?php echo $row_cust['company_name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $row_cust['customer_id']; ?>">
                <?php echo $row_cust['first_name'] . ' ' . $row_cust['last_name']; ?></option>
            <?php
                    }
                }
            } else {
                $sq_query = mysqlQuery("select * from customer_master where active_flag!='Inactive' order by customer_id desc");
                while ($row_cust = mysqli_fetch_assoc($sq_query)) {
                    if ($row_cust['type'] == 'Corporate' || $row_cust['type'] == 'B2B') { ?>
            <option value="<?php echo $row_cust['customer_id']; ?>"><?php echo $row_cust['company_name']; ?></option>
            <?php
                    } else { ?>
            <option value="<?php echo $row_cust['customer_id']; ?>">
                <?php echo $row_cust['first_name'] . ' ' . $row_cust['last_name']; ?></option>
            <?php }
                }
            }
            ?>
        </select>
    </div>

    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <input class="form-control" type="text" id="txt_m_mobile_no" onchange="mobile_validate(this.id)"
            name="txt_m_mobile_no" title="Mobile No" placeholder="Mobile No" readonly />
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <input class="form-control" type="text" id="txt_m_email_id" name="txt_m_email_id" title="Email Id"
            placeholder="Email Id" readonly />
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10_xs">
        <input class="form-control" type="text" id="txt_m_address" name="txt_m_address"
            onchange="validate_address(this.id)" placeholder="Address" title="Address" readonly />
    </div>
    <div class="col-md-3 col-xs-12 mg_tp_10 mg_bt_10_xs">
        <input type="text" id="company_name" class="hidden" name="company_name" title="Company Name"
            placeholder="Company Name" title="Company Name" readonly>
    </div>
    <div class="col-md-3 col-sm-4 col-xs-12 mg_tp_10">
        <input type="text" id="credit_amount" class="hidden" name="credit_amount" placeholder="Credit Note Balance"
            title="Credit Note Balance" readonly>
    </div>
</div>
<div class="row mg_tp_10">
    <div class="col-md-4">
        <input id="copy_details1" name="copy_details1" type="checkbox" onClick="copy_details();">
        &nbsp;&nbsp;<label for="copy_details1">Passenger Details same as above</label>
    </div>
</div>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
$(document).ready(function() {
    $("#txt_m_city, #txt_m_state, #customer_id_p").select2();
});
</script>