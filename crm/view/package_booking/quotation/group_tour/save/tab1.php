<?php
global $show_entries_switch;
$financial_year_id = $_SESSION['financial_year_id'];
$role_id = $_SESSION['role_id'];
?>
<form id="frm_tab1">
    <input type="hidden" id="branch_admin_id1" name="branch_admin_id1" value="<?= $branch_admin_id ?>">
    <input type="hidden" id="financial_year_id" name="financial_year_id" value="<?= $financial_year_id ?>">

    <div class="row">

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="hidden" id="emp_id" name="emp_id" value="<?= $emp_id ?>">
            <input type="hidden" id="login_id" name="login_id" value="<?= $login_id ?>">
            <input type="hidden" class="form-control" id="quotation_date" name="quotation_date"
                placeholder="Quotation Date" title="Quotation Date" value="<?= date('d-m-Y') ?>">
            <select name="enquiry_id" id="enquiry_id" title="Select Enquiry" style="width:100%"
                onchange="get_enquiry_details()">

                <option value="">*Select Enquiry</option>
                <option value="0"><?= "New Enquiry" ?></option>

                <?php
				if ($role == 'Admin') {
					$sq_enq = mysqlQuery("select * from enquiry_master where enquiry_type in('Group Booking') and status!='Disabled' order by enquiry_id desc");
				} else {
					if ($branch_status == 'yes') {
						if ($role == 'Branch Admin' || $role == 'Accountant' || $role_id > '7') {
							$q = "select * from enquiry_master where enquiry_type in('Group Booking') and status!='Disabled' and branch_admin_id='$branch_admin_id' order by enquiry_id desc";
						} elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                            
                            if($show_entries_switch == 'No'){
                                $q = "select * from enquiry_master where enquiry_type in('Group Booking') and assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";
                            }
                            else{
                                if($role == 'Backoffice'){
                                    $q = "select * from enquiry_master where enquiry_type in('Group Booking') and assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id') and status!='Disabled' order by enquiry_id desc";
                                }else{
                                    $q = "select * from enquiry_master where enquiry_type in('Group Booking') and assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";
                                }
                            }
							$sq_enq = mysqlQuery($q);
						}
					} elseif ($branch_status != 'yes' && ($role == 'Branch Admin' || $role_id == '7' || $role_id > '7')) {

						$sq_enq = mysqlQuery("select * from enquiry_master where enquiry_type in('Group Booking') and status!='Disabled' order by enquiry_id desc");
					} elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
						
                        if($show_entries_switch == 'No'){
                            $q = "select * from enquiry_master where enquiry_type in('Group Booking') and assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";
                        }else{
                            
                            if($role == 'Backoffice'){
                                $q = "select * from enquiry_master where enquiry_type in('Group Booking') and assigned_emp_id in(select emp_id from emp_master where branch_id='$branch_admin_id') and status!='Disabled' order by enquiry_id desc";
                            }else{
                                $q = "select * from enquiry_master where enquiry_type in('Group Booking') and assigned_emp_id='$emp_id' and status!='Disabled' order by enquiry_id desc";
                            }
                        }
						$sq_enq = mysqlQuery($q);
					}
				}
				while ($row_enq = mysqli_fetch_assoc($sq_enq)) {

					$sq_enq1 = mysqli_fetch_assoc(mysqlQuery("SELECT followup_status FROM `enquiry_master_entries` WHERE `enquiry_id` = '$row_enq[enquiry_id]' ORDER BY `entry_id` DESC"));
					if ($sq_enq1['followup_status'] != 'Dropped') {
				?>

                <option value="<?= $row_enq['enquiry_id'] ?>">Enq<?= $row_enq['enquiry_id'] ?> : <?= $row_enq['name'] ?>
                </option>

                <?php }
				} ?>
            </select>

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" id="tour_name" name="tour_name" onchange="validate_spaces(this.id);"
                placeholder="*Tour Name" title="Tour Name">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" id="from_date" name="from_date" placeholder="*From Date" title="From Date"
                onchange="get_to_date(this.id,'to_date');total_days_reflect();">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" id="to_date" name="to_date" placeholder="*To Date" title="To Date"
                onchange="validate_validDate('from_date','to_date');total_days_reflect();">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" id="total_days" name="total_days" placeholder="Total Days" title="Total Days" readonly>

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" id="customer_name" name="customer_name" onchange="fname_validate(this.id)"
                placeholder="*Customer Name" title="Customer Name" required>
            <input type="hidden" id="cust_data" name="cust_data" value='<?= get_customer_hint($branch_status) ?>'>

        </div>

        <div class="col-md-6 col-sm-6 mg_bt_10">
            <div class="col-md-3" style="padding-left:0px;">
                <select class="form-control" style="width:100%" name="country_code" id="country_code" title="Country code">
                    <?= get_country_code(); ?>
                </select>
            </div>
            <div class="col-md-9" style="padding-left:12px;padding-right:0px;">
                <input type="text" class="form-control" id="mobile_no" onchange="mobile_validate(this.id);" name="mobile_no" placeholder="*WhatsApp No" title="WhatsApp No">
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" id="email_id" name="email_id" placeholder="Email ID" title="Email ID">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="number" id="total_adult" name="total_adult" placeholder="*Total Adult(s)" title="Total Adult(s)"
                onchange="total_passangers_calculate(); validate_balance(this.id)">

        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="number" class="form-control" id="children_with_bed" name="children_with_bed"
                onchange="validate_balance(this.id);total_passangers_calculate();cost_reflect();"
                placeholder="Child With Bed(s)" title="Child With Bed(s)">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
            <input type="number" class="form-control" id="children_without_bed" name="children_without_bed"
                onchange="validate_balance(this.id);total_passangers_calculate();" placeholder="Child Without Bed(s)"
                title="Child Without Bed(s)">
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="number" id="total_infant" name="total_infant" placeholder="Total Infant(s)"
                title="Total Infant(s)" onchange="total_passangers_calculate(); validate_balance(this.id)">

        </div>
        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="number" id="single_person" name="single_person" placeholder="Total Single Person"
                title="Total Single Person" onchange="total_passangers_calculate();">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="number" id="total_passangers" name="total_passangers" value="0" placeholder="Total Members"
                title="Total Members" readonly>

        </div>

        <div class="col-sm-1 col-sm-6 col-xs-12 hidden" id="cc">
            <select name="country_code" id="country_code" style="display:none">
                <?= get_country_code() ?>
            </select>
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10 hidden" id="mb">

            <input type="text" class="form-control" id="mobile_no" name="mobile_no" placeholder="Mobile Number"
                title="Mobile Number" style="display:none">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <input type="text" class="form-control" id="quotation_date" name="quotation_date"
                placeholder="Quotation Date" title="Quotation Date" value="<?= date('d-m-Y') ?>">

        </div>

        <div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">

            <select name="booking_type" id="booking_type" title="Tour Type">

                <option value="Domestic">Domestic</option>

                <option value="International">International</option>

            </select>

        </div>

    </div>



    <br><br>



    <div class="row text-center">

        <div class="col-xs-12">

            <button class="btn btn-info btn-sm ico_right">Next&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>

        </div>

    </div>

