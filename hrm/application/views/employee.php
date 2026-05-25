<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("employee")." ".$this->lang->line("setup");?> </title>
    <style type="text/css">
        .btn-file {
            position: relative;
            overflow: hidden;
        }
        .btn-file input[type=file] {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 100%;
            min-height: 100%;
            font-size: 100px;
            text-align: right;
            filter: alpha(opacity=0);
            opacity: 0;
            outline: none;
            background: white;
            cursor: inherit;
            display: block;
        }
        
        .input-group-field {
            display: table-cell;
            vertical-align: middle;
            border-radius:4px;
            min-width:50%;
            white-space: nowrap;
        }
        .input-group-field .form-control {
            border-radius: inherit !important;
        }
        .input-group-field:not(:first-child):not(:last-child) {
            border-radius:0;
        }
        .input-group-field:not(:first-child):not(:last-child) .form-control {
            border-left-width: 0;
            border-right-width: 0;
        }
        .input-group-field:last-child {
            border-top-left-radius:0;
            border-bottom-left-radius:0;
        }
        .btn-sm, .btn-group-sm > .btn {
            padding: 5px 6px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
            margin-bottom: 7px;
        }
        #img-upload{
            width: 100%;
        }    
    </style>
    <?php require('csslinks4admin.php');?>
	
