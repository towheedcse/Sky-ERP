<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("customer")." ".$this->lang->line("setup");?> </title>
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
               <?php echo $this->lang->line("customer");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("customer");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid --> 
							  
					<div id="alert" class="alert alert-success"></div>
					<form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>customer/AddRecord">
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("customer");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("personal_information");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="institute_id"><?php echo $this->lang->line("company_name");?></label>
												<select name="institute_id" id="institute_id" class="chosen-select" required="">
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
												<select name="branch_id" id="branch_id" class="chosen-select" required="">
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
												<label class="control-label" for="customer_type"><?php echo $this->lang->line("customer_type");?>:</label>
												<select name="customer_type" id="customer_type" class="chosen-select" required="">    
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("customer_type");?></option>
													<option value="1">Corporate</option>
													<option value="2">General</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group">
												<label class="control-label" ><?php echo $this->lang->line("passport_no");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("passport_no");?>" name="passport_no" class="form-control" id="passport_no">
											</div>
										</div>
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" ><?php echo $this->lang->line("pp_validity");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("pp_validity");?>" name="pp_validity" class="form-control" id="pp_validity">
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="issue_date"><?php echo $this->lang->line("issue_date");?></label>
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="issue_date" id="issue_date" required="">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
												
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="expiry_date"><?php echo $this->lang->line("expiry_date");?></label>												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="expiry_date" id="expiry_date" required="">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>												
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="supplier_id"><?php echo $this->lang->line("agency_name");?></label>
												<select name="supplier_id" id="supplier_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("agency_name");?></option>
														<?php foreach($spquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
												<label class="control-label" for="visa_no"><?php echo $this->lang->line("visa_no");?></label>												
												<input type="text" placeholder="<?php echo $this->lang->line("visa_no");?>" name="visa_no" class="form-control" id="visa_no">
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
												<label class="control-label" for="visa_type"><?php echo $this->lang->line("visa_type");?></label>												
												<select name="visa_type" id="visa_type" class="chosen-select" required="">    
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("visa_type");?></option>
													<option value="1">Business Visa</option>
													<option value="2">Tourist Visa</option>
													<option value="3">Work Visa</option>
													<option value="4">Student Visa</option>
													<option value="5">Immigration Visa</option>
												</select>
											</div>
										</div>
										
									</div>
									
									<div class="row">
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
												<label class="control-label" for="experience_year"><?php echo $this->lang->line("experience_year");?></label>												
												<input type="text" placeholder="<?php echo $this->lang->line("experience_year");?>" name="experience_year" class="form-control" id="experience_year">
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="experience_country"><?php echo $this->lang->line("experience_country");?></label>
												
											<input type="text" placeholder="<?php echo $this->lang->line("experience_country");?>" name="experience_country" class="form-control" id="experience_country">
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
												<label class="control-label" for="place_of_birth"><?php echo $this->lang->line("place_of_birth");?></label>												
												<input type="text" placeholder="<?php echo $this->lang->line("place_of_birth");?>" name="place_of_birth" class="form-control" id="place_of_birth">
											</div>
										</div>
										
									</div>
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
										 <div class="form-group">
											<label class="control-label" for="division"><?php echo $this->lang->line("division");?></label>
											<select name="division" id="division" onChange="getDistrictList(this.value,'district','0')" class="chosen-select">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("division");?></option>
										<?php foreach($dvquery->result() as $row){
											echo '<option value="'.$row->division_id.'">'.$row->division_name.'</option>';
										}
										?>
											</select>
										 </div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
											<label class="control-label" for="district"><?php echo $this->lang->line("district");?></label>
											<select name="district" id="district" onChange="getAreaList(this.value,'thana')" class="chosen-select">
    										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("district")." ".$this->lang->line("name");?></option>
    										
    										</select>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
											<label class="control-label" for="thana"><?php echo $this->lang->line("thana");?></label>
											<select name="thana" id="thana" class="chosen-select">
    										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("thana")." ".$this->lang->line("name");?></option>
    										
    										</select>
											</div>
										</div>
										
									</div>
									<div class="row">									
										<div class="col-sm-4 col-md-4 col-lg-4 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("customer")." ".$this->lang->line("photo");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="customer_photo" id="mainupload" class="form-control" style="height: 25px;">
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
										
										<div class="col-sm-4 col-md-4 col-lg-4 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("passport_attach");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="passport_attach" id="passport_attach" class="form-control" style="height: 25px;">
														</span>
														<div style="padding-left:3px; width:4.1%; float:right">
													  <span data-toggle="tooltip" data-placement="top" title="PHP Document">
													  </span>
													  </div>
													</span>
													<input type="text" class="form-control" readonly>
												</div>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("others_attach");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="others_attach" id="others_attach" class="form-control" style="height: 25px;">
														</span>
														<div style="padding-left:3px; width:4.1%; float:right">
													  <span data-toggle="tooltip" data-placement="top" title="PHP Document">
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
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="surname"><?php echo $this->lang->line("surname");?>: </label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("surname");?>" name="surname" class="form-control" id="surname">
											</div>
										</div>
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="given_name"><?php echo $this->lang->line("given_name");?>: </label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("given_name");?>" name="given_name" class="form-control" id="given_name">
											</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="customer_full_name"><?php echo $this->lang->line("customer_full_name");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("customer_full_name");?>" name="customer_full_name" class="form-control" id="customer_full_name">
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
						    
							<div class="row">
							   
								<div class="col-sm-6 col-md-6 col-lg-6">
									<div class="form-group">
										<br>
										<button type="submit" class="btn btn-success save-record"><i class="fa fa-save"></i> Save</button>
										<button type="button" class="btn btn-danger cancel-record"><i class="fa fa-close"></i> Cancel</button>
									</div>
								</div> 
							</div>
							<input type="hidden" name="group_id" id="group_id" value="1">
							<input type="hidden" name="customer_id" id="customer_id" value="">
							<input type="hidden" name="account_type" id="account_type" value="11">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
          
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("customer")." ".$this->lang->line("list");?></h3>
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
        $('#customer_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#customer_id').val();
    $('#customer_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>customer/DelRecord",
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
            url: "<?php echo base_url();?>customer/GetRecord",
            success: function(option){			
			$('#login_id').prop('readonly', false);
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>customer/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            getDistrictList(rsStr[14],"district",rsStr[15]);
            getAreaList(rsStr[15],"thana",rsStr[16]);
            
            $('#customer_id').val(rsStr[0]);
            $('#institute_id').val(rsStr[1]);
            $('#institute_id').trigger("chosen:updated");
            $('#branch_id').val(rsStr[2]);
            $('#branch_id').trigger("chosen:updated");
            $('#customer_type').val(rsStr[3]).trigger("chosen:updated");
            $('#passport_no').val(rsStr[4]);
            $('#pp_validity').val(rsStr[5]);
            $('#issue_date').val(rsStr[6]);
            $('#expiry_date').val(rsStr[7]);
            $('#supplier_id').val(rsStr[8]).trigger("chosen:updated");          
            $('#visa_no').val(rsStr[9]); //$('#photo').val(rsStr[9]);
            $('#visa_type').val(rsStr[10]).trigger("chosen:updated");
            $('#experience_year').val(rsStr[11]);
            $('#experience_country').val(rsStr[12]);
            $('#place_of_birth').val(rsStr[13]);
            $('#division').val(rsStr[14]).trigger("chosen:updated");
            $('#district').val(rsStr[15]).trigger("chosen:updated");
            $('#thana').val(rsStr[16]).trigger("chosen:updated");
            $('#surname').val(rsStr[17]);
            $('#given_name').val(rsStr[18]);
            $('#customer_full_name').val(rsStr[19]);
            $('#fathers_name').val(rsStr[20]);
            $('#mothers_name').val(rsStr[21]);
            $('#spouse_name').val(rsStr[22]);
            $('#present_address').val(rsStr[23]);
            $('#permanent_address').val(rsStr[24]);
            $('#dob').val(rsStr[25]);
            $('#education_qualification').val(rsStr[26]);
            $('#education_qualification').trigger("chosen:updated");
            $('#extra_qualification').val(rsStr[27]);
            $('#marital_status').val(rsStr[28]);
            $('#marital_status').trigger("chosen:updated");
            $('#nationality').val(rsStr[29]).trigger("chosen:updated");
            $('#gender').val(rsStr[30]).trigger("chosen:updated");
            $('#blood_group').val(rsStr[31]).trigger("chosen:updated");
            $('#religion').val(rsStr[32]);
            $('#religion').trigger("chosen:updated");
            $('#phone').val(rsStr[33]);
            $('#mobile').val(rsStr[34]);
            $('#email').val(rsStr[35]);
            
            $('#login_id').val(rsStr[36]);
            $('#password').val(rsStr[37]); $('#confirm_password').val(rsStr[37]); 
			$('#login_id').prop('readonly', true);	
			setTimeout(function() {
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
			}, 50);
			
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
		$('#institute_id').val(1).trigger("chosen:updated");
	    $('#login_id').val("JAFT-");	    
	    $('#nationality').val("Bangladeshi").trigger("chosen:updated");	
	    $('#marital_status').val("Unmarried").trigger("chosen:updated");
	    $('#religion').val("Islam").trigger("chosen:updated");		
	    $('#customer_type').val("2").trigger("chosen:updated");
		
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
          $('#customer_id').val("");
		  location.reload(); 
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>customer/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
    
    function getDistrictList(division_id,placement,district_id=0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>customer/GetAjaxDistrictList",
            data: "division-id="+division_id+"&district-id="+district_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          return false;
    } 
    
    function getAreaList(district_id,placement,area_id=0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>customer/GetAjaxAreaList",
            data: "district-id="+district_id+"&area-id="+area_id,
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
