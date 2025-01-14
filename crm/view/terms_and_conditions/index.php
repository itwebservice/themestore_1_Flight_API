<?php
include "../../model/model.php";
/*======******Header******=======*/
require_once('../layouts/admin_header.php');
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select branch_status from branch_assign where link='terms_and_conditions/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<?= begin_panel('Travel Terms & Conditions',9) ?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >
<div class="row text-right mg_bt_20">
    <div class="col-md-12">
        <button class="btn btn-info btn-sm ico_left" id="btn_save_modal" onclick="save_modal()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Terms & Conditions</button>
    </div>
</div>
<div class="app_panel_content Filter-panel">
    <div class="row">
        <div class="col-md-3">
            <select id="type_filter" name="type_filter" style="width: 100%;" onchange="list_reflect();" title="Select Type">
                <option value="">*Select Type</option>
                <option value="Package Quotation">Package Quotation</option>
                <option value="Group Quotation">Group Quotation</option>
                <option value="Hotel Quotation">Hotel Quotation</option>
                <option value="Car Rental Quotation">Car Rental Quotation</option>
                <option value="Flight Quotation">Flight Quotation</option>
                <option value="Group Sale">Group Sale</option>
                <option value="Package Sale">Package Sale</option>
                <option value="Receipt">Receipt</option>
                <option value="Invoice">Invoice</option>
                <option value="Flight E-Ticket">Flight E-Ticket</option>
                <option value="Package Service Voucher">Package Service Voucher</option>
                <option value="Hotel Service Voucher">Hotel Service Voucher</option>
                <option value="Transport Service Voucher">Transport Service Voucher</option>
                <option value="Activity Service Voucher">Activity Service Voucher</option>
                <option value="B2B Quotation">B2B Quotation</option>
            </select>
        </div>
    </div>
</div>

<div id="div_modal"></div>
<div id="div_list_content" class="main_block"></div>

<script>
$('#type_filter').select2();
function save_modal()
{
    $('#btn_save_modal').button('loading');
    $.post('save_modal.php', {}, function(data){
        $('#btn_save_modal').button('reset');
        $('#div_modal').html(data);
    });
}

function list_reflect()
{
    var type = $('#type_filter').val();
    var branch_status = $('#branch_status').val();
	$.post('list_reflect.php', {type : type, branch_status : branch_status}, function(data){
        $('#div_list_content').html(data);
    });
}
list_reflect();

function update_modal(terms_and_conditions_id)
{
    $('#update_btn-'+terms_and_conditions_id).button('loading');
    $('#update_btn-'+terms_and_conditions_id).prop('disabled',true);
    $.post('update_modal.php', {terms_and_conditions_id : terms_and_conditions_id}, function(data){
        $('#div_modal').html(data);
        $('#update_btn-'+terms_and_conditions_id).button('reset');
        $('#update_btn-'+terms_and_conditions_id).prop('disabled',false);
    });
}
function get_dest_dropdown(service_type){
    
    var service_type = $('#'+service_type).val();
    if(service_type == 'Package Quotation'){
        $.post('get_dest_dropdown.php', {}, function(data){
            $('#dest_html').html(data);
        });
    }else{
        $('#dest_html').html('');
    }
}

</script>

<?= end_panel() ?>
<?php
/*======******Footer******=======*/
require_once('../layouts/admin_footer.php'); 
?>