</form>



<script>
$('#country_code').select2();
// New Customization ----start
$(document).ready(function() {
    let searchParams = new URLSearchParams(window.location.search);
    if (searchParams.get('enquiry_id')) {
        $('#enquiry_id').val(searchParams.get('enquiry_id'));
        $('#enquiry_id').trigger('change');
    }
});
$("#customer_name").autocomplete({
    source: JSON.parse($('#cust_data').val()),
    select: function(event, ui) {
        $("#customer_name").val(ui.item.label);
        $('#mobile_no').val(ui.item.contact_no);
        $('#country_code').val(ui.item.country_id);
		$('#country_code').trigger('change');
        $('#email_id').val(ui.item.email_id);
    },
    open: function(event, ui) {
        $(this).autocomplete("widget").css({
            "width": document.getElementById("customer_name").offsetWidth
        });
    }
}).data("ui-autocomplete")._renderItem = function(ul, item) {
    return $("<li disabled>")
        .append("<a>" + item.label + "</a>")
        .appendTo(ul);
};
// New Customization ----end
function mobile_number() {
    if ($('#enquiry_id').val() == '0') {
        $('#enquiry_id').css('display', 'block');
        $('#country_code').select2();
        $('#mobile_no').css('display', 'block');

    } else {
        $('#enquiry_id').css('display', 'none');
        $('#country_code').select2('destroy');
        $('#mobile_no').css('display', 'none');
    }
}
$('#frm_tab1').validate({

    rules: {
        enquiry_id: {
            required: true
        },
        tour_name: {
            required: true
        },
        from_date: {
            required: true
        },
        to_date: {
            required: true
        },
        total_adult: {
            required: true
        },
        customer_name: {
            required: true
        },
        country_code: {
            required: true
        },
        mobile_no: {
            required: true
        },
    },
    submitHandler: function(form) {
        $('a[href="#tab2"]').tab('show');
    }
});
</script>