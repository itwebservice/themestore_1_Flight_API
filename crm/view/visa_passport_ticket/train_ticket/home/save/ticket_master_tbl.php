 <tr>

     <td><input class="css-checkbox" id="chk_ticket1" type="checkbox"
             onchange="get_auto_values('booking_date','basic_fair','payment_mode','service_charge','markup','save','true','basic','basic');"
             checked><label class="css-label" for="chk_ticket1"> </label></td>

     <td><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control"
             disabled /></td>

     <td><select name="honorific" id="honorific1" title="Honorific">

             <?php get_hnorifi_dropdown(); ?>

         </select></td>

     <td><input type="text" id="first_name1" name="first_name" placeholder="*First Name"
             onchange="fname_validate(this.id)" title="First Name" style="width:120px;" /></td>

     <td><input type="text" id="middle_name1" name="middle_name" onchange="fname_validate(this.id)"
             placeholder="Middle Name" title="Middle Name" style="width:120px;" /></td>

     <td><input type="text" id="last_name1" name="last_name" onchange="fname_validate(this.id)" placeholder="Last Name"
             title="Last Name" style="width:120px;" /></td>

     <td><input type="text" id="birth_date1" name="birth_date" class="app_datepicker" placeholder="DOB" title="DOB"
             onchange="adolescence_reflect(this.id)" value="<?= date('d-m-Y',  strtotime(' -1 day')) ?>"
             style="width:120px;" /></td>

     <td><input type="text" id="adolescence1" name="adolescence" placeholder="*Adolescence" title="Adolescence"
             style="width:135px;" disabled /></td>

     <td><input type="text" id="coach_number1" style="text-transform: uppercase;width:135px;" name="coach_number"
             placeholder="Coach Number" onchange="validate_specialChar(this.id);" title="Coach Number"></td>

     <td><input type="text" id="seat_number1" style="text-transform: uppercase;width:135px;"
             onchange="validate_specialChar(this.id); " name="seat_number" placeholder="Seat Number"
             title="Seat Number"></td>

     <td><input type="text" id="ticket_number1" style="text-transform: uppercase;width:130px;" name="ticket_number"
             placeholder="Ticket Number" onchange="validate_specialChar(this.id);" title="Ticket Number"></td>

 </tr>



 <script>
var date = new Date();
var yest = date.setDate(date.getDate() - 1);

$('#birth_date1').datetimepicker({
    timepicker: false,
    maxDate: yest,
    format: 'd-m-Y'
});
 </script>