<?php
$sq_traveler_personal_info = mysqli_fetch_assoc(mysqlQuery("select * from traveler_personal_info where tourwise_traveler_id='$tourwise_id'"));
?>
<div class="app_panel" style="padding:0px 20px;">
    <div class="container-fluid">
        <div class="app_panel_content no-pad">
            <div class="panel panel-default panel-body app_panel_style feildset-panel mg_tp_20">
                <legend>Customer Details</legend>
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                        <select name="customer_id" id="customer_id1" class="customer_dropdown" title="Customer Name"
                            style="width:100%" onchange="customer_info_load('customer_id1','1')" disabled>
                            <?php
                            $sq_customer = mysqli_fetch_assoc(mysqlQuery("select * from customer_master where customer_id='$tourwise_details[customer_id]'"));
                            if ($sq_customer['type'] == 'Corporate' || $sq_customer['type'] == 'B2B') { ?>
                            <option value="<?php echo $sq_customer['customer_id']; ?>">
                                <?php echo $sq_customer['company_name']; ?></option>
                            <?php } else {
                            ?>
                            <option value="<?= $sq_customer['customer_id'] ?>">
                                <?= $sq_customer['first_name'] . ' ' . $sq_customer['last_name'] ?></option>
                            <?php }  ?>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                        <input class="form-control" type="text" id="txt_m_mobile_no1" name="txt_m_mobile_no"
                            onchange="mobile_validate(this.id);" placeholder="Mobile No." title="Mobile No."
                            maxlength="15" value="<?= $sq_traveler_personal_info['mobile_no'] ?>" readonly />
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                        <input class="form-control" type="text" id="txt_m_email_id1" name="txt_m_email_id"
                            placeholder="Email Id" title="Email Id" maxlength="50"
                            value="<?= $sq_traveler_personal_info['email_id'] ?>" readonly />
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                        <input class="form-control" type="text" id="txt_m_address1" name="txt_m_address"
                            onchange="validate_address(this.id)" readonly placeholder="Address" title="Address"
                            value="<?= $sq_traveler_personal_info['address'] ?>" />
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                        <input type="text" id="company_name1" class="hidden" name="company_name"
                            value="<?= $sq_customer['company_name'] ?>" title="Company Name"
                            placeholder="Company Name" title="Company Name" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    customer_info_load('customer_id1', '1');
    $("#txt_m_city, #txt_m_state, #customer_id1").select2();
});
</script>