<?php
include_once('../../model/model.php');
header("Content-type: text/css");

global $theme_color, $theme_color_dark, $theme_color_2, $topbar_color, $sidebar_color;
?>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
.dashboard_panel {
background-color: #fafafa !important;
font-family: 'Poppins', sans-serif !important;
}


.single_enquiry_widget {
background: #000;
border-radius: 5px;
padding: 25px 15px;
color: #fff;
cursor: pointer;
transition: all ease-in-out 0.3s;
}
.single_enquiry_widget:hover {
margin-top: -5px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.blue_enquiry_widget{ background:#6aafff;}

.green_enquiry_widget{ background:#40dbbc;}

.yellow_enquiry_widget{ background:#ffc674;}

.red_enquiry_widget{ background:#ff748c;}

.gray_enquiry_widget {
background:#00000059;
}
.single_enquiry_widget i {
font-size: 26px;
}
span.single_enquiry_widget_amount {
font-size: 30px;
font-weight: 500;
line-height: 28px;
}
.single_enquiry_widget_amount {
padding-top: 10px;
font-size: 14px;
text-align: left;
}
.dashboard-card-icon {
width: 32px;
height: 32px;
background-color: rgba(255,255,255,0.3);
display: flex;
align-items: center;
justify-content: center;
border-radius: 4px;
}
.dashboard-card-icon i {
font-size: 18px;
}
.dashboard_widget {
border: 1px solid #e2e2e2;
border-radius: 5px;
}
.dashboard_widget_title_panel {
border-top-left-radius: 5px;
border-top-right-radius: 5px;
padding: 10px;
cursor: pointer;
}
.dashboard_widget_title_panel.np{
cursor: inherit !important;
}
.widget_red_title {
background: #f178a7;
}
.widget_purp_title {
background: #b083f7;
}
.dashboard_widget_icon {
float: left;
width: 35px;
height: 35px;
text-align: center;
line-height: 45px;
border-radius: 50%;
background: rgba(0, 0, 0, 0.2);
color: #fff;
font-size: 18px;
}
.dashboard_widget_icon i {
line-height: 35px;
}
.dashboard_widget_title_text {
float: left;
width: 80%;
padding-left: 15px;
color: #fff;
}
.dashboard_widget_title_text h3 {
margin: 0;
font-size: 18px;
font-weight: 400;
text-transform: uppercase;
line-height: 35px;
}
.dashboard_widget_title_text p {
margin: 0;
font-size: 13px;
}
.dashboard_widget_conetent_panel {
padding: 15px;
/*text-align: center;*/
background-color: #fff;
border-bottom-left-radius: 5px;
border-bottom-right-radius: 5px;
}
.dashboard_widget_single_conetent{
text-align:center;
}
span.dashboard_widget_conetent_amount {
/*float: left; */
text-align: center;
display: block;
font-size: 20px;
/*margin-bottom: 5px;*/
font-weight: 600;
}
span.dashboard_widget_conetent_text {
border-radius: 5px;
padding: 2px 5px;
font-size: 15px;
width: auto;
margin: 0 auto;
display: inline-block;
}
.widget_blue_text {
Color: #4680ff;
}
.widget_green_text {
Color: #5d900f;
}
.widget_red_text {
Color: #ca0819;
}
.widget_yellow_text {
color: #ecca10;
}
.widget_gray_text {
Color: #00000080;
}
.dashboard_widget_panel {
border-top: 1px dashed #c7c7c7;
padding-top: 25px;
}
.dashboard_widget_panel_first{
border-top: 0;
padding-top: 0;
}

.dashboard_table_panel {
border: 1px solid #e0dfdf;
padding: 5px;
background: #fff;
border-radius: 5px;
}
.dashboard_table_heading.main_block {
padding: 0px 0 9px;
color: #009898;
}
.dashboard_table_heading.main_block h3 {
margin: 0;
font-size: 18px;
padding: 7.5px 0;
text-transform: uppercase;
font-weight: 400;
}
.table_verflow {
<!-- max-height: 226px; -->
overflow-y: scroll;
}
.table_verflow.table_verflow_two{
min-height: 260px;
}
.table_verflow::-webkit-scrollbar {
height: 1px;
width: 2px;
}
.table_verflow::-webkit-scrollbar-track {
background-color: #b3b3b3;
} /* the new scrollbar will have a flat appearance with the set background color */

.table_verflow::-webkit-scrollbar-thumb {
background-color: #ececec;
} /* this will style the thumb, ignoring the track */

.table_verflow::-webkit-scrollbar-button {
background-color: #000;
width:5px;
height: 0px;
} /* optionally, you can style the top and the bottom buttons (left and right for horizontal bars) */

.table_verflow::-webkit-scrollbar-corner {
background-color: black;
border-radius:25px;
}
span.tour_concern {
padding: 7px 0;
display: inline-block;
color: #333 !important;
}
span.tour_concern label {
font-size: 14px;
text-transform: uppercase;
font-weight: 500;
color: #333;
margin-right: 5px;
}
span.tour_concern em {
font-style: normal;
margin-right: 5px;
}


.dashboard_table_body.main_block {
border: 1px solid #e2e2e2;
}
.table_side_widget_panel {
background: #89ff740f;
}
.table_side_widget_content {
padding: 0 20px;
}
.table_side_widget {
text-align: center;
padding: 14px 0;
color: #009898;
}
.table_side_widget_amount {
font-size: 22px;
margin-bottom: 2px;
}
.table_side_widget_text {
font-size: 12px;
}
.table_side_widget_text.widget_blue_text {
background-color: #4680ff;
color: #fff;
padding: 4px 0;
border-radius: 25px;
width: 100px;
margin: 0 auto;
}
.table_side_widget_text.widget_green_text {
background-color: #5d900f;
color: #fff;
padding: 4px 0;
border-radius: 25px;
width: 100px;
margin: 0 auto;
}
.table_side_widget_text.widget_red_text {
background-color: #ca0819;
color: #fff;
padding: 4px 0;
border-radius: 25px;
width: 100px;
margin: 0 auto;
}
.table_side_widget_text.widget_yellow_text {
background-color: #FFC646;
color: #fff;
padding: 4px 0;
border-radius: 25px;
width: 100px;
margin: 0 auto;
}

.table_status{
text-align: center;
margin: 0 !important;
width: 75% !important;
}


span.danger, span.danger, span.success, span.info, span.warning{
padding: 5px 8px;
border-radius: 25px;
width: 80px;
display: block;
text-align: center;
}
span.danger {
background: #f2dede;
}
span.success {
background: #dff0d8;
}
span.info {
background: #d9edf7;
}
span.warning {
background: #fcf8e3;
}


/***Tabs***/
.dashboard_tab {
border-top: 1px dashed #c7c7c7;
padding-top: 25px;
}
.dashboard_tab ul.nav-tabs {
background: #f1f1f1;
border-radius: 50px;
display: inline-block;
margin-bottom: 30px;
border: 1px solid #bbb;
}
.dashboard_tab ul.nav-tabs li {
border-right: 1px solid #bbb;
}
.dashboard_tab ul.nav-tabs li:last-child{
border-right: 0;
}
.dashboard_tab ul.nav-tabs li a {
background: transparent;
text-align: left;
color: #696969 !important;
padding: 10px 15px;
margin: 0;
border: 0 !important;
border-radius: 0;
transition: 0.5s;
font-size: 13px;
font-weight: 500;
text-transform: uppercase;
min-width: 100px;
text-align: center;
}
.dashboard_tab ul.nav-tabs li:first-child a {
border-top-left-radius: 25px;
border-bottom-left-radius: 25px;
}
.dashboard_tab ul.nav-tabs li:last-child a {
border-top-right-radius: 25px;
border-bottom-right-radius: 25px;
}
.dashboard_tab ul.nav-tabs li.active a, .dashboard_tab ul.nav-tabs li a:hover {
background: <?= $theme_color ?>;
color: #fff !important;
}

.dashboard-summary-card {
transition: all ease-in-out 0.3s;
}
.dashboard-summary-card:hover {
margin-top: -5px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
margin-bottom: 5px;
}
.dashboard-summary-card .dashboard_widget_conetent_panel {
background-color: transparent;
}
.dashboard-summary-card .dashboard_widget_conetent_panel.main_block {
color: #fff;
}
.dashboard-sale-card {
background: linear-gradient(-45deg, #eb4d8a, #f178a7);
}
.dashboard-purchase-card {
background: linear-gradient(-45deg, #935cea, #b083f7);
}
.dashboard-summary-card .dashboard_widget_conetent_text {
background-color: rgba(255,255,255,0.3);
padding: 6px;
width: 100%;
cursor: pointer;
transition: all ease-in-out 0.3s;
text-align: center;
}
.dashboard-summary-card .dashboard_widget_conetent_text:hover {
background-color: rgba(255,255,255,0.5);
}
.dashboard-summary-amount {
display: flex;
align-items: center;
margin-bottom: 15px;
}
.dashboard-summary-icon {
width: 35px;
height: 35px;
background-color: rgba(255,255,255,0.3);
display: flex;
align-items: center;
justify-content: center;
border-radius: 50%;
margin-right: 15px;
}


@media screen and (max-width: 1200px) {
.single_enquiry_widget {
padding: 20px 0px;
min-height: 108px;
}
}

@media screen and (max-width:991px) {
    .single_enquiry_widget_amount {
    margin-bottom: 10px;
}
.table>caption+thead>tr:first-child>th,
.table>colgroup+thead>tr:first-child>th,
.table>thead:first-child>tr:first-child>th {
    width: unset !important;
    white-space: nowrap;
}
.form-control{
    width: 100% !important;
}
.dataTables_filter .form-control {
    width: auto !important;
}
}

@media screen and (max-width: 767px) {
.dashboard_widget_conetent_panel.main_block .col-sm-4 {
border-right: 0 !important;
border-bottom: 1px solid #e6e4e5;
margin-bottom: 5px;
padding-bottom: 5px;
}
.dashboard_widget_conetent_panel.main_block .col-sm-4.last_block {
border-right: 0 !important;
border-bottom: 0 !important;
margin-bottom: 0 !important;
padding-bottom: 0 !important;
}
.table_verflow.table_verflow_two, .table_verflow.table_verflow{
min-height: auto;
}
.dashboard_table_panel {
padding: 10px;
}
.dashboard_table_heading.main_block h3 {
font-size: 14px;
padding: 0 0 7px 0;
}
.single_enquiry_widget_amount {
    margin-bottom: 10px;
    clear: both;
}
.single_enquiry_widget {
    margin-bottom: 10px;
}
.dashboard-summary-card .dashboard_widget_conetent_panel.main_block .col-sm-6:not(:last-child) {
    margin-bottom: 20px;
}


}

@media screen and (max-width: 320px) { }