</head>
<body class="hold-transition sidebar-mini">
        
    <!--Delete Modal-->
    <div class="modal fade" id="deleteModal" role="dialog" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("delete_modal_header");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h5><?php echo $this->lang->line("delete_message");?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    <button type="button" id="btnDelete" class="btn btn-danger confirm"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line("delete");?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="wrapper">    
    <?php require('adminheader.php');?>
    <?php require('leftmenu.php');?>
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
              <div class="container-fluid">
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
               <?php echo $this->lang->line("employee");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("employee");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid --> 
							  
					<div id="alert" class="alert alert-success"></div>
					<form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>employee/AddRecord">
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("employee");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("appointment_information");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="institute_id"><?php echo $this->lang->line("company_name");?></label>
												<select name="institute_id" id="institute_id" class="chosen-select" required="" onChange="getBranchList(this.value,'branch_id')">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
													<?php foreach($cquery->result() as $row){
														echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
													}
													?>
												</select>												
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="branch_id"><?php echo $this->lang->line("branch");?></label>
												<select name="branch_id" id="branch_id" class="chosen-select" required="" onChange="GetAjaxFeePeriodList(this.value)">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch");?></option>
													<?php foreach($bquery->result() as $row){
														echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">											
										    <div class="form-group required">
												<label class="control-label" for="department_id"><?php echo $this->lang->line("department");?></label>
												<select name="department_id" id="department_id" class="chosen-select" required="" onChange="GetSectionList(this.value,'section_id')">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("department");?></option>
													<?php foreach($dquery->result() as $row){
														echo '<option value="'.$row->department_id.'">'.$row->department_name.'</option>';
													}
													?>
												</select>
											</div>	
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="section_id"><?php echo $this->lang->line("section");?></label>
												<select name="section_id" id="section_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("section");?></option>
													
												</select>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="appointment_type"><?php echo $this->lang->line("appointment_type");?>:</label>
												<select name="appointment_type" id="appointment_type" class="chosen-select" required="">    
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("appointment_type");?></option>
													<option value="1">Full-time</option>
													<option value="2">Part-time</option>
												</select>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="shift_id"><?php echo $this->lang->line("shift");?></label>
												<select name="shift_id" id="shift_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift");?></option>
													<?php foreach($shquery->result() as $row){
														echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>
									
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="appointment_date"><?php echo $this->lang->line("appointment_date");?></label>
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="appointment_date" id="appointment_date" required="">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
												
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="joining_date"><?php echo $this->lang->line("joining_date");?></label>												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="joining_date" id="joining_date" required="">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>												
											</div>
										</div>
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group">
												<label class="control-label" ><?php echo $this->lang->line("erp_id");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("erp_id");?>" name="employee_code" class="form-control" id="employee_code">
											</div>
										</div>
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" ><?php echo $this->lang->line("employee_id");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("employee_id");?>" name="card_id" class="form-control" id="card_id">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="employee_type"><?php echo $this->lang->line("employee_type");?></label>
												<select name="employee_type" id="employee_type" class="chosen-select" required="">    
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("employee_type");?></option>
													<option value="1">Salesman</option>
													<option value="10">Employee</option>
												</select>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
												<label class="control-label" for="designation"><?php echo $this->lang->line("designation");?></label>												
												<input type="text" placeholder="<?php echo $this->lang->line("designation");?>" name="designation" class="form-control" id="designation" style="padding: 4px 12px 6px 12px;">
											</div>
										</div>										
																			
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label for="weekend" class="control-label"><?php echo $this->lang->line("weekend");?>:</label>
												<select name="weekend[]" id="weekend" class="form-control chosen-select" multiple placeholder="<?php echo $this->lang->line("weekend");?>" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("weekend");?></option>
													<option value="Friday">Friday</option>
													<option value="Saturday">Saturday</option>
													<option value="Sunday">Sunday</option>
													<option value="Monday">Monday</option>
													<option value="Tuesday">Tuesday</option>
													<option value="Wednesday">Wednesday</option>
													<option value="Thursday">Thursday</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
									    <input type="hidden" class="form-control" name="major_subject[]" id="major_subject" value="1">									
										<!--div class="col-sm-8 col-md-8 col-lg-8">
											<div class="form-group required">
												<label for="major_subject" class="control-label"><?php echo $this->lang->line("major_subject");?>:</label>
												<select name="major_subject[]" id="major_subject" class="form-control chosen-select" multiple placeholder="<?php echo $this->lang->line("major_subject");?>">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("major_subject");?></option>
													<?php foreach($squery->result() as $row){
														echo '<option value="'.$row->subject_id.'">'.$row->subject_name.'</option>';
													}
													?>
												</select>
											</div>
										</div-->
										
										<div class="col-sm-12 col-md-12 col-lg-12 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("employee")." ".$this->lang->line("photo");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="employee_photo" id="mainupload" class="form-control" style="height: 25px;">
														</span>
														<div id ="optional_image3" style="padding-left:3px; width:4.1%; float:right">
													  <span data-toggle="tooltip" data-placement="top" title="HxW : 621x1104">
													  <img id ="uploaded_picture3" src="<?php echo SERVER."/".ASSETS."/".IMG;?>/noimg.png?<?php echo date("Y-m-d H:i:s");?>" class="img-responsive" style="height: 40px;"/>
													  </span>
													  </div>
													</span>
													<input type="text" class="form-control" readonly>
												</div>
												
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="login_id"><?php echo $this->lang->line("login_id");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("login_id");?>" name="login_id" class="form-control" id="login_id" required="">
												
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="password"><?php echo $this->lang->line("password");?></label>											
												<input type="password" placeholder="<?php echo $this->lang->line("password");?>" name="password" class="form-control" id="password" required="">
																								
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="confirm_password"><?php echo $this->lang->line("confirm_password");?></label>											
												<input type="password" placeholder="<?php echo $this->lang->line("confirm_password");?>" name="confirm_password" class="form-control" id="confirm_password" required="">
																								
											</div>
										</div>
									</div>
									
								</div>
								
							</div> <!-- End Card -->
							
							<div class="card card-info card-outline">								
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("personal_info");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="employee_name_bn"><?php echo $this->lang->line("employee_name_bn");?>: </label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("employee_name_bn");?>" name="employee_name_bn" class="form-control" id="employee_name_bn">
											</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="employee_name_en"><?php echo $this->lang->line("employee_name_en");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("employee_name_en");?>" name="employee_name_en" class="form-control" id="employee_name_en">
											</div>
										</div>
										
										<div class="col-sm-6 col-md-6 col-lg-6">
												<div class="form-group required">
													<label class="control-label" for="fathers_name"><?php echo $this->lang->line("father_name");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("father_name");?>" name="fathers_name" class="form-control" id="fathers_name">
												</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="mothers_name"><?php echo $this->lang->line("mother_name");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("mother_name");?>" name="mothers_name" class="form-control" id="mothers_name">
											</div>
										</div>
										
										<div class="col-sm-6 col-md-6 col-lg-6">
												<div class="form-group">
													<label for="spouse_name"><?php echo $this->lang->line("spouse_name");?>:</label>
													<input type="text" placeholder="<?php echo $this->lang->line("spouse_name");?>" name="spouse_name" class="form-control" id="spouse_name">
												</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group">
												<label class="control-label" for="dob"><?php echo $this->lang->line("dob");?>:</label>
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="dob" id="dob">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="present_address"><?php echo $this->lang->line("present_address");?>:</label>
												<textarea type="text" rows="1" required="" placeholder="<?php echo $this->lang->line("present_address");?>" name="present_address" class="form-control" id="present_address"></textarea>
											</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="permanent_address"><?php echo $this->lang->line("permanent_address");?>:</label>
												<textarea type="text" rows="1" required="" placeholder="<?php echo $this->lang->line("permanent_address");?>" name="permanent_address" class="form-control" id="permanent_address"></textarea>
											</div>
										</div>										
										
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="education_qualification"><?php echo $this->lang->line("education_qualification");?>:</label>
												<select name="education_qualification" id="education_qualification" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("education_qualification");?></option>
												<?php foreach($qquery->result() as $row){
													echo '<option value="'.$row->qualification_id.'">'.$row->qualification_name.'</option>';
												}
												?>
												</select>
											</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group">
												<label for=""><?php echo $this->lang->line("extra_qualification");?>:</label>
												<input type="text" placeholder="<?php echo $this->lang->line("extra_qualification");?>" name="extra_qualification" class="form-control" id="extra_qualification"/>
											</div>
										</div>
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="nationality"><?php echo $this->lang->line("nationality");?>:</label>
												<select name="nationality" id="nationality" class="chosen-select" required="">    
													<option value="Bangladeshi"><?php echo $this->lang->line("bangladeshi");?></option>
													<option value="Others"><?php echo $this->lang->line("others");?></option>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="gender"><?php echo $this->lang->line("gender");?>:</label>
												<select name="gender" id="gender" class="chosen-select" required="">    
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("gender");?></option>
													<option value="Male"><?php echo $this->lang->line("male");?></option>
													<option value="Female"><?php echo $this->lang->line("female");?></option>
													<option value="Others"><?php echo $this->lang->line("others");?></option>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label for="blood_group"><?php echo $this->lang->line("blood_group");?>:</label>
												<select name="blood_group" id="blood_group" class="chosen-select">    
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("blood_group");?></option>
													<option value="O Positive">O+</option>
													<option value="O Negative">O-</option>
													<option value="A Positive">A+</option>
													<option value="A Negative">A-</option>
													<option value="B Positive">B+</option>
													<option value="B Negative">B-</option>
													<option value="AB Positive">AB+</option>
													<option value="AB Negative">AB-</option>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="marital_status"><?php echo $this->lang->line("marital_status");?>:</label>
												<select name="marital_status" id="marital_status" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("marital_status");?></option>
													<option value="Unmarried"><?php echo $this->lang->line("unmarried");?></option>
													<option value="Married"><?php echo $this->lang->line("married");?></option>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="religion"><?php echo $this->lang->line("religion");?>:</label>
												<select name="religion" id="religion" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("religion");?></option>
													<option value="Islam"><?php echo $this->lang->line("islam");?></option>
													<option value="Hindu"><?php echo $this->lang->line("hindu");?></option>
													<option value="Chirstian"><?php echo $this->lang->line("chirstian");?></option>
													<option value="Buddhist"><?php echo $this->lang->line("buddhist");?></option>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label for="phone"><?php echo $this->lang->line("phone");?>:</label>
												<input type="text" placeholder="<?php echo $this->lang->line("phone");?>" name="phone" class="form-control" id="phone">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="mobile"><?php echo $this->lang->line("mobile");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("mobile");?>" name="mobile" class="form-control" id="mobile">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label for="email"><?php echo $this->lang->line("email");?>:</label>
												<input type="email" placeholder="<?php echo $this->lang->line("email");?>" name="email" class="form-control" id="email">
											</div>
										</div>
									</div>
								</div>
								
							</div> <!-- End Card -->
														
							<div class="card card-info card-outline">								
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("salary_structure");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								    
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="cash_salary"><?php echo $this->lang->line("cash_salary");?>: </label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("cash_salary");?>" name="cash_salary" id="cash_salary" onKeyUp="calFixPayble()" class="form-control">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="tnt_allowance"><?php echo $this->lang->line("tnt_allowance");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("tnt_allowance");?>" name="tnt_allowance" id="tnt_allowance" onKeyUp="calFixPayble()" class="form-control">
											</div>
										</div>										
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="others_payble"><?php echo $this->lang->line("others_payble");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("others_payble");?>" name="others_payble" id="others_payble" onKeyUp="calFixPayble()" class="form-control">
												</div>
										</div>										
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="total_fix_payble"><?php echo $this->lang->line("total_fix_payble");?>:</label>
													<input type="text" required="" readonly placeholder="<?php echo $this->lang->line("total_fix_payble");?>" name="total_fix_payble" id="total_fix_payble" class="form-control">
												</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="basic_salary"><?php echo $this->lang->line("basic_salary");?>: </label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("basic_salary");?>" name="basic_salary" id="basic_salary" onKeyUp="calGrossSalary()" class="form-control">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="house_rent_allowance"><?php echo $this->lang->line("house_rent_allowance");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("house_rent_allowance");?>" name="house_rent_allowance" id="house_rent_allowance" onKeyUp="calGrossSalary()" class="form-control">
											</div>
										</div>										
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="medical_allowance"><?php echo $this->lang->line("medical_allowance");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("medical_allowance");?>" name="medical_allowance" id="medical_allowance" onKeyUp="calGrossSalary()" class="form-control">
												</div>
										</div>										
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="transport_allowance"><?php echo $this->lang->line("conveyance_allowance");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("transport_allowance");?>" name="transport_allowance" id="transport_allowance" onKeyUp="calGrossSalary()" class="form-control">
												    <input type="hidden" placeholder="<?php echo $this->lang->line("communication_allowance");?>" name="communication_allowance" id="communication_allowance" value="0" onKeyUp="calGrossSalary()" class="form-control">
												</div>
										</div>																				
										<div class="col-sm-6 col-md-6 col-lg-6">
												<div class="form-group required">
													<label class="control-label" for="festival_bonus"><?php echo $this->lang->line("festival_bonus")."(% ".$this->lang->line("on_basic_salary").")";?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("festival_bonus");?>" name="festival_bonus" id="festival_bonus" class="form-control">
												</div>
										</div>																				
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="others_allowance"><?php echo $this->lang->line("others_allowance");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("others_allowance");?>" name="others_allowance" id="others_allowance" onKeyUp="calGrossSalary()" class="form-control">
												</div>
										</div>
																				
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="gross_salary"><?php echo $this->lang->line("gross_salary");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("gross_salary");?>" name="gross_salary" id="gross_salary" readonly class="form-control">
												</div>
										</div>
										
																				
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="provident_fund"><?php echo $this->lang->line("provident_fund");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("provident_fund");?>" name="provident_fund" id="provident_fund" onKeyUp="calGrossDeduction()" class="form-control">
												</div>
										</div>																				
										<div class="col-sm-6 col-md-6 col-lg-6">
										    <div class="form-group">
                    							<label class="control-label" for="income_tax"><?php echo $this->lang->line("income_tax")." (".$this->lang->line("on_basic_salary").")";?>:</label>
                                                <div class="row">
            										<div class="col-sm-6 col-md-6 col-lg-6">
            											<input type="text" required="" placeholder="<?php echo $this->lang->line("income_tax");?>(%)" name="income_tax" id="income_tax" class="form-control" onKeyUp="calGrossDeduction()">
            										</div> <!-- input-group -->
            										<div class="col-sm-6 col-md-6 col-lg-6">
            											<input type="text" required="" placeholder="<?php echo $this->lang->line("income_tax");?>(Tk)" name="income_tax_amount" id="income_tax_amount" class="form-control" onKeyUp="calGrossDeduction()">
            										</div>
            									</div>
                    						</div>	
										</div>
																														
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="total_loan_and_adv"><?php echo $this->lang->line("total_loan_and_adv");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("total_loan_and_adv");?>" name="total_loan_and_adv" id="total_loan_and_adv" class="form-control">
												</div>
										</div>
										
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="loan_and_adv"><?php echo $this->lang->line("loan_installment");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("loan_installment");?>" name="loan_and_adv" id="loan_and_adv" onKeyUp="calGrossDeduction()" class="form-control">
												</div>
										</div>
																														
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="loan_total_paid"><?php echo $this->lang->line("loan_total_paid");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("loan_total_paid");?>" name="loan_total_paid" id="loan_total_paid" class="form-control">
												</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="gross_deduction"><?php echo $this->lang->line("gross_deduction");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("gross_deduction");?>" name="gross_deduction" id="gross_deduction" readonly class="form-control">
												</div>
										</div>																				
										<div class="col-sm-3 col-md-3 col-lg-3">
												<div class="form-group required">
													<label class="control-label" for="net_salary"><?php echo $this->lang->line("net_salary");?>:</label>
													<input type="text" required="" placeholder="<?php echo $this->lang->line("net_salary");?>" name="net_salary" id="net_salary" class="form-control">
												</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="pf_achead_mapping"><?php echo $this->lang->line("pf_achead_mapping");?>:</label>
												<select name="pf_achead_mapping" id="pf_achead_mapping" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("pf_achead_mapping");?></option>
												<?php foreach($pfh_account->result() as $row){												
												  if($row->account_details){
												      $pf_account_name = $row->account_name.", ".$row->account_details;
												  }else{
													  $pf_account_name = $row->account_name;
												  }
												  echo '<option value="'.$row->account_id.'">'.$pf_account_name.'</option>';
												}
												?>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="loan_achead_mapping"><?php echo $this->lang->line("loan_achead_mapping");?>:</label>
												<select name="loan_achead_mapping" id="loan_achead_mapping" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("loan_achead_mapping");?></option>
												<?php foreach($lon_account->result() as $row){												
												  if($row->account_details){
												      $loan_account_name = $row->account_name.", ".$row->account_details;
												  }else{
													  $loan_account_name = $row->account_name;
												  }
												  echo '<option value="'.$row->account_id.'">'.$loan_account_name.'</option>';
												}
												?>
												</select>
											</div>
										</div>
										
    									<div class="col-sm-3 col-md-3 col-lg-3">
    										<div class="form-group">
    										    <label class="control-label" for="status">Status</label>
                            					<select name="status" id="status" class="chosen-select">					<option value="">All</option>
                            						<option value="1">Active</option>
                            						<option value="0">Inactive</option>
                            					</select>
    										</div>
    									</div>
									</div>
									
    								<div class="row">
    										<div class="col-sm-6 col-md-6 col-lg-6">
    											<div class="form-group">
    												<br>
    												<button type="submit" class="btn btn-success save-record"><i class="fa fa-save"></i> Save</button>
    												<button type="button" class="btn btn-danger cancel-record"><i class="fa fa-close"></i> Cancel</button>
    											</div>
    										</div>
    										<div class="col-sm-6 col-md-6 col-lg-6">
    											
    										</div>
    								</div>
								</div>
							</div> <!-- End Card -->
							<input type="hidden" name="group_id" id="group_id" value="2">
							<input type="hidden" name="teacher_id" id="teacher_id" value="">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("employee");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
                            <form>
		                <div class="row">
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-company-id"><?php echo $this->lang->line("company_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-company-id" id="src-company-id" class="chosen-select" onChange="getBranchList(this.value,'src-branch-id')">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company_name");?></option>
		                                    <?php foreach($cquery->result() as $row){
		                                        echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
		                                    }
		                                    ?>
		                                </select>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group">
		                            <label class="control-label" for="src-branch-id"><?php echo $this->lang->line("branch_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-branch-id" id="src-branch-id" class="chosen-select">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
		                                    <?php foreach($bquery->result() as $row){
												echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
											}
											?>
		                                </select>
		                            </div>
		                        </div>
		                    </div>
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group">
		                            <label class="control-label" for="src-department-id"><?php echo $this->lang->line("department");?>:</label>
		                            <div id="com_id">
		                                <select name="src-department-id" id="src-department-id" class="chosen-select" onChange="GetSectionList(this.value,'src-section-id')">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("department");?></option>
											<?php foreach($dquery->result() as $row){
												echo '<option value="'.$row->department_id.'">'.$row->department_name.'</option>';
											}
											?>
										</select>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		                <div class="row">
							<div class="col-sm-2 col-md-2 col-lg-2">
								<div class="form-group">
									<label class="control-label" for="src-section-id"><?php echo $this->lang->line("section");?></label>
									<select name="src-section-id" id="src-section-id" class="chosen-select">
										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("section");?></option>
										
									</select>
								</div>
							</div>
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label class="control-label" for="src-shift-id"><?php echo $this->lang->line("shift");?></label>
                                    <select name="src-shift-id" id="src-shift-id" class="chosen-select">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift");?></option>
                                        <?php foreach($shquery->result() as $row){
                                            echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
							
							<div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
									<label class="control-label" ><?php echo $this->lang->line("employee_name");?></label>
									<input type="text" placeholder="<?php echo $this->lang->line("employee_name");?>" name="src-employee-name" class="form-control" id="src-employee-name">
								</div>
                            </div>
							<div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
									<label class="control-label" ><?php echo $this->lang->line("employee_id");?></label>
									<input type="text" placeholder="<?php echo $this->lang->line("employee_id");?>" name="src-card-id" class="form-control" id="src-card-id">
								</div>
                            </div>
		                </div>
												
						<div class="row">
							<div class="col-sm-2 col-md-2 col-lg-2">							 
							 <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>
							</div>							
							<div class="col-sm-2 col-md-2 col-lg-2">							 
							 <button type="reset" id="reset" style="margin-top: 7px;" class="btn btn-block btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
							</div>
						</div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("employee")." ".$this->lang->line("list");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
						    <div id="alert-delete" class="alert alert-danger"></div>
                            <div id="dataGrid"></div>
                        </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
				<?php }?>
                </div><!-- End Container-fluid -->
            </div><!-- End Content -->
        </div><!-- /.content-wrapper --> 
      <?php require('copyright.php');?>
    </div><!-- /.wrapper --> 
    <?php require('jslinks4admin.php');?>
    <script>
    $(function(){
        $('.chosen-select').chosen();
        $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
    });

    function resizeChosen(){
        $(".chosen-container").each(function() {
        $(this).attr('style', 'width: 100%');
        });
    }
    
    $('#addnew').click(function() {
     $(".show-create").show();
    });
    
    jQuery('.datepicker_mask').datetimepicker({
         timepicker:false,
         mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
         format:'d/m/Y'
    });
	jQuery('.datepicker_nomask').datetimepicker({
         timepicker:false,
         mask:false, // '9999/19/39 29:59' - digit is the maximum possible for a cell
         format:'d/m/Y'
    });
	//jQuery('#datetimepicker').datetimepicker();	
	
    $(document).ready( function() {
        reloadDataGrid(); 
        $('.FeesList').hide();
        $(document).on('change', '.btn-file :file', function() {
            var input = $(this),
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [label]);
        });

        $('.btn-file :file').on('fileselect', function(event, label) {
            
            var input = $(this).parents('.input-group').find(':text'),
                log = label;
            
            if( input.length ) {
                input.val(log);
            } else {
                if( log ) alert(log);
            }
        
        });
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    $('#img-upload').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
		$('#alert').hide();
        $("#imgInp").change(function(){
            readURL(this);
        });     
    });
	function isNullAndUndef(variable) {
    	if(variable == null || variable == undefined || variable==""){
		return true;
	}else if(isNaN(variable)){ return true; }	
    }    	
    /*
    $('#InputForm').submit(function(evt) {
                evt.preventDefault();

		var data = new FormData();

		//Form data
		var form_data = $('#InputForm').serializeArray();
		$.each(form_data, function (key, input) {
		    data.append(input.name, input.value);
		});

		//File data
		var file_data = $('input[name="student_photo"]')[0].files;
		for (var i = 0; i < file_data.length; i++) {
		    data.append("student_photo[]", file_data[i]);
		}

		//Custom data
		data.append('key', 'value');

                $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>admission/saveData",
                data:data,
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {                    
			$('#admission_id').val("");
			$('#dataGrid').html(option);
			$('.show-create').hide();
			$('#alert').show();
			$('#alert').html('Successfully saved record!');
                },
                error: function(data) {
		    $('#alert-delete').show();
                    $('#alert-delete').html('Failed to save record. Please try again!!!');
                }
                });
    });
    */
	
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#teacher_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#teacher_id').val();
    $('#teacher_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>employee/DelRecord",
        data: "id="+delId,
        success: function(option){
            $('#alert-delete').show(1000);
            $('#alert-delete').html('Delete successfully!!!');
            reloadDataGrid();
        }//Success
        });// ajax
        return false;
    }
    });// End reset
    /* End Delete Data*/
	
    function reloadDataGrid(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetRecord",
            success: function(option){			
			$('#login_id').prop('readonly', false);
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
	
    function isNullAndUndefined(variable){
		if(variable == null || variable == undefined || variable == ""){
		    return true;
		}else if(isNaN(variable)){
		    return true;
		}
    }
 	$('.search').click(function(){
		var company_id      = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var department_id   = $('#src-department-id').val();
		if(isNullAndUndefined(department_id)){ department_id=0;}
		var section_id      = $('#src-section-id').val();
		if(isNullAndUndefined(section_id)){ section_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var employee_name   = $('#src-employee-name').val();
		var card_id	        = $('#src-card-id').val();
		if(isNullAndUndefined(card_id)){ card_id=0;} //alert(company_id+" "+branch_id);
		if(company_id >0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>employee/GetRecord",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&department-id="+department_id+"&section-id="+section_id+"&shift-id="+shift_id+"&card-id="+card_id+"&employee-name="+employee_name,
            		success: function(option){
                		$('#dataGrid').html(option);
            		}//Success
        	});// ajax
		}else{
		  $('#alert').hide();
		  $('#alert').html('');
		  $('#alert-delete').show();
		  $('#alert-delete').html('Record did not found! Please fill data in required fields');		
	    }
        return false;
    });// End search
    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
		//alert("f-"+frm+",t-"+to+"p-"+pno);
		var company_id      = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var department_id   = $('#src-department-id').val();
		if(isNullAndUndefined(department_id)){ department_id=0;}
		var section_id      = $('#src-section-id').val();
		if(isNullAndUndefined(section_id)){ section_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var card_id	        = $('#src-card-id').val();
		if(isNullAndUndefined(card_id)){ card_id=0;}
		
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>employee/GetRecord",
		    data: "company-id="+company_id+"&branch-id="+branch_id+"&department-id="+department_id+"&section-id="+section_id+"&shift-id="+shift_id+"&card-id="+card_id+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
		    success: function (option) {
		    $('#dataGrid').html(option);
		    }//Success
		});// End datagrid
		return false;
    }
    function calFixPayble(){		
		var cash_salary     		= parseInt($('#cash_salary').val());
        var tnt_allowance		    = parseInt($('#tnt_allowance').val());
        var others_payble   	    = parseInt($('#others_payble').val());
        if(isNullAndUndef(cash_salary)){cash_salary=0;} 
        if(isNullAndUndef(tnt_allowance)){tnt_allowance=0;}
		if(isNullAndUndef(others_payble)){others_payble=0;}
        var total_fix_payble = (cash_salary+tnt_allowance+others_payble);
		
        $('#total_fix_payble').val(total_fix_payble);
        
    }
	function calGrossSalary(){	
	    		
		var cash_salary     		= parseInt($('#cash_salary').val());
        var tnt_allowance		    = parseInt($('#tnt_allowance').val());
        var others_payble   	    = parseInt($('#others_payble').val());
        if(isNullAndUndef(cash_salary)){cash_salary=0;} 
        if(isNullAndUndef(tnt_allowance)){tnt_allowance=0;}
		if(isNullAndUndef(others_payble)){others_payble=0;}
        var total_fix_payble = (cash_salary+tnt_allowance+others_payble);
        $('#total_fix_payble').val(total_fix_payble);
        
		var basic_salary     		= parseInt($('#basic_salary').val());
        var houserent_allowance		= parseInt($('#house_rent_allowance').val());
        var medical_allowance   	= parseInt($('#medical_allowance').val());
        var transport_allowance 	= parseInt($('#transport_allowance').val());
        var communication_allowance = parseInt($('#communication_allowance').val());
        var others_allowance    	= parseInt($('#others_allowance').val());
		if(isNullAndUndef(basic_salary)){basic_salary=0;} if(isNullAndUndef(houserent_allowance)){houserent_allowance=0;}
		if(isNullAndUndef(medical_allowance)){medical_allowance=0;} if(isNullAndUndef(transport_allowance)){transport_allowance=0;}
		if(isNullAndUndef(communication_allowance)){communication_allowance=0;} if(isNullAndUndef(others_allowance)){others_allowance=0;}
		var GrossSalary = (basic_salary+houserent_allowance+medical_allowance+transport_allowance+communication_allowance+others_allowance);
		
		$('#gross_salary').val(GrossSalary);
		var gross_deduction  = parseInt($('#gross_deduction').val()); if(isNullAndUndef(gross_deduction)){gross_deduction=0;}
		if(gross_deduction >0){
			var net_salary = ((GrossSalary+total_fix_payble)-gross_deduction);
			$('#net_salary').val(net_salary);
		}else{
		    $('#gross_deduction').val("0");
		    $('#net_salary').val((GrossSalary+total_fix_payble));
		}
	}
	function calGrossDeduction(){		
		var provident_fund     		= parseInt($('#provident_fund').val());
        var income_tax				= parseInt($('#income_tax').val());	
        var income_tax_amount		= parseInt($('#income_tax_amount').val());	
        var loan_and_adv   			= parseInt($('#loan_and_adv').val());
		if(isNullAndUndef(provident_fund)){provident_fund=0;} if(isNullAndUndef(income_tax)){income_tax=0;} if(isNullAndUndef(loan_and_adv)){loan_and_adv=0;}
		if(income_tax >0){	
		  var basic_salary     		= parseInt($('#basic_salary').val()); 
		  if(isNullAndUndef(basic_salary)){basic_salary=0;}
          var income_tax_amount		= ((basic_salary/100) * income_tax);
		}else{
		  var income_tax_amount		= parseInt($('#income_tax_amount').val());    
		}
		var GrossDeduction 			= (provident_fund+income_tax_amount+loan_and_adv);
		if(isNullAndUndef(GrossDeduction)){GrossDeduction=0;}
		$('#gross_deduction').val(GrossDeduction);
		var total_fix_payble = parseInt($('#total_fix_payble').val());
		if(isNullAndUndef(total_fix_payble)){total_fix_payble=0;}
		
        var gross_salary   			= parseInt($('#gross_salary').val()); if(isNullAndUndef(gross_salary)){gross_salary=0;}
		
		if(gross_salary >0){
			var net_salary = ((gross_salary+total_fix_payble)-GrossDeduction);
			$('#net_salary').val(net_salary);
		}else{
		    $('#gross_deduction').val("0");
		    $('#net_salary').val((gross_salary+total_fix_payble));
		}
	}
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#institute_id').val(rsStr[1]);
            $('#institute_id').trigger("chosen:updated");
            $('#branch_id').val(rsStr[2]);
            $('#branch_id').trigger("chosen:updated");
            $('#department_id').val(rsStr[3]);
            $('#department_id').trigger("chosen:updated");
            GetSectionList(rsStr[3],'section_id',rsStr[4]);
            
            $('#section_id').val(rsStr[4]);
            $('#section_id').trigger("chosen:updated");
            
            $('#appointment_type').val(rsStr[5]).trigger("chosen:updated");
            $('#appointment_date').val(rsStr[6]);
            $('#joining_date').val(rsStr[7]);
            $('#employee_code').val(rsStr[8]);
            $('#employee_type').val(rsStr[9]).trigger("chosen:updated");
            $('#designation').val(rsStr[10]);            
            $('#card_id').val(rsStr[11]); //$('#photo').val(rsStr[9]);
			var major_subject =rsStr[12];
			var majorsubject_array = major_subject.split(',');
			$('#major_subject').val(majorsubject_array).trigger("chosen:updated");
			var weekend = rsStr[13];
			var weekend_array = weekend.split(',');
            $('#weekend').val(weekend_array).trigger("chosen:updated");
            $('#login_id').val(rsStr[14]);
            $('#password').val(rsStr[15]); $('#confirm_password').val(rsStr[13]); 
            $('#employee_name_bn').val(rsStr[16]);
            $('#employee_name_en').val(rsStr[17]);
            $('#fathers_name').val(rsStr[18]);
            $('#mothers_name').val(rsStr[19]);
            $('#spouse_name').val(rsStr[20]);
            $('#dob').val(rsStr[21]);
            $('#present_address').val(rsStr[22]);
            $('#permanent_address').val(rsStr[23]);
            $('#education_qualification').val(rsStr[24]);
            $('#education_qualification').trigger("chosen:updated");
            $('#extra_qualification').val(rsStr[25]);
            $('#nationality').val(rsStr[26]).trigger("chosen:updated");
            $('#gender').val(rsStr[27]).trigger("chosen:updated");
            $('#blood_group').val(rsStr[28]).trigger("chosen:updated");
            $('#marital_status').val(rsStr[29]);
            $('#marital_status').trigger("chosen:updated");
            $('#religion').val(rsStr[30]);
            $('#religion').trigger("chosen:updated");
            $('#phone').val(rsStr[31]);
            $('#mobile').val(rsStr[32]);
            $('#email').val(rsStr[33]);
            $('#cash_salary').val(rsStr[34]);
            $('#tnt_allowance').val(rsStr[35]);
            $('#others_payble').val(rsStr[36]);
            $('#total_fix_payble').val(rsStr[37]);
            $('#basic_salary').val(rsStr[38]);
            $('#house_rent_allowance').val(rsStr[39]);
            $('#medical_allowance').val(rsStr[40]);
            $('#transport_allowance').val(rsStr[41]);
            $('#communication_allowance').val(rsStr[42]);
            $('#festival_bonus').val(rsStr[43]);
            $('#others_allowance').val(rsStr[44]);
            $('#gross_salary').val(rsStr[45]);
            $('#provident_fund').val(rsStr[46]);
            $('#income_tax').val(rsStr[47]);
            $('#income_tax_amount').val(rsStr[48]);
            $('#loan_and_adv').val(rsStr[49]);
            $('#gross_deduction').val(rsStr[50]);
            $('#net_salary').val(rsStr[51]);
            $('#pf_achead_mapping').val(rsStr[52]).trigger("chosen:updated");
            $('#loan_achead_mapping').val(rsStr[53]).trigger("chosen:updated");	
            $('#shift_id').val(rsStr[54]).trigger("chosen:updated");
            $('#total_loan_and_adv').val(rsStr[55]);
            $('#loan_total_paid').val(rsStr[56]);	
            $('#status').val(rsStr[57]).trigger("chosen:updated");
            $('#teacher_id').val(rsStr[0]);
			$('#login_id').prop('readonly', true);							
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
            
            }//Success
        });// ajax
        return false;
    }

    
    $(document).ready(function(){
        //Start Chosen Responsive//
        resizeChosen();	
	    $('#st-danger-alert').hide();
        jQuery(window).on('resize', resizeChosen);
        $(".chosen-select").val('').trigger("chosen:updated");
        //End Chosen Responsive//
		
	    $('#login_id').val("LIRA-");	    
	    $('#nationality').val("Bangladeshi").trigger("chosen:updated");	
	    $('#marital_status').val("Unmarried").trigger("chosen:updated");
	    $('#religion').val("Islam").trigger("chosen:updated");		
	    $('#appointment_type').val("1").trigger("chosen:updated");
	    $('#shift_id').val("1").trigger("chosen:updated");
	    $('#employee_type').val("1").trigger("chosen:updated");
		var majorsubject = "1,2";
		var majorsubject_array = majorsubject.split(',');
		$('#major_subject').val(majorsubject_array).trigger("chosen:updated");
		var weekends = "Friday";
		var weekendArray = weekends.split(',');
		$('#weekend').val(weekendArray).trigger("chosen:updated");		
	    $('#pf_achead_mapping').val("0").trigger("chosen:updated");		
	    $('#loan_achead_mapping').val("0").trigger("chosen:updated");
		
        $('#btnDelete').click(function() {
            $('#deleteModal').modal('hide');
        });
        $('.show-create').hide();
        $('#alert-delete').hide();
        $('#alert').hide();
		
        //Load dataGrid
        reloadDataGrid();
    }); 

    $('.cancel-record').click(function(){
          $('#alert').hide();
          $('#teacher_id').val("");
		  location.reload(); 
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
    function getBranchList(company_id,placement,branch_id=0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>building/GetAjaxBranchList",
            data: "company-id="+company_id+"&branch-id="+branch_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          return false;
    }
    function GetSectionList(department_id,placement,section_id=0){
	    var company_id = $("#company-name").val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetAjaxSectionList",
            data: "company-id="+company_id+"&department-id="+department_id+"&section-id="+section_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success
        
        });// ajax
        return false;
    }
    </script>
</body>
</html>
