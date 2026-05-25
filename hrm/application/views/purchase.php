<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("purchase");?> </title>
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
               <?php echo $this->lang->line("purchase");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("purchase");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                    <div class="card card-primary show-create">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("purchase");?></h3>
						<div class="card-tools">
						  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
						</div>
					</div> <!-- /.card-header -->  
                    <div class="card-body">
                        <div id="alert" class="alert alert-success"></div>
                        <div class="container-fluid">
                            <form method="post" id="InputForm" enctype="multipart/form-data">
							<div class="card card-info card-outline">							
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("purchase")." ".$this->lang->line("details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								
									<div class="row">
										 <div class="col-sm-4 col-md-4 col-lg-4">
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
										 <div class="col-sm-4 col-md-4 col-lg-4">
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
										 <div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="session_id"><?php echo $this->lang->line("session");?></label>
												<select name="session_id" id="session_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session");?></option>
													<?php foreach($squery->result() as $row){
														echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
													}
													?>
												</select>
											</div>
										 </div>
									</div>
																
									<div class="row">
										
										<div class="col-sm-2 col-md-2 col-lg-2">                                    
											<div class="form-group required">	
												<label class="control-label" for="purchase_date"><?php echo $this->lang->line("purchase_date");?></label>						
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="purchase_date" id="purchase_date">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
											</div>									
										</div>
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" for="fee_period"><?php echo $this->lang->line("month_name");?></label>
												<select name="fee_period" id="fee_period" class="chosen-select" required="">
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("month_name");?></option>
													<?php foreach($fpquery->result() as $row){
														if($this->session->userdata('language')=="en"){
															$period_name = $row->period_name_en;
														}else{
															$period_name = $row->period_name_bn;
														}
														echo '<option value="'.$row->period_no.'">'.$period_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>

										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="store_id"><?php echo $this->lang->line("store_name");?></label>
												<select name="store_id" id="store_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("store_name");?></option>
													<?php foreach($stquery->result() as $row){
														echo '<option value="'.$row->store_id.'">'.$row->store_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="supplier_id"><?php echo $this->lang->line("supplier_name");?></label>
												<select name="supplier_id" id="supplier_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("supplier_name");?></option>
														<?php foreach($spquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
									</div>							
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
						
							<div class="card card-info card-outline">							
								<div class="bg-default">
										<h3 class="container-fluid card-title"><?php echo $this->lang->line("product_details");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">								
									<table class="table table-condensed table-bordered"> <!-- class table-condensed -->
									   <thead>
										<tr>
											<th class="active"><?php echo $this->lang->line("particulars");?> *</th>
											<th class="active"><?php echo $this->lang->line("unit_price");?> *</th>
											<th class="active"><?php echo $this->lang->line("quantity");?> *</th>
											<th class="active"><?php echo $this->lang->line("free_qty");?></th>
											<th class="active"><?php echo $this->lang->line("total_amount");?></th>
											<th class="active"><?php echo $this->lang->line("remarks");?></th>
										</tr>
									   </thead>
									   <tbody>
										<tr>									
											<td class="active" id="detail-item1">
											<select name="particulars-id" id="particulars-id" class="form-control chosen-select" onChange='getProductPurchasePrice(this.value)' required="">
												<option value=""><?php echo $this->lang->line("particulars")." ".$this->lang->line("name");?></option>
												<?php foreach($fequery->result() as $row){
													echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
												?>
											</select>
											</td>						    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("unit_price");?>" name="unit-price" id="unit-price" onKeyUp="calTotalAmount()" class="form-control">
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("quantity");?>" name="quantity" id="quantity" onKeyUp="calTotalAmount()" class="form-control" style="line-height:24px"/>
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("free_qty");?>" name="free_qty" id="free_qty" class="form-control" style="line-height:24px"/>
											</td>						    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("total_amount");?>" name="total-amount" id="total-amount" class="form-control">
											
											</td>						    
											<td class="active" id="detail-item3">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("remarks");?>" name="remarks" id="remarks" class="form-control">								
											</td>
										</tr>
									   </tbody>
									</table>
									<div class="row">				
										<div class="col-sm-12 col-md-12 col-lg-12" style="margin-bottom:8px">
											<button type="button" id="resetrow" class="btn btn-sm btn-warning pull-right" style="float: right; margin-left: 10px;"><span class="glyphicon glyphicon-refresh" > <?php echo $this->lang->line("clear");?></span></button>
											<button type="button" class="btn btn-sm btn-info save-detail pull-right" style="float: right;"><span class="glyphicon glyphicon-saved"> <?php echo $this->lang->line("add");?></span></button>
										</div>
									</div>
									<div class="row" id="FeesGrid">
									
									</div>
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
										
										
							<div class="card card-info card-outline">
									
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("billing")." ".$this->lang->line("details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="total_bill"><?php echo $this->lang->line("total_bill");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("total_bill");?>" name="total_bill" id="total_bill" class="form-control" value="0" readonly> 
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="discount_percentage"><?php echo $this->lang->line("discount_percentage");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("discount_percentage");?>" name="discount_percentage" id="discount_percentage" class="form-control" value="0" onKeyUp="calDiscountPersent()"> 
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="discount_amount"><?php echo $this->lang->line("discount_amount");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("discount_amount");?>" name="discount_amount" id="discount_amount" class="form-control" value="0" onKeyUp="getSubTotalBill()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="sub_total"><?php echo $this->lang->line("sub_total");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("sub_total");?>" name="sub_total" id="sub_total" class="form-control" value="0" readonly> 
											</div>
										</div>
												
									</div>
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="description"><?php echo $this->lang->line("description");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("description");?>" name="description" id="description" class="form-control" value=""> 
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="vat_percentage"><?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> +</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> " name="vat_percentage" id="vat_percentage" class="form-control" onKeyUp="calVatPersent()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="vat_amount"><?php echo $this->lang->line("vat")." ".$this->lang->line("amount");?> +</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("vat")." ".$this->lang->line("amount");?> " name="vat_amount" id="vat_amount" class="form-control" onKeyUp="getNetPayble()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="net_amount"><?php echo $this->lang->line("net_bill")." ".$this->lang->line("payable");?></label>
												<input type="text" readonly required="" placeholder="<?php echo $this->lang->line("net_bill")." ".$this->lang->line("payable");?>" name="net_amount" id="net_amount" class="form-control">
											</div>
										</div>
										<input type="hidden" name="bill-id" id="bill-id" value="0">
										<input type="hidden" name="details-id" id="details-id" value="0">
										<input type="hidden" placeholder="Invoice Note 1" name="invoice_note1" id="invoice_note1" class="form-control" value="Please issue A/C payee cheque/Pay Order/DD/BEFTN in favor of <?php echo $this->session->userdata('company_name');?>.">
										<input type="hidden" placeholder="Invoice Note 2" name="invoice_note2" id="invoice_note2" class="form-control" value="Payment should be clear with in 45 days after receiving this invoice.">
												
									</div>									
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
					
							<div class="row">
							    <div class="col-sm-6 col-md-6 col-lg-6 img-container">
									<div class="form-group">
										<label class="control-label"><?php echo $this->lang->line("others_attach");?></label>
										<div class="input-group">
											<span class="input-group-btn">
												<span class="btn btn-default btn-file">
													Browse… <input type="file" name="others_attach" id="others_attach" class="form-control" style="height: 25px;">
												</span>
												<div style="padding-left:3px; width:4.1%; float:right">
											  <span data-toggle="tooltip" data-placement="top" title="PDF Document">
											  </span>
											  </div>
											</span>
											<input type="text" class="form-control" readonly>
										</div>
									</div>
								</div>
								<div class="col-sm-3 col-md-3 col-lg-3 pull-right">
								   <div class="pull-left" style="margin-top:30px">			
									<button type="reset" id="bill-clear" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-refresh"> <?php echo $this->lang->line("clear");?> </span></button> &nbsp;
									<button type="button" class="btn btn-md btn-success save-bill"><span class="glyphicon glyphicon-saved"> <?php echo $this->lang->line("save");?> </span></button>
								   </div>
								</div>
							</div>
				
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->    
                               
                    
				</div> <!-- End Card --> 
          
            <?php if($hasViewOption){?>
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("purchase");?></h3>
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
												<select name="src-branch_id" id="src-branch_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch");?></option>
													<?php foreach($bquery->result() as $row){
														echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>
									
									<div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-session_id"><?php echo $this->lang->line("session");?></label>
											<select name="src-session_id" id="src-session_id" class="chosen-select" required="">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session");?></option>
												<?php foreach($squery->result() as $row){
													echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-fee_period"><?php echo $this->lang->line("month_name");?></label>
											<select name="src-fee_period" id="src-fee_period" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("month_name");?></option>
												<?php foreach($fpquery->result() as $row){
													if($this->session->userdata('language')=="en"){
														$period_name = $row->period_name_en;
													}else{
														$period_name = $row->period_name_bn;
													}
													echo '<option value="'.$row->period_no.'">'.$period_name.'</option>';
												}
												?>
											</select>
										</div>
									</div>
								</div>
															
								<div class="row">

								 <div class="col-sm-4 col-md-4 col-lg-4">
									<div class="form-group required">
										<label class="control-label" for="src-store_id"><?php echo $this->lang->line("store_name");?></label>
										<select name="src-store_id" id="src-store_id" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("store_name");?></option>
											<?php foreach($stquery->result() as $row){
												echo '<option value="'.$row->store_id.'">'.$row->store_name.'</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-sm-2 col-md-2 col-lg-2">                                    
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
								<div class="col-sm-2 col-md-2 col-lg-2">                                    
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
										<label class="control-label" for="src-supplier_name"><?php echo $this->lang->line("supplier_name");?></label>
										<select name="src-supplier_name" id="src-supplier_name" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("supplier_name");?></option>
												<?php foreach($spquery->result() as $row){
													echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">								
								<div class="form-group text-right">
								<button type="button" class="btn btn-md btn-info search" style="margin-top:5px"><span class="glyphicon glyphicon-search"> <?php echo $this->lang->line("search");?></span></button>
								<?php if($hasPrintOption){?>
								<button type="button" onclick="PrintElem('#dataGrid')" class="btn btn-md btn-success print" style="margin-top:5px"><span class="glyphicon glyphicon-print"> <?php echo $this->lang->line("print");?></span></button>
								<?php }?>
								</div>						
							</div>
					    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("purchase")." ".$this->lang->line("list");?></h3>
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
        //$('.ItemList').hide(); 
		resizeChosen();
		jQuery(window).on('resize', resizeChosen);
		resizeGridChosen1();
		jQuery(window).on('resize', resizeGridChosen1);
		resizeGridChosen2();
		jQuery(window).on('resize', resizeGridChosen2);	
		resizeGridChosen3();
		jQuery(window).on('resize', resizeGridChosen3);
			    
        //End Chosen Responsive//
		$('#st-danger-alert').hide();
       
        $('#btnDelete').click(function() {
            $('#deleteModal').modal('hide');
        });
        $('.show-create').hide();
        $('#alert-delete').hide();
        $('#alert').hide();
		
    });
	function resizeChosen() {
        $(".chosen-container").each(function() {
            $(this).attr('style', 'width: 100%');
        });
    }
    function resizeGridChosen1() {
        $("#detail-item1 .chosen-container").each(function() {
            $(this).attr('style', 'width: 220px');
        });
    }
    function resizeGridChosen2() {
        $("#detail-item2 .chosen-container").each(function() {
            $(this).attr('style', 'width: 120px');
        });
    }
    function resizeGridChosen3() {
        $("#detail-item3 .chosen-container").each(function() {
            $(this).attr('style', 'width: 180px');
        });
    }
	
    $('.save-detail').click(function(){
	var details_id		= parseInt($('#details-id').val());
	var bill_id			= parseInt($('#bill-id').val());
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var supplierId      = parseInt($('#supplier_id').val());
	var sessionId       = parseInt($('#session_id').val());
	var storeId         = parseInt($('#store_id').val());
	var fee_period		= parseInt($('#fee_period').val());
	var purchase_date 	= $('#purchase_date').val();
	
	var particularsId	= parseInt($('#particulars-id').val());
	var quantity		= parseInt($('#quantity').val());
	var free_qty		= parseInt($('#free_qty').val());
	var unit_price 		= parseFloat($('#unit-price').val());
	var total_amount	= parseFloat($('#total-amount').val());	
	var remarks			= $('#remarks').val();      	
	
	if((purchase_date!="") && (instituteId >0 && branchId >0 && supplierId >0 && sessionId >0 && storeId >0 && fee_period >0 && particularsId >0 && quantity >0 && unit_price >0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>purchase/AddBill",
			data: "details-id="+details_id+"&bill-id="+bill_id+"&bill-date="+purchase_date+"&bill-period="+fee_period+"&institute_id="+instituteId+"&branch_id="+branchId+"&supplier_id="+supplierId+"&session_id="+sessionId+"&store_id="+storeId+"&particulars-id="+particularsId+"&quantity="+quantity+"&free_qty="+free_qty+"&unit_price="+unit_price+"&total_amount="+total_amount+"&remarks="+remarks,
			success: function(option){
				$('#particulars-id').val("").trigger('chosen:updated');
				$('#quantity').val("");
				$('#free_qty').val("");
				$('#unit-price').val("");				
				$('#total-amount').val("0");				
				$('#remarks').val("");
				$('#details-id').val("0");
				$('#danger-alert').hide();
				$('#alert').show(1000);
				$('#alert').html('Record has been added successfully!!!');
				setTimeout(function() {
				  dataSTR  = option.split("##&##");
				  dataGride= dataSTR[0];	
				  $('#FeesGrid').html(dataGride);
				  total_bill= dataSTR[1];	
				  $('#total_bill').val(total_bill);
				}, 100);	

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
	
	$('#resetrow').click(function(){
		$('#particulars-id').val("").trigger('chosen:updated');
		$('#quantity').val("");
		$('#free_qty').val("");
		$('#unit-price').val("");				
		$('#total-amount').val("0");				
		$('#remarks').val("");
		$('#details-id').val("0");
	});// End clear
	
	$('.save-bill').click(function(){
	var details_id		= parseInt($('#details-id').val());
	var bill_id			= parseInt($('#bill-id').val());
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var supplierId      = parseInt($('#supplier_id').val());
	var sessionId       = parseInt($('#session_id').val());
	var storeId         = parseInt($('#store_id').val());
	var fee_period		= parseInt($('#fee_period').val());
	var purchase_date 	= $('#purchase_date').val();
	
	var discount_percentage	= parseFloat($('#discount_percentage').val());
	var discount_amount		= parseFloat($('#discount_amount').val());
	var vat_percentage 		= parseFloat($('#vat_percentage').val());
	var vat_amount 			= parseFloat($('#vat_amount').val());		
	var total_bill      	= parseFloat($('#total_bill').val());		
	var net_amount      	= parseFloat($('#net_amount').val());		
	var invoice_note1      	= parseFloat($('#invoice_note1').val());		
	var invoice_note2      	= parseFloat($('#invoice_note2').val());
	var description			= $('#description').val().replace(/&/g,'U+0026');
	
	if((purchase_date!="") && (instituteId >0 && branchId >0 && supplierId >0 && sessionId >0 && storeId >0 && fee_period >0 && total_bill >0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>purchase/SaveBill",
			data: "details-id="+details_id+"&bill-id="+bill_id+"&bill-date="+purchase_date+"&bill-period="+fee_period+"&institute_id="+instituteId+"&branch_id="+branchId+"&supplier_id="+supplierId+"&session_id="+sessionId+"&store_id="+storeId+"&discount_percentage="+discount_percentage+"&discount_amount="+discount_amount+"&vat_percentage="+vat_percentage+"&vat_amount="+vat_amount+"&total_bill="+total_bill+"&net_amount="+net_amount+"&invoice_note1="+invoice_note1+"&invoice_note2="+invoice_note2+"&description="+description,
			success: function(option){
				$('#dataGrid').html(option);				
				$('#danger-alert').hide();
				$('#alert').show(1000);
				$('#alert').html('Record has been save successfully!!!');
				ClearForm();
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
	
	$('#bill-clear').click(function(){
		ClearForm();
	});// End clear
	
	function ClearForm(){		
		$('#details-id').val("0");
		$('#bill-id').val("0");
		$('#supplier_id').val("0").trigger("chosen:updated");
		$('#session_id').val("").trigger("chosen:updated");
		$('#store_id').val("").trigger("chosen:updated");
		$('#fee_period').val("0").trigger("chosen:updated");
		$('#purchase_date').val("");
		$('#description').val("");
		$('#discount_percentage').val("0");
		$('#discount_amount').val("0");
		$('#vat_percentage').val("0");
		$('#vat_amount').val("0");
		$('#total_bill').val("0");
		$('#net_amount').val("0");
		$('#FeesGrid').html("");
	}
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#bill-id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
		var delId = $('#bill-id').val();
		$('#bill-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>purchase/DelRecord",
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
	    	
    function getProductPurchasePrice(particulars_id){
		var instituteId     = $('#institute_id').val();
		var branchId        = $('#branch_id').val();
        if(particulars_id >0){			
    	   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>billing/loadProductPurchasePrice",
			data: "institute_id="+instituteId+"&branch_id="+branchId+"&particulars-id="+particulars_id,
			success: function(option){				
				rsStr  = option.split("##&##");
				//alert(rsStr[1]);	
				if(parseInt(rsStr[0])>0){
					$('#unit-price').val(rsStr[0]);
					$('#quantity').select();
					$('#quantity').focus();
				}else{					
					$('#unit-price').val("");
					$('#unit-price').focus();
				}
                }//Success
           });// ajax
		}
        return false;
    }
	function isNullAndUndef(variable) {
    	if(variable == null || variable == undefined || variable==""){
		return true;
		}else if(isNaN(variable)){ return true; }	
	}
	function calDiscountPersent(){
		var total_bill 			= parseFloat($('#total_bill').val());
		var discount_percentage = parseFloat($('#discount_percentage').val());
		if(isNullAndUndef(total_bill)){ total_bill = 0;}
		if(isNullAndUndef(discount_percentage)){ discount_percentage = 0;}
		if(total_bill >0){
		var discount_amount		= ((total_bill/100) * discount_percentage);
		}else{
		var discount_amount		= 0;	
		}
		$('#discount_amount').val(discount_amount);
		var sub_total = (total_bill - discount_amount);
		if(isNullAndUndef(sub_total)){ sub_total = 0;}
		sub_total = Math.round(sub_total);
		$('#sub_total').val(sub_total);
		
		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		var vat_amount 			= parseFloat($('#vat_amount').val());
		if(isNullAndUndef(vat_amount)){ vat_amount = 0;}
		if(vat_percentage >0 && vat_amount==0){
			vat_amount = ((sub_total/100) * vat_percentage); 
			if(isNullAndUndef(vat_amount)){ vat_amount = 0; }
			vat_amount = Math.round(vat_amount);
			var net_payble = (sub_total + vat_amount);
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#vat_amount').val(vat_amount);
			$('#net_amount').val(net_payble);
		}else{
			vat_amount = Math.round(vat_amount);
			if(vat_amount >0){
			  vat_percentage = ((vat_amount * 100)/sub_total);
			}else{ vat_amount =0; vat_percentage=0;}
			
			if(isNullAndUndef(vat_percentage)){ vat_percentage = 0; }
			vat_percentage = vat_percentage.toFixed(4);
			$('#vat_percentage').val(vat_percentage);
			$('#vat_amount').val(vat_amount);			
			var net_payble = (sub_total + vat_amount);
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#net_amount').val(net_payble);
		}		
	}
	
	function getSubTotalBill(){
		var total_bill 			= parseFloat($('#total_bill').val());
		if(isNullAndUndef(total_bill)){ total_bill = 0;}
		var discount_percentage = parseFloat($('#discount_percentage').val());
		if(isNullAndUndef(discount_percentage)){ discount_percentage = 0;}
		var discount_amount 	= parseFloat($('#discount_amount').val());		
		if(isNullAndUndef(discount_amount)){ discount_amount = 0;}
		
		if(discount_percentage >0 && discount_amount==0){
			discount_amount = ((total_bill/100) * discount_percentage); 
			if(isNullAndUndef(discount_amount)){ discount_amount = 0; }
			discount_amount = Math.round(discount_amount);
			
			var sub_total = (total_bill - discount_amount);
			
			if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
			$('#discount_amount').val(discount_amount);
			$('#sub_total').val(sub_total);
		}else{
			discount_amount = Math.round(discount_amount);
			if(discount_amount >0){
			  discount_percentage = ((discount_amount * 100)/total_bill);
			}else{ discount_amount =0; discount_percentage=0;}
			
			if(isNullAndUndef(discount_percentage)){ discount_percentage = 0; }
			discount_percentage = discount_percentage.toFixed(4);
			$('#discount_percentage').val(discount_percentage);
			$('#discount_amount').val(discount_amount);			
			var sub_total = (total_bill - discount_amount);
			
			if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
			$('#sub_total').val(sub_total);
		}
		
		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		var vat_amount 			= parseFloat($('#vat_amount').val());
		if(isNullAndUndef(vat_amount)){ vat_amount = 0;}
		if(vat_percentage >0 && vat_amount==0){
			vat_amount = ((sub_total/100) * vat_percentage); 
			if(isNullAndUndef(vat_amount)){ vat_amount = 0; }
			vat_amount = Math.round(vat_amount);
			var net_payble = (sub_total + vat_amount);
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#vat_amount').val(vat_amount);
			$('#net_amount').val(net_payble);
		}else{
			vat_amount = Math.round(vat_amount);
			if(vat_amount >0){
			  vat_percentage = ((vat_amount * 100)/sub_total);
			}else{ vat_amount =0; vat_percentage=0;}
			
			if(isNullAndUndef(vat_percentage)){ vat_percentage = 0; }
			vat_percentage = vat_percentage.toFixed(4);
			$('#vat_percentage').val(vat_percentage);
			$('#vat_amount').val(vat_amount);			
			var net_payble = (sub_total + vat_amount);
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#net_amount').val(net_payble);
		}	
	}
	
	function calVatPersent(){		
		var sub_total 	   = parseFloat($('#sub_total').val());
		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(sub_total)){ sub_total = 0;}
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		
		if(sub_total >0){
		var vat_amount		= ((sub_total/100) * vat_percentage);
		}else{
		var sub_total		= 0;	
		}
		
		if(sub_total==0){
			var total_bill 			= parseFloat($('#total_bill').val());
			if(isNullAndUndef(total_bill)){ total_bill = 0;}
			var discount_percentage = parseFloat($('#discount_percentage').val());
			if(isNullAndUndef(discount_percentage)){ discount_percentage = 0;}
			var discount_amount 	= parseFloat($('#discount_amount').val());		
			if(isNullAndUndef(discount_amount)){ discount_amount = 0;}
			
			if(discount_percentage >0 && discount_amount==0){
				discount_amount = ((total_bill/100) * discount_percentage); 
				if(isNullAndUndef(discount_amount)){ discount_amount = 0; }
				discount_amount = Math.round(discount_amount);				
				var sub_total = (total_bill - discount_amount);
				
				if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
				$('#discount_amount').val(discount_amount);
				$('#sub_total').val(sub_total);
				
				if(sub_total >0){
				  vat_amount		= ((sub_total/100) * vat_percentage);
				}else{
				  vat_amount		= 0;	
				}	
				
			}else{
				discount_amount = Math.round(discount_amount);
				if(discount_amount >0){
				  discount_percentage = ((discount_amount * 100)/total_bill);
				}else{ discount_amount =0; discount_percentage=0;}
				
				if(isNullAndUndef(discount_percentage)){ discount_percentage = 0; }
				discount_percentage = discount_percentage.toFixed(4);
				$('#discount_percentage').val(discount_percentage);
				$('#discount_amount').val(discount_amount);			
				var sub_total = (total_bill - discount_amount);
				
				if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
				$('#sub_total').val(sub_total);
				
				if(sub_total >0){
				  vat_amount		= ((sub_total/100) * vat_percentage);
				}else{
				  vat_amount		= 0;	
				}
			}
		}
		
		$('#vat_amount').val(vat_amount);
		var net_payble = (sub_total + vat_amount);
		if(isNullAndUndef(net_payble)){ net_payble = 0;} net_payble = Math.round(net_payble);
		$('#net_amount').val(net_payble);				
	}
	
	function getNetPayble(){
		var total_bill 			= parseFloat($('#total_bill').val());
		if(isNullAndUndef(total_bill)){ total_bill = 0;}
		var discount_percentage = parseFloat($('#discount_percentage').val());
		if(isNullAndUndef(discount_percentage)){ discount_percentage = 0;}
		var discount_amount 	= parseFloat($('#discount_amount').val());		
		if(isNullAndUndef(discount_amount)){ discount_amount = 0;}
		
		if(discount_percentage >0 && discount_amount==0){
			discount_amount = ((total_bill/100) * discount_percentage); 
			if(isNullAndUndef(discount_amount)){ discount_amount = 0; }
			discount_amount = Math.round(discount_amount);
			
			var sub_total = (total_bill - discount_amount);
			
			if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
			$('#discount_amount').val(discount_amount);
			$('#sub_total').val(sub_total);
		}else{
			discount_amount = Math.round(discount_amount);
			if(discount_amount >0){
			  discount_percentage = ((discount_amount * 100)/total_bill);
			}else{ discount_amount =0; discount_percentage=0;}
			
			if(isNullAndUndef(discount_percentage)){ discount_percentage = 0; }
			discount_percentage = discount_percentage.toFixed(4);
			$('#discount_percentage').val(discount_percentage);
			$('#discount_amount').val(discount_amount);			
			var sub_total = (total_bill - discount_amount);
			
			if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
			$('#sub_total').val(sub_total);
		}
		
		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		var vat_amount 			= parseFloat($('#vat_amount').val());
		if(isNullAndUndef(vat_amount)){ vat_amount = 0;}
		if(vat_percentage >0 && vat_amount==0){
			vat_amount = ((sub_total/100) * vat_percentage); 
			if(isNullAndUndef(vat_amount)){ vat_amount = 0; }
			vat_amount = Math.round(vat_amount);
			var net_payble = (sub_total + vat_amount);
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#vat_amount').val(vat_amount);
			$('#net_amount').val(net_payble);
		}else{
			vat_amount = Math.round(vat_amount);
			if(vat_amount >0){
			  vat_percentage = ((vat_amount * 100)/sub_total);
			}else{ vat_amount =0; vat_percentage=0;}
			
			if(isNullAndUndef(vat_percentage)){ vat_percentage = 0; }
			vat_percentage = vat_percentage.toFixed(4);
			$('#vat_percentage').val(vat_percentage);
			$('#vat_amount').val(vat_amount);			
			var net_payble = (sub_total + vat_amount);
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#net_amount').val(net_payble);
		}		
	}
	
	function calTotalAmount(){
		var unit_price = parseFloat($('#unit-price').val());
		var quantity   = parseFloat($('#quantity').val());
		var total_bill = (unit_price * quantity);
		$('#total-amount').val(total_bill);
	}
			
    function editRow(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>purchase/FillDetails",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#particulars-id').val(rsStr[1]);
            $('#particulars-id').trigger("chosen:updated");
            $('#unit-price').val(rsStr[2]);
            $('#quantity').val(rsStr[3]);
            $('#free_qty').val(rsStr[4]);
            $('#total-amount').val(rsStr[5]);
            $('#remarks').val(rsStr[6]);			
            $('#details-id').val(rsStr[0]);			
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
            }//Success
        });// ajax
        return false;
    }
	
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>purchase/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
			ArrStr = option.split("@@##@@");
			rsStr  = ArrStr[0].split("##&##");
			//===== Bill List Grid =====
			//var StudentDropdown = ArrStr[1];
            //alert(rsStr[1]);			
           	$('#details-id').val(0);
			$('#bill-id').val(rsStr[1]);
			$('#institute_id').val(rsStr[2]).trigger("chosen:updated");	
			$('#branch_id').val(rsStr[3]).trigger("chosen:updated");		
			$('#supplier_id').val(rsStr[4]).trigger("chosen:updated");
			$('#session_id').val(rsStr[5]).trigger("chosen:updated");
			$('#store_id').val(rsStr[6]).trigger("chosen:updated");
			$('#fee_period').val(rsStr[7]).trigger("chosen:updated");
			$('#purchase_date').val(rsStr[8]);			
			$('#discount_percentage').val(rsStr[9]);
			$('#discount_amount').val(rsStr[10]);			
			$('#vat_percentage').val(rsStr[11]);
			$('#vat_amount').val(rsStr[12]);
			$('#total_bill').val(rsStr[13]);
			$('#net_amount').val(rsStr[14]);
			$('#description').val(rsStr[15]);
			getFeesList(rsStr[1]);		
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
            }//Success
        });// ajax
        return false;
    }
    function getFeesList(bill_id=0){		
        var instituteId     = parseInt($('#institute_id').val());
        var branchId        = parseInt($('#branch_id').val());
        var sessionId       = parseInt($('#session_id').val());
        var storeId         = parseInt($('#store_id').val());
		var supplierId		= parseInt($('#supplier_id').val());
		var fee_period		= parseInt($('#fee_period').val());
		if(bill_id==0){ var bill_id			= parseInt($('#bill-id').val()); }
		
		var bill_date 		= $('#bill_date').val();
		if(instituteId >0 && branchId >0 && sessionId >0 && storeId >0 && supplierId >0 && fee_period >0){
		 $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>purchase/GetBillList",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&bill_date="+bill_date+"&session_id="+sessionId+"&store_id="+storeId+"&supplier_id="+supplierId+"&bill-period="+fee_period+"&bill-id="+bill_id,
            success: function(option){
				  dataSTR  = option.split("##&##");
				  dataGride= dataSTR[0];	
				  $('#FeesGrid').html(dataGride);
				  total_bill= dataSTR[1];	
				  $('#total_bill').val(total_bill);
            }//Success
         });// End datagrid
		}
	}
	function reloadDataGrid(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>purchase/GetRecords",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
	$('.search').click(function(){
    	var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var periodId        = $('#src-fee_period').val();
        var sessionId       = $('#src-session_id').val();
        var storeId         = $('#src-store_id').val();
        var supplierId   	= $('#src-supplier_id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>purchase/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-period="+periodId+"&src-session="+sessionId+"&src-store="+storeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&src-supplier="+supplierId,
            success: function(option){
                $('#dataGrid').html(option);
            }//Success
        });// ajax
        return false;
    });// End search
	
    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var periodId        = $('#src-fee_period').val();
        var sessionId       = $('#src-session_id').val();
        var storeId         = $('#src-store_id').val();
        var supplierId   	= $('#src-supplier_id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>purchase/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-period="+periodId+"&src-session="+sessionId+"&src-store="+storeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&src-supplier="+supplierId+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }	
		
    </script>
</body>
</html>
