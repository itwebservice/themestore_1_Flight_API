<section class="print_sec main_block inv_rece_footer_top">

    <div class="row">

        <div class="col-md-12">

            <h3 class="no-marg font_5 font_s_14">In Words : <?php echo $amount_in_word; ?></h3>

        </div>

    </div>

</section>




<!-- invoice_receipt_footer -->

<section class="print_sec main_block inv_rece_footer_bottom">

    <div class="inv_rece_footer_signature border_block mg_bt_10">

        <div class="row mg_bt_20">

            <div class="col-md-7 border_rt">

                <div class="inv_rece_footer_left">

                    <?php

                    if (isset($sq_terms_cond['terms_and_conditions'])) { ?>

                        <h3 class="no-marg font_5 font_s_14">TERMS & CONDITIONS</h3>

                        <p class="less_opact mg_bt_30"><?= $sq_terms_cond['terms_and_conditions'] ?></p>

                    <?php } ?>

                    <div class="mg_tp_30 text-right">

                        <p class="no-marg font_s_13">RECEIVER SIGNATURE</p>

                    </div>

                </div>

            </div>

            <div class="col-md-5" style="margin-bottom:0;">

                <div class="inv_rece_footer_right" style="margin-bottom:0 !important; padding:0 !important;">



                    <h3 class="no-marg font_5 font_s_14">FOR <?= $app_name ?></h3>

                    <?php

                    if (check_sign()) {

                    ?>

                        <div class="text-right">

                            <?= get_signature() ?>

                        </div>

                    <?php } ?>

                    <br>

                    <div style="text-align:right;">

                        <p class="no-marg font_s_13">AUTHORIZED SIGNATURE</p>

                        <p class="no-marg font_s_13">GENERATED BY : <?= $emp_name ?></p>

                    </div>



                </div>



            </div>

            <br>

            <!-- <div class="signature_block signature_block_r text-right">



                <div class="row" style="margin-right:20px;">

                    <div class="col-md-12"><br>

                    

            </div> -->





        </div>

    </div>



    <!-- invoice_receipt_back_detail -->

    <div class="border_block inv_rece_back_detail">

        <div class="row">

            <div class="col-md-4">

                <p class="border_lt"><span class="font_5">BANK NAME :

                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['bank_name'] != '') ? $sq_bank_branch['bank_name'] : $bank_name_setting ?>

                </p>

            </div>

            <div class="col-md-4">

                <p class="border_lt"><span class="font_5">A/C TYPE :

                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_type'] != '') ? $sq_bank_branch['account_type'] : $acc_name ?></p>

            </div>

            <div class="col-md-4">

                <p class="border_lt"><span class="font_5">BRANCH :

                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['branch_name'] != '') ? $sq_bank_branch['branch_name'] : $bank_branch_name ?>

                </p>

            </div>

            <div class="col-md-4">

                <p class="border_lt no-marg"><span class="font_5">A/C NO :

                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_no'] != '') ? $sq_bank_branch['account_no'] : $bank_acc_no ?>

                </p>

            </div>

            <div class="col-md-4">

                <p class="border_lt no-marg"><span class="font_5">IFSC/SWIFT CODE :

                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['ifsc_code'] != '') ? strtoupper($sq_bank_branch['ifsc_code']) : strtoupper($bank_ifsc_code) ?><?= ($sq_bank_count>0 || $sq_bank_branch['swift_code'] != '') ? '/' . strtoupper($sq_bank_branch['swift_code']) :  strtoupper($bank_swift_code) ?> 

                </p>

            </div>

            <div class="col-md-4">

                <p class="border_lt no-marg"><span class="font_5">BANK ACCOUNT NAME :

                    </span><?= ($sq_bank_count>0 || $sq_bank_branch['account_name'] != '') ? $sq_bank_branch['account_name'] : $bank_account_name ?>

                </p>

            </div>

        </div>

    </div>

</section>

<?php
if($table_name != ''){

    if (mysqli_num_rows(mysqlQuery($values_query)) > 0) {
    ?>
    <div class="col-xs-12 no-pad">
        <div class="table-responsive">
            <table class="table table-hover" id="tbl_list" style="margin: 20px 0 !important;">

                <tr class="table-heading-row">
                    <th>Payment Date</th>
                    <th>Mode</th>
                    <th>Cheque_NO/ID</th>
                    <th>Amount</th>
                </tr>
                    <?php
                    $values_fetch = mysqlQuery($values_query);
                    while ($rows = mysqli_fetch_assoc($values_fetch)) {

                        $credit_charges = isset($rows[$credit_charges]) ? $rows[$credit_charges] : 0;
                        if ($receipt_type == 'Hotel Receipt' || $receipt_type == 'Tour Receipt' || $receipt_type == 'Activity Receipt' || $receipt_type == 'Visa Receipt') {

                            $payment_amount1 = currency_conversion($currency, $currency_code, $rows[$amount_key] + $credit_charges);
                        } else {

                            if ($receipt_type == 'B2B Sale Receipt') {

                                $payment_amount1 = number_format($rows[$amount_key], 2);
                            } else {

                                $payment_amount1 = number_format($rows[$amount_key] + $credit_charges, 2);
                            }
                        }
                    ?>
                    <tr>
                        <td>
                            <center><?= date('d-m-Y', strtotime($rows[$date_key])); ?></center>
                        </td>
                        <td>
                            <center><?= $rows['payment_mode'] ?></center>
                        </td>
                        <td>
                            <center><?php echo ($rows['payment_mode'] == 'Cash' || $rows['transaction_id'] == '') ? 'NA' : $rows['transaction_id']; ?></center>
                        </td>
                        <td>
                            <center><?= $payment_amount1 ?></center>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
<?php }
} ?>

<!-- Bottom_Note -->
<section class="print_sec main_block inv_rece_footer_top">

    <div class="row">

        <div class="col-md-12">

            <h3 class="no-marg font_5 font_s_13 text-center less_opact">This is a Computer generated document and does not require any signature</h3>
        </div>
    </div>

</section>

</body>

</html>