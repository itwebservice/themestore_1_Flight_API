<?php
include "../../../model/model.php";
$q = "select branch_status from branch_assign where link='checklist/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>" >
<div class="modal fade" id="entity_save_modal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Checklist</h4>
      </div>
      <div class="modal-body">
        <form id="frm_entity_save">
        <input type="hidden" name="emp_id" id="emp_id" value="<?= $emp_id ?>">
        <div class="row text-enter">
          <div class="col-sm-4 mg_bt_30">
            <select name="entity_for" id="entity_for" title="Select Service" data-toggle="tooltip" onchange="reflect_entity();reflect_destination()" class="form-control">
              <option value="">*Select Service</option>
              <option value="Package Tour">Package Tour</option>
              <option value="Group Tour">Group Tour</option>
              <option value="Hotel Booking">Hotel Booking</option>
              <option value="Flight Booking">Flight Booking</option>
              <option value="Visa Booking">Visa Booking</option>
              <option value="Car Rental Booking">Car Rental Booking</option>
              <option value="Excursion Booking">Activity Booking</option>
              <option value="Train Booking">Train Booking</option>
              <option value="Bus Booking">Bus Booking</option>
              <option value="Miscellaneous Booking">Miscellaneous Booking</option>
            </select>
          </div>
          
           <div id="destination"></div>
        </div>
       
       <div class="panel panel-default panel-body app_panel_style mg_tp_30 feildset-panel">
         <!-- <legend></legend> -->
          <div class="row mg_bt_10">
          <div class="col-md-4 text-right"></div>
          <div class="col-md-4 text-center"><h4>Checklist Entries<h4></div>
          <div class="col-md-4 text-right">
            <button type="button" class="btn btn-excel" title="Add Row" onclick="addRow('tbl_dynamic_tour_name')"><i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-pdf btn-sm" title="Delete Row" onclick="deleteRow('tbl_dynamic_tour_name')"><i class="fa fa-trash"></i></button>
          </div> </div>

          <div class="row"> <div class="col-md-12"> 
        
            <table id="tbl_dynamic_tour_name" name="tbl_dynamic_tour_name" class="table table-bordered table-hover no-marg"  cellspacing="0">
              <tr>
                  <td class="col-md-1"><input id="chk_tour_group1" type="checkbox" checked></td>
                  <td class="col-md-1"><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /></td>
                  <td class="col-md-10"><input placeholder="*Checklist Name" onchange="validate_specialChar(this.id);" id="entity_name" name="entity_name" title="Checklist Name" class="form-control"/></td>
              </tr>                                
            </table>  

          </div> </div>
       </div>
          <div class="row text-center mg_tp_20">
            <div class="col-md-12">
              <button class="btn btn-sm btn-success" id="save_checklist"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
            </div>
          </div>
      </form>
      </div>      
    </div>
  </div>
</div>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>

<script>
$('#entity_save_modal').modal('show');
function reflect_destination(){
  var entity_for = $('#entity_for').val();
  var emp_id = $('#emp_id').val();
  var base_url = $('#base_url').val();
  var branch_status = $('#branch_status').val();
  $.post(base_url+'view/checklist/entities/destination_load.php', { entity_for : entity_for, emp_id : emp_id,branch_status : branch_status }, function(data){
    $('#destination').html(data);
  });
}
function reflect_entity(){
  var entity_for = $('#entity_for').val();
  var emp_id = $('#emp_id').val();
  var base_url = $('#base_url').val();
  var branch_status = $('#branch_status').val();
  if(entity_for != ''){
    $.post(base_url+'view/checklist/entities/entity_load.php', { entity_for : entity_for, emp_id : emp_id,branch_status : branch_status }, function(data){
      var hotel_arr = JSON.parse(data);
      var table = document.getElementById("tbl_dynamic_tour_name");
        if(table.rows.length == 1){
          for(var k=1; k<table.rows.length; k++){
              document.getElementById("tbl_dynamic_tour_name").deleteRow(k);
          }
        }else{
          while(table.rows.length > 1){
              document.getElementById("tbl_dynamic_tour_name").deleteRow(k);
              table.rows.length--;
          }
        }
        if(table.rows.length!=hotel_arr.length){
            for(var j=0; j<hotel_arr.length-1; j++){

              addRow('tbl_dynamic_tour_name');

            }
          }
          for(var i=0; i<hotel_arr.length; i++){
            var row = table.rows[i]; 
            row.cells[2].childNodes[0].value = hotel_arr[i];
            
          }
      });
  }
  }
  function feild_reflect(){
    var entity_for = $('#entity_for').val();
    var emp_id = $('#emp_id').val();
    var base_url = $('#base_url').val();
    var branch_status = $('#branch_status').val();
    $.post(base_url+'view/checklist/entities/tour_load.php', { entity_for : entity_for, emp_id : emp_id,branch_status : branch_status }, function(data){
      $('#div_reflect_tour').html(data);
    });
  }
$(function(){
  $('#frm_entity_save').validate({
    rules:{
      tour_id : { required:true },
      entity_for: { required:true },
      tour_group_id : { required:true },
      booking_id : { required:true },
      dest_name_s :  { required:true },
    },
    submitHandler:function(form){
      $('#save_checklist').prop('disabled',true);
      var base_url = $('#base_url').val();
      var entity_for = $('#entity_for').val();
      var dest_name = $('#dest_name_s').val();

      var entity_name_arr = new Array();
      var table = document.getElementById("tbl_dynamic_tour_name");
      var rowCount = table.rows.length;

      for(var i=0; i<rowCount; i++)
      {
        var row = table.rows[i];
        if(rowCount == 1){
          if(!row.cells[0].childNodes[0].checked){
            error_msg_alert("Atleast one checklist details is required!");
            $('#save_checklist').prop('disabled',false);
            return false;
          }
        }
        if(row.cells[0].childNodes[0].checked)
        {  

          var entity_name = row.cells[2].childNodes[0].value;  
            
          if(entity_name=="")
          {
            error_msg_alert("Enter Checklist Name in Row "+(i+1));
            $('#save_checklist').prop('disabled',false);
            return false;
          }  
          entity_name_arr.push(entity_name);          
        } 
      }
      if(!entity_name_arr.length){
        error_msg_alert("Atleast one checklist details is required!");
        $('#save_checklist').prop('disabled',false);
        return false;
      }
      $('#save_checklist').button('loading');
      $.ajax({
        type:'post',
        url:base_url+'controller/checklist/entities/entity_save.php',
        data:{entity_for : entity_for,dest_name:dest_name,entity_name_arr : entity_name_arr},
        success:function(result){
          var msg = result.split('--');
          if(msg[0] == 'error'){
            error_msg_alert(msg[1]); 
            $('#save_checklist').prop('disabled',false);
            $('#save_checklist').button('reset');
            return false;
          }else{
            success_msg_alert(msg[0]);
            $('#save_checklist').button('reset');
            $('#save_checklist').prop('disabled',false);
            reset_form('frm_entity_save');
            $('#entity_save_modal').modal('hide');
            entities_list_reflect();
            window.reload
          }
        }
      });
    }
  });
});
</script>