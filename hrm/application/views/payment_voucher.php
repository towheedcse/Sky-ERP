<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("payments_voucher");?> </title>
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
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("delete_modal_header");?></h4>
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
	<!--Delete Item Modal-->
	<div class="modal fade" id="deleteItemModal" role="dialog" tabindex="-1" aria-labelledby="confirmIDLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("delete_modal_header");?></h4>
				</div>
				<div class="modal-body">
					<h5><?php echo $this->lang->line("delete_message");?></h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
					<button type="button" id="btnItemDelete" class="btn btn-danger confirm-item"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line("delete");?></button>
				</div>
			</div>
		</div>
	</div>
	<!--Dishonored Modal-->
	<div class="modal fade" id="DishonoredModal" role="dialog" tabindex="-1" aria-labelledby="confirmDishonoredLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> Dishonored Cheque</h4>
				</div>
				<div class="modal-body">
					<h5>Are you sure you want to dishonored this cheque?</h5>
					<form>
					  <div class="form-group">
						<label for="recipient-name" class="col-form-label">Reason:</label>
						<input type="text" class="form-control" id="dishonor-remarks">
					  </div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
					<button type="button" id="btnDishonored" class="btn btn-danger confirm-dishonored"><i class="fa fa-ban-o"></i> Dishonored</button>
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
               <?php echo $this->lang->line("payments_voucher");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("payments_voucher");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("payments_voucher");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
                            <div id="alert" class="alert alert-success"></div>
							<div class="container-fluid">
								
							<form>
									<div class="row">
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="institute_id"><?php echo $this->lang->line("company_name");?></label>
												<div id="s_id">
													<select name="institute_id" id="institute_id" class="chosen-select" required="">
														<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
														<?php foreach($cquery->result() as $row){
															echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
														}
														?>
													</select>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="branch_id"><?php echo $this->lang->line("branch");?></label>
												<div id="s_id">
													<select name="branch_id" id="branch_id" class="chosen-select" required="">
														<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch");?></option>
														<?php foreach($bquery->result() as $row){
															echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
														}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
																
									<div class="row">
									 <div class="col-sm-12 col-md-12 col-lg-12">
										<div class="form-group required">
											<label class="control-label" for="session_id"><?php echo $this->lang->line("session");?></label>
											<select name="session_id" id="session_id" class="chosen-select" required="">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session");?></option>
												<?php foreach($sequery->result() as $row){
													echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
												}
												?>
											</select>
										</div>
									 </div>
								   </div>
								<div class="row">			
									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group required">
											<label class="control-label" for="Payment Mode">Payment Mode:</label>
											<select name="payment-mode" id="payment-mode" class="chosen-select" onChange='showHideBank(this.value)'>
												<option value="1">Cash</option>	
												<option value="2">Cheque</option>	
												<option value="3">Challan</option>		
												<option value="4">bKash</option>	
												<option value="5">Card</option>		
												<option value="10">Others</option>	
											</select>
																
										</div>
									</div>

									<div class="col-sm-6 col-md-6 col-lg-6">
									<div class="form-group required">	
										<label class="control-label" for="Voucher Date">Voucher Date:</label>	
										<div class="input-group">
											<input type="text" class="form-control datepicker_mask" name="created-date" id="created-date">
											<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
										
									</div>
								   </div>
								</div>
								<div class="row bank">			
									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
										<label class="control-label" for="Bank Name">Bank Name:</label>
										<input type="text" name="bank-name" id="bank-name" class="form-control" placeholder="Bank Name"/>
										</div>
									</div>
									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
										<label class="control-label" for="Branch Name">Branch Name:</label>
										<input type="text" name="branch-name" id="branch-name" class="form-control" placeholder="Branch Name"/>
										</div>
									</div>

									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
										<label class="control-label" for="Account Number">Account No:</label>
										<input type="text" name="acc-no" id="acc-no" class="form-control" placeholder="Account Number"/>
										</div>
									</div>

									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
										<label class="control-label" for="Cheque No">Cheque No:</label>
										<input type="text" name="cheque-no" id="cheque-no" class="form-control" placeholder="Cheque No"/>
										</div>
									</div>

									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
										<label class="control-label" for="Cheque Type">Cheque Type:</label>
										<select name="cheque-type" id="cheque-type" class="chosen-select">							
											<option value="">Select Cheque Type</option>	
											<option value="1">Cash Cheque</option>	
											<option value="2">A/C Payee Cheque</option>	
											<option value="3">Bearer Cheque</option>
											<option value="4">Pay Order</option>	
											<option value="5">Bank Transfer</option>		
										</select>
										
										</div>
									</div>
									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group">
										<label class="control-label" for="Issue Date">Issue Date:</label>
										<div class="input-group date form_date" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input2">
											<input type="text" class="form-control" name="issue-date" id="issue-date" placeholder="Issue Date" value="">
											<span class="input-group-addon"><span class="fa fa-calendar"></span></span>
											<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="form-group required">
											
											<div class="pull-left">
											<label class="control-label" for="Dr Account">Dr Account:</label>
											</div>
											<div class="pull-right">
											
											</div>
											
											<select name="dr-account" id="dr-account" class="chosen-select" OnChange="getInvoiceList(this.value,'invoice-no')">
												<option value="">Dr Account</option>
												<?php if($tah_account){?>
												<optgroup label="Customer">
												<?php foreach($tah_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}?>
												</optgroup>
												<?php
												}
												if($sah_account){
												?>
												<optgroup label="Supplier">
												<?php foreach($sah_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}?>
												</optgroup>
												<?php
												}
												if($eah_account){
												?>
												<optgroup label="Employee">
												<?php foreach($eah_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}?>
												</optgroup>
												<?php
												}
												if($exp_account){
												?>
												<optgroup label="Expense">
												<?php foreach($exp_account->result() as $row){												
												  if($row->account_details){
												      $exp_account_name = $row->account_name.", ".$row->account_details;
												  }else{
													  $exp_account_name = $row->account_name;
												  }
												  echo '<option value="'.$row->account_id.'">'.$exp_account_name.'</option>';
												}}
												?>
												</optgroup>
											</select>
										</div>
									</div>
									
								</div>
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="form-group required">
											<div class="pull-left">
													<label class="control-label" for="Cr Account">Cr Account:</label>			
											</div>
											<div class="pull-right" style="float: right;">
													<input type="checkbox" id="others" class="others" value="1"> &nbsp;Others (Payment)
											</div>
											<select name="cr-account" id="cr-account" class="chosen-select">
												<option value="">Cr Account</option>												
												<optgroup label="Cash">
												<?php foreach($cash_account->result() as $row){	echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
												<optgroup label="bKash">
												<?php foreach($bkash_account->result() as $row){	echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
												<optgroup label="Card">
												<?php foreach($card_account->result() as $row){	echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
												<optgroup label="Bank">
												<?php foreach($bank_account->result() as $row){	echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
												<optgroup label="Duties & Taxes">
												<?php foreach($dnt_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
												<optgroup label="Extra Adjustment"> <!--doubtful debts-->
												<?php foreach($pdd_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
											</select>
										</div>
									</div>
									
								</div>

								<div class="row"> <!-- class table-responsive -->
									<table class="table table-condensed table-bordered"> <!-- class table-condensed -->
									   <thead>
										<tr>
											<th class="active">Invoice No*</th>
											<th class="active">Advance</th>
											<th class="active">+ VAT</th> <!-- Incloding -->
											<th class="active">Total Payable</th>
											<th class="active">Total Payment</th>
											<th class="active">Total Due</th>
											<th class="active">Paid Amount*</th>
											<th class="active">Remaining</th>
											<th class="active">Note</th>
										</tr>
									   </thead>
									   <tbody>
										<tr>
											
											<td class="active" id="detail-item1">
											<select name="invoice-no" id="invoice-no" class="chosen-select" OnChange="getAjaxInvoiceInfo(this.value)">
												<option value="0">Select Invoice No</option>
												
											</select>
											</td>
											<td class="active" id="detail-item3">
											<select name="advance-collect" id="advance-collect" class="chosen-select">
												<option value="0">No</option>
												<option value="1">Yes</option>
												
											</select>
											</td>
											<td class="active" id="detail-item4">
											<select name="vat" id="vat" class="chosen-select">
												<option value="0">No</option>
												<option value="1">Yes</option>
												
											</select>
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="Total Payable" name="net-bill" id="net-bill" class="form-control" readonly>
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="Total Payaments" name="paid-amount" id="paid-amount" class="form-control" readonly>
											</td>						    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="Total Due" name="total-due" id="total-due" class="form-control" readonly>
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="Payment Amount" name="payment-amount" id="payment-amount" OnKeyUp="checkReceivedAmount(this.value)" class="form-control">
											 
											</td>					    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="Remaining" name="remaining-amount" id="remaining-amount" class="form-control" readonly>
											
											</td>					    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="Note" name="receive-note" id="receive-note" class="form-control">
											
											</td>										

										</tr>
									   </tbody>
									</table>
								</div>
								
								<div class="row">
								
									<div class="col-sm-11 col-md-11 col-lg-11" style="margin-top:4px">
									<button type="button" id="resetrow" class="btn btn-sm btn-warning pull-right"><span class="glyphicon glyphicon-refresh" > Clear</span></button>
									</div>
									
									<div class="col-sm-1 col-md-1 col-lg-1" style="margin-top:4px">
									<button type="button" class="btn btn-sm btn-info save-detail pull-right"><span class="glyphicon glyphicon-saved"> Add </span></button>
									</div>
								</div>
								<div class="row">				
										<div id="raw-data" class="col-sm-12 col-md-12">
									
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="form-group required">
											<label class="control-label" for="Total Paid Amount">Total Paid Amount:</label>				
											<input type="text" required="" placeholder="Total Paid Amount" name="total-received-amount" id="total-received-amount" class="form-control">
											
										</div>
									</div>
									
								</div>
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="form-group required">
											<label class="control-label" for="Naration">Naration:</label>
											<textarea type="text" required="" placeholder="Naration" name="naration" id="naration" class="form-control"></textarea>
										</div>
									</div>
									
								</div>			
								<div class="row">					
									<div class="col-sm-12 col-md-12 col-lg-12">				
										<button type="reset" id="reset" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-refresh"> Clear </span></button>		
										<button type="button" class="btn btn-sm btn-success save-voucher"><span class="glyphicon glyphicon-saved"> Save </span></button>
									</div>
								</div>				
								<input type="hidden" class="form-control" id="voucher-type" value="1">
								<input type="hidden" class="form-control" id="contra-id" value="0">
								<input type="hidden" class="form-control" id="details-id" value="0">
								<input type="hidden" class="form-control" id="voucher-no" value="">
								<input type="hidden" class="form-control" id="v-status" value="0"/>
								<input type="hidden" class="form-control" id="del-account" value="0">
							</form>
							</div><!-- End Container Fluid-->
                    </div> <!-- End Card Body-->
                </div> <!-- End Card --> 
                               
                    
            </div> <!-- End Card --> 
          
            <?php if($hasViewOption){?>
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("payments_voucher");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
								<div class="row">
									<div class="col-sm-4 col-md-4 col-lg-4">
										<div class="form-group required">
											<label class="control-label" for="src-institute_id"><?php echo $this->lang->line("company_name");?></label>
											<div id="s_id">
												<select name="src-institute_id" id="src-institute_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
													<?php foreach($cquery->result() as $row){
														echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-md-4 col-lg-4">
										<div class="form-group required">
											<label class="control-label" for="src-branch_id"><?php echo $this->lang->line("branch");?></label>
											<div id="s_id">
												<select name="src-branch_id" id="src-branch_id" class="chosen-select" required="" onChange="GetSrcFeePeriodList(this.value)">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch");?></option>
													<?php foreach($bquery->result() as $row){
														echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>									
									<div class="col-sm-4 col-md-4 col-lg-4">
									<div class="form-group required">
										<label class="control-label" for="src-session_id"><?php echo $this->lang->line("session");?></label>
										<select name="src-session_id" id="src-session_id" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session");?></option>
											<?php foreach($sequery->result() as $row){
												echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
											}
											?>
										</select>
									</div>
								 </div>
								</div>
															
								<div class="row">
								 
								  <div class="col-sm-4 col-md-4 col-lg-4">                                    
									<div class="form-group required">	
										<label class="control-label" for="src-date-from"><?php echo $this->lang->line("from_date");?></label>						
										
										<div class="input-group">
										<input type="text" class="form-control datepicker_nomask" name="src-date-from" id="src-date-from">
										<div class="input-group-prepend">
										  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
									</div>									
								  </div>
								  <div class="col-sm-4 col-md-4 col-lg-4">                                    
									<div class="form-group required">	
										<label class="control-label" for="src-date-to"><?php echo $this->lang->line("to_date");?></label>						
										
										<div class="input-group">
										<input type="text" class="form-control datepicker_nomask" name="src-date-to" id="src-date-to">
										<div class="input-group-prepend">
										  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
									</div>									
								  </div>
									
								  <div class="col-sm-4 col-md-4 col-lg-4">
									<div class="form-group required">
										<label class="control-label" for="src-customer_id"><?php echo $this->lang->line("account")." ".$this->lang->line("name");?></label>
										<select name="src-customer_id" id="src-customer_id" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("account");?></option>
											<?php 										
												if($sah_account){
												?>
												<optgroup label="Supplier">
												<?php foreach($sah_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}?>
												</optgroup>
												<?php
												}
												if($ex_account){
												?>
												<optgroup label="Expense">
												<?php foreach($ex_account->result() as $row){ echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}?>
												</optgroup>
												<?php
												}
												?>												
												<optgroup label="Customer">												
												<?php foreach($squery->result() as $row){
													echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
												</optgroup>
											
										</select>
									</div>
								</div>

								
							</div>
							<div class="row">								
								<div class="form-group text-right">
								<button type="button" class="btn btn-md btn-info search" style="margin-top:5px"><span class="glyphicon glyphicon-search"> Search</span></button>
								<?php if($hasPrintOption){?>
								<button type="button" onclick="PrintElem('#dataGrid')" class="btn btn-md btn-success print" style="margin-top:5px"><span class="glyphicon glyphicon-print"> Print</span></button>
								<?php }?>
								</div>						
							</div>
					    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("payments_voucher")." ".$this->lang->line("list");?></h3>
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
	$('#btnDelete').click(function() {
		$('#deleteModal').modal('hide');
	});
	$('#btnItemDelete').click(function() {
		$('#deleteItemModal').modal('hide');
	});
	
	$('#btnDishonored').click(function() {
		$('#DishonoredModal').modal('hide');
	});
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
        resizeChosen();
        jQuery(window).on('resize', resizeChosen);
		resizeGridChosen1();
        jQuery(window).on('resize', resizeGridChosen1);
		resizeGridChosen2();
        jQuery(window).on('resize', resizeGridChosen2);	
		resizeGridChosen3();
        jQuery(window).on('resize', resizeGridChosen3);		
		resizeGridChosen4();
        jQuery(window).on('resize', resizeGridChosen4);	
		$('#alert').hide();
		$('.alert-danger').hide();
		$(".show-create").hide();
		showHideBank(1);
		//==Load dataGrid==
		reloadDraftDataGrid();
		reloadDataGrid();
		//==== Start Temp Set =====		
	    $('#institute_id').val("1").trigger("chosen:updated");
	    $('#branch_id').val("1").trigger("chosen:updated");
	    $('#cr-account').val("16").trigger("chosen:updated");
    });

    function resizeChosen() {
        $(".chosen-container").each(function() {
            $(this).attr('style', 'width: 100%');
        });
    }
    function resizeGridChosen1() {
        $("#detail-item1 .chosen-container").each(function() {
            $(this).attr('style', 'width: 155px');
        });
    }
    function resizeGridChosen2() {
        $("#detail-item2 .chosen-container").each(function() {
            $(this).attr('style', 'width: 21%');
        });
    }
    function resizeGridChosen3() {
        $("#detail-item3 .chosen-container").each(function() {
            $(this).attr('style', 'width: 60px');
        });
    }
    function resizeGridChosen4() {
        $("#detail-item4 .chosen-container").each(function() {
            $(this).attr('style', 'width: 60px');
        });
    }
	function showHideBank(payment_mode){
		if(payment_mode =="2"){
			$('.bank').show();
		}else{
			$('#bank-name').val("");
			$('#branch-name').val("");
			$('#check-no').val("");
			$('#issue-date').val("");
			$('.bank').hide();
		}
    }
	
	function getAdmitedStudentList(plasement_id,admission_id=0){
		var instituteId     = $('#institute_id').val();
        var branchId        = $('#branch_id').val();
        var sessionId       = $('#session_id').val();
        var versionId       = $('#version_id').val();
        var classId    		= $('#class_id').val();
        var groupId   		= $('#group_id').val();
		
		if(instituteId >0 && branchId >0){
		  $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>billing/GetAdmitedStudents",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&session_id="+sessionId+"&version_id="+versionId+"&class_id="+classId+"&group_id="+groupId+"&admission_id="+admission_id,
            success: function(option){
             $('#'+plasement_id).html(option);
			 $('#'+plasement_id).trigger("chosen:updated");
			 //getFeesList();
            }//Success
          });// End datagrid
		}//end if
	}
	function isNullAndUndef(variable) {
       if(variable == null || variable == undefined || variable==""){
		 return true;
	   }else if(isNaN(variable)){ return true; }	
    }
	function checkReceivedAmount(received){
		var received_amount = parseFloat(received);		
		var invoice_no      = parseInt($('#invoice-no').val());
		if(isNullAndUndef(invoice_no)){invoice_no=0;} 
		if(invoice_no >0){
		  if(isNullAndUndef(received_amount)){received_amount=0;$("#received-amount").val("");} 
		  var due_amount =parseFloat($("#total-due").val());
		  if(isNullAndUndef(due_amount)){due_amount=0;} 
		  var remaining_amount = (due_amount-received_amount);
		  if(received_amount > due_amount){
			$("#received-amount").val(0);
			$('#danger-alert').show();
			$('#danger-alert').html("The collection amount can't be greater than the due amount.");
		  }else{
			
			$('#remaining-amount').val(remaining_amount.toFixed(2));
			$('#danger-alert').hide();
			$('#danger-alert').html("");
		  }
		}
 	
	}
	function ClearAll(){
		$(this).find('form').trigger('reset');
        $('#branch_id').val("").trigger('chosen:updated');
        $('#session_id').val("").trigger('chosen:updated');
        $('#version_id').val("").trigger('chosen:updated');
		$('#payment-mode').val("1");	
		$('#payment-mode').trigger('chosen:updated');
		showHideBank(1);
		$('#bank-name').val("");
		$('#branch-name').val("");
		$('#acc-no').val("");
		$('#cheque-no').val("");
		$('#cheque-no').trigger('chosen:updated');	
		$('#issue-date').val("");
		$('#cheque-type').val("");
		$('#cheque-type').trigger('chosen:updated');
		$('#dr-account').val("");
		$('#dr-account').trigger('chosen:updated');	
		$('#cr-account').val("");
		$('#cr-account').trigger('chosen:updated');
		$('#invoice-no').val("0");	
		$('#invoice-no').trigger('chosen:updated');
		$('#advance-collect').val("0");	
		$('#advance-collect').trigger('chosen:updated');
		$('#vat').val("0");	
		$('#vat').trigger('chosen:updated');
		$('#net-bill').val("");
		$('#paid-amount').val("");
		$('#total-due').val("");
		$('#payment-amount').val("");
		$("#remaining-amount").val("");
		$('#naration').val("");
		$('#contra-id').val("0");
		$('#voucher-no').val("");
		$('#voucher-type').val("1");
		$('#invoice-no').prop('disabled', false);
		$('#total-received-amount').val("0");
		$('#v-status').val("0");
		$('#details-id').val("0");
		$('#raw-data').html("");
	}
	function ClearRow(){		
		$('#invoice-no').val("0");	
		$('#invoice-no').trigger('chosen:updated');
		$('#advance-collect').val("0");	
		$('#advance-collect').trigger('chosen:updated');
		$('#vat').val("0");	
		$('#vat').trigger('chosen:updated');
		$('#net-bill').val("");
		$('#paid-amount').val("");
		$('#total-due').val("");
		$('#payment-amount').val("");
		$("#remaining-amount").val("");
		$('#invoice-no').prop('disabled', false);
		$('#details-id').val("0");
	}
	/* Start Delete Data*/
	function deleteRecord(invoice_no,contra_id){
		$('#contra-id').val(contra_id);
		$('#voucher-no').val(invoice_no);
		return false;
	}
	$('.confirm').click(function(){
		var contra_id  = $('#contra-id').val();
		var invoice_no = $("#voucher-no").val();
		$('#contra-id').val("0");
		$('#voucher-no').val("");
		if(contra_id >0 && invoice_no !=""){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>voucher/DeleteRecord",
				data: "contra-id="+contra_id+"&invoice-no="+invoice_no+"&voucher-type=1",
				success: function(option){
					if(option==1){
					$('#danger-alert').hide(1000);
					$('#alert').show(1000);
					$('#alert').html('Record deleted successfully!!!');		
					reloadDataGrid();
					}else{
					$('#alert').hide(1000);
					$('#danger-alert').show(1000);
					$('#danger-alert').html('Failed to delete this record. Please try again!!!');
					reloadDataGrid();
					}
				}//Success
			});// ajax
			return false;
		}
	});// End reset
	/* End Delete Data*/
	
	/* Start Dishonor Data*/
	function DishonoredCheque(invoice_no,contra_id){
		$('#contra-id').val(contra_id);
		$('#voucher-no').val(invoice_no);
		return false;
	}
	$('.confirm-dishonored').click(function(){
		var contra_id  		    = $('#contra-id').val();
		var invoice_no 		    = $("#voucher-no").val();
		var dishonor_remarks 	= $("#dishonor-remarks").val();
		$('#contra-id').val("0");
		$('#voucher-no').val("");
		if(contra_id >0 && invoice_no !=""){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>voucher/DishonoredCheque",
				data: "contra-id="+contra_id+"&invoice-no="+invoice_no+"&dishonor-remarks="+dishonor_remarks+"&voucher-type=1",
				success: function(result){
					if(result==1){
					$('#danger-alert').hide(1000);
					$('#alert').show(1000);
					$('#alert').html('The cheque has been dishonored successfully!!!');			
					reloadDataGrid();
					}else{
					$('#alert').hide(1000);
					$('#danger-alert').show(1000);
					$('#danger-alert').html('Failed to dishonored this cheque. Please try again!!!');
					reloadDataGrid();
					}
					
				}//Success
			});// ajax
			return false;
		}
	});// End reset
	/* End Dishonor Data*/
    
	/* Start Delete Item Data*/
	function deleteItem(details_id,contra_id,account_id,invoice_no){
		$('#contra-id').val(contra_id);
		$('#details-id').val(details_id);
		$('#voucher-no').val(invoice_no); 
		$('#del-account').val(account_id); 
		return false;
	}
	$('.confirm-item').click(function(){
		var contra_id  = $('#contra-id').val();
		var details_id = $("#details-id").val();
		var invoice_no = $("#voucher-no").val();
		var account_id = $("#del-account").val();
		if(isNullAndUndef(invoice_no)){invoice_no=0;} 
		$('#contra-id').val("0");
		$('#details-id').val("0");
		$('#voucher-no').val("");
		$('#del-account').val("0");
		if(contra_id >=0 && details_id >0 && invoice_no >=0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>voucher/DeleteItem",
				data: "contra-id="+contra_id+"&invoice-no="+invoice_no+"&account-id="+account_id+"&details-id="+details_id+"&voucher-type=1",
				success: function(option){
					if(option==1){
					$('#danger-alert').hide(1000);
					$('#alert').show(1000);
					$('#alert').html('Record deleted successfully!!!');		
					reloadDraftDataGrid();
					}else{
					$('#alert').hide(1000);
					$('#danger-alert').show(1000);
					$('#danger-alert').html('Failed to delete this record. Please try again!!!');
					reloadDraftDataGrid();
					}
				}//Success
			});// ajax
			return false;
		}
	});// End reset
	/* End Delete Data*/

	$('#reset').click(function(){
		$('#alert').hide();
		ClearAll();
	});// End reset
	function getInvoiceList(cr_account,placement,invoice_id=0){
		if(invoice_id==0){ var invoice_id = $('#voucher-no').val();}
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>voucher/GetAjaxPaymentInvoiceList",
		    data: "cr-account="+cr_account+"&invoice-id="+invoice_id+"&voucher-type=1",
		    success: function(option){
		        //alert(option);
		        $('#'+placement).html(option);
		        $('#'+placement).trigger('chosen:updated');
		    }//Success

		});// ajax
		return false;
    }
    function getAjaxInvoiceInfo(id,received_amount=0,status=1){
						
		if(id >0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>voucher/getAjaxInvoiceInfo",
			data: "invoice-no="+id,
			success: function(option){
				//alert(option);
				rsStr = option.split("##&##");
				//alert(rsStr[1]);
				if(received_amount==0){
				$('#net-bill').val(rsStr[0]);
				$('#paid-amount').val(rsStr[1]);
				$('#total-due').val(rsStr[2]);
				}else if(received_amount >0 && status==1){ 
				$('#net-bill').val(rsStr[0]);
				var paidamount = (parseFloat(rsStr[1])-parseFloat(received_amount));
				$('#paid-amount').val(paidamount);
				var due_amount = (parseFloat(rsStr[2]) + parseFloat(received_amount)); 
				due_amount = due_amount.toFixed(2);
				$('#total-due').val(due_amount);
				var remaining_amount = (due_amount-received_amount);
				$('#remaining-amount').val(remaining_amount.toFixed(2));
				}else if(received_amount >0 && status==0){ 
				$('#net-bill').val(rsStr[0]);
				var paidamount = parseFloat(rsStr[1]);
				$('#paid-amount').val(paidamount);
				var due_amount = parseFloat(rsStr[2]); 
				due_amount = due_amount.toFixed(2);
				$('#total-due').val(due_amount);
				var remaining_amount = (due_amount-received_amount);
				$('#remaining-amount').val(remaining_amount.toFixed(2));
				}
			}//Success

		});// ajax
		}else{
			$('#net-bill').val("");
			$('#paid-amount').val("");
			$('#total-due').val("");
			$('#remaining-amount').val("");
		}
		return false;
	}
	function reloadDataGrid(){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>voucher/GetPaymentRecords",
			success: function(option){
				$('#dataGrid').html(option);
				$('#danger-alert').hide();
			}//Success
		});// End datagrid
	}
	$('.search').click(function(){
		var institute_id	= $('#src-institute_id').val();
		if(isNullAndUndef(institute_id)){institute_id=0;} 
		var branch_id		= $('#src-branch_id').val();
		if(isNullAndUndef(branch_id)){branch_id=0;} 
		var session_id		= $('#src-session_id').val();
		if(isNullAndUndef(session_id)){session_id=0;}
		var fee_period		= 0;
		if(isNullAndUndef(fee_period)){fee_period=0;}
		var srcAccHead      = $("#src-customer_id").val();
		if(isNullAndUndef(srcAccHead)){srcAccHead=0;}  
		var srcPaymode      = $("#src-payment-mode").val();
		var srcFrom	    	= $('#src-date-from').val();
		var srcTo	    	= $('#src-date-to').val();
		$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>voucher/GetPaymentRecords",
		data: "src-institute-id="+institute_id+"&src-branch-id="+branch_id+"&src-session-id="+session_id+"&src-fee-period="+fee_period+"&src-account-id="+srcAccHead+"&src-payment-mode="+srcPaymode+"&receive-from="+srcFrom+"&receive-to="+srcTo,
				success: function(option){
					$('#dataGrid').html(option);
				}//Success
		});// ajax
		return false;
    });// End search
	
	/* Pagination Next Page */
	function nextPage(frm, to, pno) {
		var institute_id	= $('#src-institute_id').val();
		if(isNullAndUndef(institute_id)){institute_id=0;} 
		var branch_id		= $('#src-branch_id').val();
		if(isNullAndUndef(branch_id)){branch_id=0;} 
		var session_id		= $('#src-session_id').val();
		if(isNullAndUndef(session_id)){session_id=0;} 
		var version_id		= $('#src-version_id').val();
		if(isNullAndUndef(version_id)){version_id=0;}  
		var fee_period		= $('#src-fee_period').val();
		if(isNullAndUndef(fee_period)){fee_period=0;}
		var srcAccHead      = $("#src-acc-head").val();
		var srcPaymode      = $("#src-payment-mode").val();
		var srcFrom	    = $('#srcFrom').val();
		var srcTo	    = $('#srcTo').val();
		$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>voucher/GetPaymentRecords",
		data: "src-institute-id="+institute_id+"&src-branch-id="+branch_id+"&src-session-id="+session_id+"&src-version-id="+version_id+"&src-fee-period="+fee_period+"&src-account-id="+srcAccHead+"&src-payment-mode="+srcPaymode+"&receive-from="+srcFrom+"&receive-to="+srcTo+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
		success: function (option) {
			$('#dataGrid').html(option);
		}//Success
		});// End datagrid
		return false;
	}
	function editRecord(contra_id,details_id=0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>voucher/FillRecord",
			data: "contra-id="+contra_id+"&details-id="+details_id,
			success: function(option){
				//alert(option);
				rsStr = option.split("##&##");
				//alert(rsStr[1]);
				var others_payment=0;
				$('.others:checked').each(function() {
					others_payment = $(this).val();
				});	
				
				var contra_id = rsStr[0]; if(isNullAndUndef(contra_id)){contra_id="0";}		
				//$('#contra-id').val(contra_id);
				var voucher_no = rsStr[1]; if(voucher_no==0){voucher_no="";} 
				$('#voucher-no').val(voucher_no);
				var payment_mode=rsStr[2]; if(isNullAndUndef(payment_mode)){payment_mode="1";} 
				$('#payment-mode').val(payment_mode);	
				$('#payment-mode').trigger('chosen:updated');
				showHideBank(rsStr[2]);
				$('#created-date').val(rsStr[3]);
				var voucher_type = rsStr[4]; if(isNullAndUndef(voucher_type)){voucher_type="1";} 
				$('#voucher-type').val(voucher_type);
				$('#bank-name').val(rsStr[5]);
				$('#branch-name').val(rsStr[6]);
				$('#acc-no').val(rsStr[7]);
				$('#cheque-no').val(rsStr[8]);
				$('#cheque-no').trigger('chosen:updated');	
				$('#issue-date').val(rsStr[9]);
				$('#cheque-type').val(rsStr[10]);
				$('#cheque-type').trigger('chosen:updated');
				$('#dr-account').val(rsStr[11]);
				$('#dr-account').trigger('chosen:updated');	 				
				if(others_payment==1){
				 $('#cr-account').val(rsStr[22]);
				 $('#cr-account').trigger('chosen:updated');
				 //getInvoiceList(rsStr[13],'invoice-no',rsStr[15]); 
				}else{ 
				 $('#cr-account').val(rsStr[13]);
				 $('#cr-account').trigger('chosen:updated');				
				 //getInvoiceList(rsStr[11],'invoice-no',rsStr[15]);
				}
				$('#invoice-no').val(rsStr[15]); 
				if(parseInt(rsStr[15]>0)){
				$('#invoice-no').prop('disabled', true).trigger("chosen:updated");
				}
				getAjaxInvoiceInfo(rsStr[15],rsStr[16],rsStr[19]);
				$('#payment-amount').val(rsStr[16]);
				$('#naration').val(rsStr[17]);		
				$('#vat').val(rsStr[18]);					 
				$('#vat').trigger("chosen:updated");
				$('#advance-collect').val(rsStr[19]);					 
				$('#advance-collect').trigger("chosen:updated");
				var status = rsStr[20]; if(isNullAndUndef(status)){status=0;} 
				$('#v-status').val(status);
				var details_id = rsStr[21]; if(isNullAndUndef(details_id)){details_id=0;}	
				$('#details-id').val(details_id);
				$('#alert').show();
				$('#alert').html('Ready to Edit!');
			}//Success

		});// ajax
		return false;
	}
	
	function editMaster(id){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>voucher/FillMasterRecord",
			data: "contra-id="+id,
			success: function(option){
				ArrStr = option.split("@@##@@");
				rsStr  = ArrStr[0].split("##&##");
				//===== Bill List Grid =====
				var VGrid = ArrStr[1];
				$('#raw-data').html(VGrid);
				//===== Drop Down =====
				var INVDropdown = ArrStr[2];
				$('#invoice-no').html(INVDropdown);
		        $('#invoice-no').trigger('chosen:updated');
				//var CrDropdown = ArrStr[3];
				//$('#cr-account').html(CrDropdown);
		        //$('#cr-account').trigger('chosen:updated');				
				//alert(rsStr[0]);
				var contra_id = rsStr[0];
				if(isNullAndUndef(contra_id)){contra_id="0";} 				
				$('#contra-id').val(contra_id);
				var voucher_no = rsStr[1]; if(voucher_no==0){voucher_no="";} 
				$('#voucher-no').val(voucher_no);
				var payment_mode=rsStr[2];
				if(isNullAndUndef(payment_mode)){payment_mode="1";} 
				$('#payment-mode').val(payment_mode);	
				$('#payment-mode').trigger('chosen:updated');
				showHideBank(rsStr[2]);
				$('#created-date').val(rsStr[3]);
				var voucher_type = rsStr[4];
				if(isNullAndUndef(voucher_type)){voucher_type="1";} 
				$('#voucher-type').val(voucher_type);
				$('#bank-name').val(rsStr[5]);
				$('#branch-name').val(rsStr[6]);
				$('#acc-no').val(rsStr[7]);
				$('#cheque-no').val(rsStr[8]);
				$('#cheque-no').trigger('chosen:updated');	
				$('#issue-date').val(rsStr[9]);
				$('#cheque-type').val(rsStr[10]);
				$('#cheque-type').trigger('chosen:updated');
				$('#dr-account').val(rsStr[11]);
				$('#dr-account').trigger('chosen:updated');	
				
				$('#total-received-amount').val(rsStr[15]);
				$('#naration').val(rsStr[16]);
				var others_payment = rsStr[17]; if(isNullAndUndef(others_payment)){others_payment=0;}
				if(others_payment==1){ $("#others").prop('checked', true); }else{$("#others").prop('checked', false);}
				if(others_payment==1){
				  $('#cr-account').val(rsStr[26]);
				  $('#cr-account').trigger('chosen:updated');
				}else{					
				  $('#cr-account').val(rsStr[13]);
				  $('#cr-account').trigger('chosen:updated');
				}
				var status = rsStr[18];
				if(isNullAndUndef(status)){status=0;} 
				$('#v-status').val(status);
				
				var including_vat = rsStr[19];if(isNullAndUndef(including_vat)){including_vat=0;}
				
				$('#institute_id').val(rsStr[20]);
				$('#institute_id').trigger('chosen:updated');	
				$('#branch_id').val(rsStr[21]);
				$('#branch_id').trigger('chosen:updated');
				$('#session_id').val(rsStr[22]);
				$('#session_id').trigger('chosen:updated');
				$('#version_id').val(rsStr[23]);
				$('#version_id').trigger('chosen:updated');	
				//$('#class_id').val(rsStr[24]);
				//$('#class_id').trigger('chosen:updated');
				//$('#group_id').val(rsStr[25]).trigger('chosen:updated');				
				$(".show-create").show();
				$('#alert').show();
				$('#alert').html('Ready to Edit!');
			}//Success

		});// ajax
		return false;
	}
	function reloadDraftDataGrid(){
		var contra_id  = $('#contra-id').val();	
		if(isNullAndUndef(contra_id)){contra_id=0;} 
		var account_id = $('#cr-account').val();	
		if(isNullAndUndef(account_id)){account_id=0;} 
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>voucher/GetPaymentDraftRecords",
			data: "contra-id="+contra_id+"&account-id="+account_id,
			success: function(option){	
				setTimeout(function() {
				dataSTR  = option.split("##&##");
				dataGride= dataSTR[0];	
				$('#raw-data').html(dataGride);
				dataRow  = dataSTR[1].split("###");
				var total_received= parseFloat(dataRow[0]);
				total_received = total_received.toFixed(2);
				$('#total-received-amount').val(total_received);
				}, 50);				
		
				$('#danger-alert').hide();
			}//Success
		 });// End ajax
	}
        /* Pagination Next Page */
    function nextDraftPage(frm, to, pno) {
	   var contra_id  = $('#contra-id').val();	
	   if(isNullAndUndef(contra_id)){contra_id=0;} 
	   var account_id = $('#cr-account').val();	
	   if(isNullAndUndef(account_id)){account_id=0;} 
	   $.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>voucher/GetDraftRecords",
		data: "contra-id="+contra_id+"&account-id="+account_id+"&from="+frm+"&to="+to+"&page_no="+pno,
		success: function(option){	
			setTimeout(function() {
			dataSTR  = option.split("##&##");
			dataGride= dataSTR[0];	
			$('#raw-data').html(dataGride);
			dataRow  = dataSTR[1].split("###");
			$('#total-received-amount').val(dataRow[0]);
			}, 50);				
	
			$('#danger-alert').hide();
		}//Success
	   });// End ajax
	return false;
    }
	
    $('.save-detail').click(function(){
		var details_id	= $('#details-id').val();
		if(isNullAndUndef(details_id)){details_id=0;}
		var payment_mode= $('#payment-mode').val();
		var voucher_date= $('#created-date').val();
		var bank_name 	= $('#bank-name').val();
		var branch_name = $('#branch-name').val();
		var acc_no 	= $('#acc-no').val();
		var cheque_no	= $('#cheque-no').val();
		var cheque_type	= $('#cheque-type').val();	
		var issue_date  = $('#issue-date').val();
		var dr_account	= $('#dr-account').val();	
		var cr_account	= $('#cr-account').val();
		var invoice_no	= $('#invoice-no').val();
		var payment_amount= $('#payment-amount').val();
		if(isNullAndUndef(payment_amount)){payment_amount=0;}
		payment_amount  = Math.round(payment_amount);
		var naration	= $('#naration').val().replace(/&/g,'U+0026');
		var contra_id	= $('#contra-id').val();
		var voucher_no	= $('#voucher-no').val();
		var voucher_type= $('#voucher-type').val();
		var net_bill	= $('#net-bill').val();
		var paid_amount	= $('#paid-amount').val();
		var total_due	= $('#total-due').val();
		  	
		var advance_collect = $('#advance-collect').val();
		if(isNullAndUndef(advance_collect)){advance_collect=0;}
		
		var including_vat = $('#vat').val();
		if(isNullAndUndef(including_vat)){including_vat=0;} 

		var others_payment=0; var others_income=0;
		$('.others:checked').each(function() {
			others_payment = $(this).val();
		});		
		if(isNullAndUndef(others_payment)){others_payment=0;}		
		var receive_note= $('#receive-note').val().replace(/&/g,'U+0026'); 	
		var status	= $('#v-status').val();
		if(isNullAndUndef(status)){status=0;}       	
		//alert("Date="+voucher_date+" Dr="+dr_account+" Cr="+cr_account+" Amount="+payment_amount+" status="+status);
		if((voucher_date !="") && (parseInt(dr_account) >0 && parseInt(cr_account) >0 && parseInt(payment_amount) >0 && status < 3)){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>voucher/AddCollection",
				data: "details-id="+details_id+"&payment-mode="+payment_mode+"&voucher-date="+voucher_date+"&voucher-type="+voucher_type+"&bank-name="+bank_name+"&branch-name="+branch_name+"&acc-no="+acc_no+"&cheque-no="+cheque_no+"&cheque-type="+cheque_type+"&issue-date="+issue_date+"&dr-account="+dr_account+"&cr-account="+cr_account+"&invoice-no="+invoice_no+"&received-amount="+payment_amount+"&naration="+naration+"&including-vat="+including_vat+"&contra-id="+contra_id+"&voucher-no="+voucher_no+"&status="+status+"&advance-collect="+advance_collect+"&others-payment="+others_payment+"&others-income="+others_income+"&receive-note="+receive_note,
				success: function(option){
					$('#invoice-no').val("0").trigger('chosen:updated');
					$('#advance-collect').val("0").trigger('chosen:updated');
					$('#vat').val("0").trigger('chosen:updated');		
					$('#net-bill').val("");		
					$('#paid-amount').val("");		
					$('#total-due').val("");		
					$('#payment-amount').val("");		
					$('#receive-note').val("");
					$('#details-id').val("0");
					$('#danger-alert').hide();
					$('#alert').show(1000);
					$('#alert').html('Record has been added successfully!!!');
					setTimeout(function() {
					  dataSTR  = option.split("##&##");
					  dataGride= dataSTR[0];	
					  $('#raw-data').html(dataGride);
					  dataRow  = dataSTR[1].split("###");
					  $('#total-received-amount').val(dataRow[0]);
					}, 50);	
				}//Success
				
			});// ajax
		}else{
			$('#alert').hide();
			$('#alert').html('');
			$('#danger-alert').show();
			$('#danger-alert').html('Record did not saved! Please fill data in required fields');
			return false;
		}
		return false;
    	});// End save

	$('.save-voucher').click(function(){
		var instituteId     = $('#institute_id').val();
        var branchId        = $('#branch_id').val();
        var sessionId       = $('#session_id').val();
        var versionId       = $('#version_id').val();
        var classId    		= 0;
        var groupId   		= 0;
		var payment_mode= $('#payment-mode').val();
		var voucher_date= $('#created-date').val();
		var bank_name 	= $('#bank-name').val();
		var branch_name = $('#branch-name').val();
		var acc_no 		= $('#acc-no').val();
		var cheque_no	= $('#cheque-no').val();
		var cheque_type	= $('#cheque-type').val();	
		var issue_date  = $('#issue-date').val();
		var dr_account	= $('#dr-account').val();	
		var cr_account	= $('#cr-account').val();
		var invoice_no	= $('#invoice-no').val();
		var payment_amount= $('#total-received-amount').val();
		if(isNullAndUndef(payment_amount)){payment_amount=0;}
		payment_amount  = Math.round(payment_amount);
		var naration	= $('#naration').val().replace(/&/g,'U+0026');
		var contra_id	= $('#contra-id').val();
		var voucher_no	= $('#voucher-no').val();
		var voucher_type= $('#voucher-type').val();
		var net_bill	= $('#net-bill').val();
		var paid_amount	= $('#paid-amount').val();
		var total_due	= $('#total-due').val();
		var including_vat="0";
		$('.vat:checked').each(function() {
			including_vat = $(this).val();
		});		
  		if(isNullAndUndef(including_vat)){including_vat=0;}
		var advance_collect = $('#advance-collect').val();
		if(isNullAndUndef(advance_collect)){advance_collect=0;}

		var others_payment=0;
		$('.others:checked').each(function() {
			others_payment = $(this).val();
		});		
		if(isNullAndUndef(others_payment)){others_payment=0;}   
		if(isNullAndUndef(voucher_type)){voucher_type=1;}
		var status	= $('#v-status').val();
		if(isNullAndUndef(status)){status=0;}
		
		//alert(duration+packageId);
		if(status < 3 && parseInt(dr_account) >0 && parseInt(cr_account) >0 && parseInt(payment_amount) >0 && voucher_date !=""){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>voucher/SavePaymentRecord",
				data: "institute-id="+instituteId+"&branch-id="+branchId+"&session-id="+sessionId+"&version-id="+versionId+"&class-id="+classId+"&group-id="+groupId+"&contra-id="+contra_id+"&payment-mode="+payment_mode+"&voucher-date="+voucher_date+"&voucher-type="+voucher_type+"&bank-name="+bank_name+"&branch-name="+branch_name+"&acc-no="+acc_no+"&cheque-no="+cheque_no+"&cheque-type="+cheque_type+"&issue-date="+issue_date+"&dr-account="+dr_account+"&cr-account="+cr_account+"&invoice-no="+invoice_no+"&payment-amount="+payment_amount+"&naration="+naration+"&including-vat="+including_vat+"&voucher-no="+voucher_no+"&status="+status+"&advance-collect="+advance_collect+"&others-payment="+others_payment,
				success: function(option){
					//alert(option);
					if(option==0){
						$('#danger-alert').show();
						$('#danger-alert').html('Record did not saved! Please try again!!!');
						return false;
					}else{
						ClearAll();
						$('#raw-data').html("");
						$('#dataGrid').html(option);
						$('#alert').show();
						$('#alert').html('Record has been saved successfully!!!');
					}
				}//Success
			});// ajax
		}else{
			
			$('#danger-alert').show();
			$('#danger-alert').html('Record did not saved! Please fill data in required fields');
			return false;
		}
		return false;
	});// End save
	
    </script>
</body>
</html>
