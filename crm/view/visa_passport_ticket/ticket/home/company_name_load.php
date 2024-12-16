<?php
include "../../../../model/model.php";
$branch_status = isset($_POST['branch_status']) ? $_POST['branch_status'] : '';
$branch_admin_id = $_SESSION['branch_admin_id'];
$role = $_SESSION['role']; 
$cust_type = isset($_POST['cust_type']) ? $_POST['cust_type'] : ''; 
if($cust_type == 'Corporate' || $cust_type == 'B2B')
{
?>
<select name="company_filter" id="company_filter" class="form-control"  onchange="dynamic_customer_load('cust_type_filter',this.value);" title="Select Company">
	<option value="">Company Name</option>
      <?php 
      	if($role=='Admin') {
                  $sq_query = mysqlQuery("select distinct(company_name) from customer_master where type = '$cust_type' order by company_name");
                  while($row_query=mysqli_fetch_assoc($sq_query)){ ?>
                        <option value="<?php echo $row_query['company_name']; ?>"><?php echo $row_query['company_name']; ?></option>
            <?php }  } 
            else{
                  if($branch_status=='yes' && $role!='Admin'){

                        $sq_query = mysqlQuery("select distinct(company_name) from customer_master where type = '$cust_type' and branch_admin_id='$branch_admin_id' order by company_name");
                        while($row_query=mysqli_fetch_assoc($sq_query)){ ?>
                                    <option value="<?php echo $row_query['company_name']; ?>"><?php echo $row_query['company_name']; ?></option>
                  <?php   } 
                        }
                  elseif($branch_status!='yes') {
                        $sq_query = mysqlQuery("select distinct(company_name) from customer_master where type = '$cust_type' order by company_name");
                        while($row_query=mysqli_fetch_assoc($sq_query)){ ?>
                              <option value="<?php echo $row_query['company_name']; ?>"><?php echo $row_query['company_name']; ?></option>
                  <?php }  }
            }
      ?>
</select>
<?php } 
else
{
	}?>
<script>
$('#company_filter').select2();
</script>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>