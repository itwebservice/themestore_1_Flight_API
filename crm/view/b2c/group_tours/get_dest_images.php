<?php
include "../../../model/model.php";
$package_id = $_POST['package'];
// $sq_package = mysqli_fetch_assoc(mysqlQuery("select dest_id from custom_package_master where package_id = '$package_id'"));
$sq_img = mysqlQuery("select image_url from gallary_master where dest_id = '$package_id'");
?>
<option value="">*Select Image</option>
<?php if($package_id != 0){
    while($row_img = mysqli_fetch_assoc($sq_img)){
      
      $image_url = substr($row_img['image_url'], strrpos($row_img['image_url'], '/') + 1);
      ?>
      <option value="<?php echo $row_img['image_url']; ?>"><?php echo $image_url; ?></option>
  <?php }
} ?>
