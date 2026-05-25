<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("workorder");?> </title>
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
    
   <!--Add New Salesman Modal-->
   <div class="modal fade" data-refresh="true" id="addBModal" tabindex="-1" role="dialog" aria-labelledby="addBModalLabel" aria-hidden="true">
	  <div class="modal-dialog">
		<!-- Add New Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<p class="modal-title" style="font-size: x-large"><i class="fa fa-edit"></i> Salesman</p>
			</div>
			<div class="modal-body">				
				<form>
					<div class="row">
						<div class="col-sm-6 col-md-6 col-lg-6">
        					<div class="form-group required">
        						<label class="control-label" for="Sales Person Name">Sales Person Name:</label>
        						<input type="text" required="" placeholder="Sales Person Name" name="employee_name" id="employee_name" class="form-control">
        					</div>
        				</div>
        				<div class="col-sm-6 col-md-6 col-lg-6">
        					<div class="form-group required">
        						<label class="control-label" for="Designation">Designation:</label>
        						<input type="text" required="" placeholder="Designation" name="designation" id="designation" class="form-control">
        					</div>
						</div>
					</div>	
					<div class="row">
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group required">
								<label class="control-label" for="login_id"><?php echo $this->lang->line("login_id");?></label>
								<input type="text" placeholder="<?php echo $this->lang->line("login_id");?>" name="login_id" class="form-control" id="login_id" required="">
								
							</div>
						</div>
						
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group required">
								<label class="control-label" for="password"><?php echo $this->lang->line("password");?></label>											
								<input type="password" placeholder="<?php echo $this->lang->line("password");?>" name="password" class="form-control" id="password" required="">
																				
							</div>
						</div>
					</div>				
					<input type="hidden" name="employee_id" class="form-control" id="employee_id">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="reset" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				<button type="button" id="btnSave" class="btn btn-success save-emp"><i class="fa fa-save"></i> Save</button>
			</div>
		</div>
	  </div>
   </div>  
   
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
    <!--Delete Row Modal-->
    <div class="modal fade" id="deleteDraftModal" role="dialog" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
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
                    <button type="button" id="btnRowDelete" class="btn btn-danger confirmDraft"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line("delete");?></button>
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
               <?php echo $this->lang->line("customer_workorder");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("workorder");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                    <div class="card card-primary show-create">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("customer_workorder");?></h3>
						<div class="card-tools">
						  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
						</div>
					</div> <!-- /.card-header -->  
                    <div class="card-body">                        						    
						<div id="alert" class="alert alert-success"></div>
						<div id="danger-alert" class="alert alert-danger"></div>
                        <div class="container-fluid">
                            <form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>workorder/SaveWorkorder">
							<div class="card card-info card-outline">							
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("workorder")." ".$this->lang->line("details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="customer_id"><?php echo $this->lang->line("customer_name");?></label>
												<select name="customer_id" id="customer_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("customer_name");?></option>
														<?php foreach($spquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->head_id.' : '.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group">	
												<label class="control-label" for="workorder_no"><?php echo $this->lang->line("workorder_no");?></label>
												<input type="text" class="form-control" name="workorder_no" id="workorder_no" placeholder="<?php echo $this->lang->line("workorder_no");?>" required="">
											</div>								
										</div> 
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">	
												<label class="control-label" for="workorder_type"><?php echo $this->lang->line("workorder_type");?></label>			
											    <select name="workorder_type" id="workorder_type" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("workorder_type");?></option>
													<option value="1">Fresh</option>
													<option value="2">Renual</option>
													<option value="3">Addon</option>
												</select>
											</div>								
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">	
												<label class="control-label" for="workorder_date"><?php echo $this->lang->line("workorder_date");?></label>			
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="workorder_date" id="workorder_date" required="">
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
												<label class="control-label" for="salesman_id"><?php echo $this->lang->line("sales_person");?></label>
    											<div class="pull-right">
                    								<!--span class="input-group-addon"-->
                    								<span data-toggle="tooltip" data-original-title="Add New"><a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#addBModal"><i class="fa fa-plus"></i></a></span>								
                    								<!--/span-->
                    							</div>
												<select name="salesman_id" id="salesman_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("sales_person");?></option>
														<?php foreach($smquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-5 col-md-5 col-lg-5">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<div class="form-group">	
												<label class="control-label" for="oem"><?php echo $this->lang->line("oem");?></label>
												<input type="text" class="form-control" name="oem" id="oem" placeholder="<?php echo $this->lang->line("oem");?>">
											</div>
										</div>	
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">	
												<label class="control-label" for="delivery_date"><?php echo $this->lang->line("delivery_date");?></label>			
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="delivery_date" id="delivery_date" required="">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
											</div>								
										</div>
									</div>
																
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">									<div class="form-group required">	
												<label class="control-label" for="including_vat"><?php echo $this->lang->line("including_vat");?></label>			
											    <select name="including_vat" id="including_vat" class="chosen-select" onChange="setlblVATAIT(this.value)" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("including_vat");?></option>
													<option value="0">No</option>
													<option value="1">Yes</option>
												</select>
											</div>
										</div>
										<div class="col-sm-5 col-md-5 col-lg-5">										
											<div class="form-group required">
												<label class="control-label" for="vat_percent"> <?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> <em id="lbl_vatp">(Add)</em> </label> 
												<input type="text" required="" placeholder="<?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> " name="vat_percent" id="vat_percent" class="form-control">
											</div>	
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="ait_percent"> <?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> <em id="lbl_aitp">(Add)</em> </label> 
												<input type="text" required="" placeholder="<?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> " name="ait_percent" id="ait_percent" class="form-control">
											</div>								
										</div>
									</div>
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
						
							<div class="card card-info card-outline">							
								<div class="bg-default">
										<h3 class="container-fluid card-title"><?php echo $this->lang->line("product_details")."(".$this->lang->line("sales").")";?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">								
									<table class="table table-condensed table-bordered"> <!-- class table-condensed -->
									   <thead>
										<tr>
										    <th class="active"><?php echo $this->lang->line("category");?> *</th>
											<th class="active"><?php echo $this->lang->line("product_description");?> *</th>
											<th class="active"><?php echo $this->lang->line("product_sku");?></th>
											<th class="active"><?php echo $this->lang->line("validity");?></th>
											<th class="active"><?php echo $this->lang->line("quantity");?> *</th>
											<th class="active"><?php echo $this->lang->line("unit_price");?> *</th>
											<th class="active"><?php echo $this->lang->line("total");?></th>
											<th class="active"><?php echo $this->lang->line("remarks");?></th>
										</tr>
									   </thead>
									   <tbody>
										<tr>
											<td class="active" id="detail-item2">
											<select name="category" id="category" class="select">
                							    <option value="0">Select one</option>
                							    <option value="1">Hardware</option>
                							    <option value="2">Software</option>
                							    <option value="3">Support</option>
                							    <option value="4">Training</option>
                							    <option value="5">AMC</option>
                							</select>
											
											</td>									
											<td class="active" id="detail-item4">
											<input type="text" placeholder="<?php echo $this->lang->line("product_description");?>" name="product-description" id="product-description" class="form-control" style="min-width:170px">
											</td>
											<td class="active" id="detail-item1">
											
											<input type="text" placeholder="<?php echo $this->lang->line("product_sku");?>" name="product-sku" id="product-sku" class="form-control" style="width:109px">
											</td>							    
											<td class="active" id="detail-item2">
											<input type="text" placeholder="<?php echo $this->lang->line("validity");?>" name="validity" id="validity" class="form-control" style="width: 74px;">								
											</td>
											<td class="active" id="detail-item2">
											<input type="text" placeholder="<?php echo $this->lang->line("quantity");?>" name="quantity" id="quantity" onKeyUp="calTotalAmount()" class="form-control" style="width: 85px;"/>
											</td>					    
											<td class="active" id="detail-item2">
											<input type="text" placeholder="<?php echo $this->lang->line("unit_price");?>" name="unit-price" id="unit-price" onKeyUp="calTotalAmount()" class="form-control" style="width:85px">
											</td>						    
											<td class="active" id="detail-item2">
											<input type="text" placeholder="<?php echo $this->lang->line("total");?>" name="total-amount" id="total-amount" class="form-control" style="width:92px">
											
											<input type="hidden" name="total-vat" id="total-vat" style="width:92px" value="0">
											<input type="hidden" name="total-ait" id="total-ait" style="width:92px" value="0">
											
											</td>
											<td class="active" id="detail-item2">
											<input type="text" placeholder="<?php echo $this->lang->line("remarks");?>" name="remarks" id="remarks" class="form-control" style="min-width:60px">
											
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
									<div class="row" id="ProductGrid">
									
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
												<label class="control-label" for="total_bill"><?php echo $this->lang->line("total")." ".$this->lang->line("workorder_value");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("total")." ".$this->lang->line("workorder_value");?>" name="total_bill" id="total_bill" class="form-control" value="0"> 
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
												<label class="control-label" for="Payment Mode"><?php echo $this->lang->line("payment_mode");?>:</label>
												<select name="payment_mode" id="payment-mode" class="chosen-select" required="">
													<option value="1">Cash</option>	
													<option value="2">Cheque</option>	
													<option value="3">bKash</option>	
													<option value="10">Others</option>	
												</select>																
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="vat_percentage"> <?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> <em id="lbl_vatp">(Add)</em> </label> 
												<input type="text" required="" placeholder="<?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> " name="vat_percentage" id="vat_percentage" class="form-control" onKeyUp="calVatPersent()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="vat_amount"><?php echo $this->lang->line("vat")." ".$this->lang->line("amount");?> <em id="lbl_vat">(Add)</em></label> 
												<input type="text" required="" placeholder="<?php echo $this->lang->line("vat")." ".$this->lang->line("amount");?> " name="vat_amount" id="vat_amount" class="form-control" onKeyUp="getNetPayble()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="grand_total"><?php echo $this->lang->line("grand_total");?></label>
												<input type="text" readonly required="" placeholder="<?php echo $this->lang->line("grand_total");?>" name="grand_total" id="grand_total" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="midman_commission"><?php echo $this->lang->line("midman_commission");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("midman_commission")." (BDT)";?>" name="midman_commission" id="midman_commission" class="form-control">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="ait_percentage"><?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> <em id="lbl_aitp">Add </em></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> " name="ait_percentage" id="ait_percentage" class="form-control" onKeyUp="calAitPersent()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="ait_amount"><?php echo $this->lang->line("ait")." ".$this->lang->line("amount");?> <em id="lbl_ait">Add </em></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("ait")." ".$this->lang->line("amount");?> " name="ait_amount" id="ait_amount" class="form-control" onKeyUp="getNetPayble()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="net_amount"><?php echo $this->lang->line("net_bill")." ".$this->lang->line("receivable");?></label>
												<input type="text" readonly required="" placeholder="<?php echo $this->lang->line("net_bill")." ".$this->lang->line("receivable");?>" name="net_amount" id="net_amount" class="form-control">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("workorder_attach");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="workorder_attach" id="workorder_attach" class="form-control" style="height: 25px;" required="">
														</span>
														<div style="padding-left:3px; width:4.1%; float:right">
													  <span data-toggle="tooltip" data-placement="top" title="PDF Document">
													  </span>
													  </div>
													</span>
													<input type="text" class="form-control" readonly>
											        <input type="hidden" placeholder="<?php echo $this->lang->line("description");?>" name="description" id="description" class="form-control" value="">
												</div>
											</div>
										</div>
									</div>
									
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="payment_terms"><?php echo $this->lang->line("customer")." ".$this->lang->line("payment_terms");?></label>
        								        <textarea placeholder="<?php echo $this->lang->line("payment_terms");?>" name="payment_terms" id="payment_terms" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="workorder_note"><?php echo $this->lang->line("note");?></label>
        								  <textarea placeholder="<?php echo $this->lang->line("note");?>" name="workorder_note" id="workorder_note" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
					
							<div class="row">
							    
    							<input type="hidden" name="institute_id" id="institute_id" value="1">
    							<input type="hidden" name="branch_id" id="branch_id" value="1">
    							<input type="hidden" name="workorder-id" id="workorder-id" value="0">
    							<input type="hidden" name="details-id" id="details-id" value="0"> 
							
								<div class="col-sm-3 col-md-3 col-lg-3 pull-right">
								   <div class="pull-left" style="margin-bottom:20px">			
									<button type="reset" id="bill-clear" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-refresh"> <?php echo $this->lang->line("clear");?> </span></button> &nbsp;
									<button type="submit" class="btn btn-md btn-success save-bill"><span class="glyphicon glyphicon-saved"> <?php echo $this->lang->line("save");?> </span></button>
								   </div>
								</div>
							</div>
				
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->    
                               
                    
				</div> <!-- End Card --> 
          
            <?php if($hasViewOption){?>
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("customer_workorder");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
							<input type="hidden" name="src-institute_id" id="src-institute_id" value="1">
							<input type="hidden" name="src-branch_id" id="src-branch_id" value="1">						
							<div class="row">
                                
								<div class="col-sm-4 col-md-4 col-lg-4">
									<div class="form-group">
										<label class="control-label" for="src-customer_id"><?php echo $this->lang->line("customer_name");?></label>
										<select name="src-customer_id" id="src-customer_id" class="chosen-select">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("customer_name");?></option>
												<?php foreach($spquery->result() as $row){
													echo '<option value="'.$row->account_id.'">'.$row->head_id.' : '.$row->account_name.'</option>';
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-sm-2 col-md-2 col-lg-2">
									<div class="form-group">	
										<label class="control-label" for="workorder_no"><?php echo $this->lang->line("workorder_no");?></label>
										<input type="text" class="form-control" name="src-workorder_no" id="src-workorder_no" placeholder="<?php echo $this->lang->line("workorder_no");?>">
									</div>
								</div>
								<div class="col-sm-3 col-md-3 col-lg-3">                                    
									<div class="form-group">	
										<label class="control-label" for="src-date-from"><?php echo $this->lang->line("from_date");?></label>						
										
										<div class="input-group">
										<input type="text" class="form-control datepicker_nomask" name="src-date-from" id="src-date-from">
										<div class="input-group-prepend">
										  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
									</div>									
								</div>
								<div class="col-sm-3 col-md-3 col-lg-3">                                    
									<div class="form-group">	
										<label class="control-label" for="src-date-to"><?php echo $this->lang->line("to_date");?></label>						
										
										<div class="input-group">
										<input type="text" class="form-control datepicker_nomask" name="src-date-to" id="src-date-to">
										<div class="input-group-prepend">
										  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("customer_workorder")." ".$this->lang->line("list");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
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
    jQuery('.datetimepicker_mask').datetimepicker({
		 datepicker:false,
		 mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
		 format:'H:i'
	});
	//jQuery('#datetimepicker').datetimepicker();	
	
    $(document).ready( function() {
        reloadDataGrid(); 
        //$('.ItemList').hide(); 
		resizeChosen();
		jQuery(window).on('resize', resizeChosen);
		resizeGridChosen1();
		/*
		jQuery(window).on('resize', resizeGridChosen1);
		resizeGridChosen2();
		jQuery(window).on('resize', resizeGridChosen2);	
		resizeGridChosen3();
		jQuery(window).on('resize', resizeGridChosen3);
		resizeGridChosen4();
		jQuery(window).on('resize', resizeGridChosen4);
		*/
        //End Chosen Responsive
		$('#danger-alert').hide();
       
        $('#btnDelete').click(function() {
            $('#deleteModal').modal('hide');
        });
        $('#btnRowDelete').click(function() {
            $('#deleteDraftModal').modal('hide');
        });
        $('#institute_id').val("1");
        $('#branch_id').val("1");
        $('#workorder_type').val("2").trigger('chosen:updated');
    	$('#including_vat').val("0").trigger("chosen:updated");
        
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
            $(this).attr('style', 'width: 160px');
        });
    }
    function resizeGridChosen2() {
        $("#detail-item2 .chosen-container").each(function() {
            $(this).attr('style', 'width: 18px');
        });
    }
    function resizeGridChosen3() {
        $("#detail-item3 .chosen-container").each(function() {
            $(this).attr('style', 'width: 55px');
        });
    }
    function resizeGridChosen4() {
        $("#detail-item4 .chosen-container").each(function() {
            $(this).attr('style', 'width: 100px');
        });
    }
	

	$('.save-emp').click(function(){
		var employee_name	= $('#employee_name').val().replace(/&/g,'U+0026'); 
		var designation		= $('#designation').val();
		var login_id		= $('#login_id').val();
		var password		= $('#password').val();
		//alert(duration+packageId);
		if((employee_name !="" && login_id !="")){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>employee/saveAjaxEmp",
				data: "employee_name="+employee_name+"&designation="+designation+"&login_id="+login_id+"&confirm_password="+password,
				success: function(option){	
					$('#addBModal').modal('hide');
					$('#employee_id').val("");	
					$('#employee_name').val("");	
					$('#designation').val("");	
					$('#login_id').val("");	
					$('#password').val("");
					$('#salesman_id').html(option);	
					$('#salesman_id').trigger('chosen:updated');
					$('#alert').show();
					$('#alert').html('Record has been saved successfully!!!');
				}//Success
			});// ajax
		}else{
			$('#addBModal').modal('hide');
			$('#employee_id').val("");	
			$('#employee_name').val("");	
			$('#designation').val("");	
			$('#login_id').val("");	
			$('#password').val("");
			$('#danger-alert').show();
			$('#danger-alert').html('Record did not saved! Please fill data in required fields');
			return false;
		}
		return false;
	});// End save
	
	
    $('.save-detail').click(function(){
	var details_id		= parseInt($('#details-id').val());
	var workorder_id    = parseInt($('#workorder-id').val());
	var workorder_no    = $('#workorder_no').val();
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var customerId      = parseInt($('#customer_id').val());
	var category        = parseInt($('#category').val());
	var workorder_date 	= $('#workorder_date').val();
	var product_details	= $('#product-description').val().replace(/&/g,'U+0026');
	var product_sku	    = $('#product-sku').val().replace(/&/g,'U+0026');  
	var validity    	= $('#validity').val();
	var quantity		= parseInt($('#quantity').val());
	var unit_price 		= parseFloat($('#unit-price').val());
	var total_amount	= parseFloat($('#total-amount').val());
	var total_vat	    = parseFloat($('#total-vat').val());
	var total_ait	    = parseFloat($('#total-ait').val());
	var vat_percent	    = parseFloat($('#vat_percent').val());
	var ait_percent	    = parseFloat($('#ait_percent').val());
	var remarks		    = $('#remarks').val().replace(/&/g,'U+0026'); 
	var including_vat   = parseInt($('#including_vat').val()); 
	if(isNullAndUndef(category)){ category = 0; }    	
	
	if((workorder_date!="" && workorder_date!="__/__/____" && product_details!="") && (instituteId >0 && branchId >0 && customerId >0 && category >0 && quantity >0 && unit_price >=0 && including_vat >=0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>workorder/AddProduct",
			data: "details-id="+details_id+"&workorder-id="+workorder_id+"&workorder-no="+workorder_no+"&workorder-date="+workorder_date+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customerId+"&category="+category+"&product-details="+product_details+"&product-sku="+product_sku+"&quantity="+quantity+"&validity="+validity+"&unit_price="+unit_price+"&total_amount="+total_amount+"&vat_percent="+vat_percent+"&ait_percent="+ait_percent+"&total_vat="+total_vat+"&total_ait="+total_ait+"&remarks="+remarks,
			success: function(option){
			    //$('#category').val("0").trigger('chosen:updated');
				$('#product-description').val("");
				$('#product-sku').val("");
				$('#validity').val("");
				$('#quantity').val("");
				$('#unit-price').val("");				
				$('#total-amount').val("0");				
				$('#total-vat').val("0");				
				$('#total-ait').val("0");
				$('#details-id').val("0");
				$('#remarks').val("");	
				$('#danger-alert').hide();
				$('#alert').show(1000);
				$('#alert').html('Record has been added successfully!!!');
				setTimeout(function() {
				  dataSTR  = option.split("##&##");
				  dataGride= dataSTR[0];	
				  $('#ProductGrid').html(dataGride);
                  var including_vat_ait = parseInt($('#including_vat').val());
    			  total_bill= parseFloat(dataSTR[1]);
    			  if(isNullAndUndef(total_bill)){ total_bill = 0; }
    			  $('#total_bill').val(total_bill);
    			  
                  var discount_amount = parseFloat($('#discount_amount').val());
    			  if(isNullAndUndef(discount_amount)){ discount_amount = 0; }
                  var sub_total = (total_bill - discount_amount);
                  	
    			  $('#sub_total').val(sub_total);
    			
    			  TotalVat= parseFloat(dataSTR[2]);	
    			  if(isNullAndUndef(TotalVat)){ TotalVat = 0; }
    			  //TotalVat = Math.round(TotalVat);
    			  $('#vat_amount').val(TotalVat);
    			  TotalAit= parseFloat(dataSTR[3]);	
    			  if(isNullAndUndef(TotalAit)){ TotalAit = 0; }
    			  //TotalAit = Math.round(TotalAit);
    			  $('#ait_amount').val(TotalAit);
    			  
                  if(TotalVat >0){
                    vat_percentage = ((TotalVat * 100)/sub_total);
                  }else{ vat_percentage=0;}
    			  $('#vat_percentage').val(vat_percentage);
    			  var grand_total=0;
    			  if(including_vat_ait==0){
    			    var grand_total = (sub_total + TotalVat);
    			  }else{
    			    var grand_total = (sub_total - TotalVat);
    			  }
    			  $('#grand_total').val(grand_total);
                  if(TotalAit >0){
                    ait_percentage = ((TotalAit * 100)/grand_total);
                  }else{ ait_percentage=0;}
    			  $('#ait_percentage').val(ait_percentage);
    			  
    			  var net_amount=0;
    			  if(including_vat_ait==0){
    			    var net_amount = (grand_total + TotalAit);
    			  }else{
    			    var net_amount = (grand_total - TotalAit);
    			  }
    			  $('#net_amount').val(net_amount);
				}, 150);	

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
		$('#category').val("0").trigger('chosen:updated');
		$('#product-description').val("");
		$('#product-sku').val("");
		$('#validity').val("");
		$('#quantity').val("0");
		$('#unit-price').val("0");				
		$('#total-amount').val("0");				
		$('#total-vat').val("0");				
		$('#total-ait').val("0");
		$('#details-id').val("0");
		$('#remarks').val("");
	});// End clear
	
	/*
	$('.save-bill').click(function(){
	var workorder_id    = parseInt($('#workorder-id').val());
	var workorder_no    = $('#workorder_no').val();
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var customerId      = parseInt($('#customer_id').val());
	var salesman_id     = parseInt($('#salesman_id').val());
	var workorder_type 	= $('#workorder_type').val();
	var workorder_date 	= $('#workorder_date').val();
	var oem 	        = $('#oem').val().replace(/&/g,'U+0026');
	
	var total_bill      	= parseFloat($('#total_bill').val());
	var discount_percentage	= parseFloat($('#discount_percentage').val());
	var discount_amount		= parseFloat($('#discount_amount').val());
	var sub_total           = parseFloat($('#sub_total').val());
	var vat_percentage 		= parseFloat($('#vat_percentage').val());
	var vat_amount 			= parseFloat($('#vat_amount').val());
	var grand_total 		= parseFloat($('#grand_total').val());
	var ait_percentage 		= parseFloat($('#ait_percentage').val());
	var ait_amount 			= parseFloat($('#ait_amount').val());			
	var net_amount      	= parseFloat($('#net_amount').val());
	var payment_mode      	= parseInt($('#payment-mode').val());
	var oem 	            = $('#oem').val().replace(/&/g,'U+0026');
	var payment_terms 	    = $('#payment_terms').val().replace(/&/g,'U+0026');	
	var workorder_note 	    = $('#workorder_note').val().replace(/&/g,'U+0026');	
	var description			= $('#description').val().replace(/&/g,'U+0026');		
	
	if((workorder_date!="" && workorder_date!="__/__/____" && workorder_no !="") && (instituteId >0 && branchId >0 && customerId >0 && total_bill >0 && received_account>0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>workorder/SaveWorkorder",
			data: "workorder_id="+workorder_id+"&workorder_no="+workorder_no+"&workorder_type="+workorder_type+"&workorder_date="+workorder_date+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customerId+"&salesman_id="+salesman_id+"&oem="+oem+"&discount_percentage="+discount_percentage+"&discount_amount="+discount_amount+"&vat_percentage="+vat_percentage+"&vat_amount="+vat_amount+"&ait_percentage="+vat_percentage+"&ait_amount="+ait_amount+"&total_bill="+total_bill+"&sub_total="+sub_total+"&grand_total="+grand_total+"&net_amount="+net_amount+"&workorder_note="+workorder_note+"&payment_terms="+payment_terms+"&payment_mode="+payment_mode+"&description="+description,
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
	*/
	
	$('#bill-clear').click(function(){
		ClearForm();
	});// End clear
	
	function ClearForm(){		
    	$('#details-id').val("0");
    	$('#workorder-id').val("");
    	$('#workorder_no').val("");
    	$('#customer_id').val("0").trigger("chosen:updated");
    	$('#salesman_id').val("0").trigger("chosen:updated");
    	$('#workorder_type').val("1").trigger("chosen:updated");
    	$('#including_vat').val("0").trigger("chosen:updated");	
    	$('#vat_percent').val("");
    	$('#ait_percent').val("");
		$('#total-vat').val("0");				
		$('#total-ait').val("0");
    	$('#workorder_date').val("");
    	$('#delivery_date').val("");
    	$('#oem').val("");
    	
    	$('#total_bill').val("0");
    	$('#discount_percentage').val("0");
    	$('#discount_amount').val("0");
    	$('#sub_total').val("0");
    	$('#vat_percentage').val("0");
    	$('#vat_amount').val("0");
    	$('#grand_total').val("0");
    	$('#ait_percentage').val("0");
    	$('#ait_amount').val("0");	
    	$('#midman_commission').val("0");			
    	$('#net_amount').val("0");
    	$('#payment-mode').val("1").trigger("chosen:updated");
    	$('#payment_terms').val("");	
    	$('#workorder_note').val("");	
    	$('#description').val("");
	    $('#ProductGrid').html("");
	}
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#workorder-id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
		var delId = $('#workorder-id').val();
		$('#workorder-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>workorder/DelRecord",
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
    
    function deleteRow(id,workorderid=0){
        $('#details-id').val(id);
        return false;
    }
	
    $('.confirmDraft').click(function(){
	var delsId          = $('#details-id').val();
	var workorder_id    = $('#workorder-id').val();
    var instituteId     = parseInt($('#institute_id').val());
    var branchId        = parseInt($('#branch_id').val());
    var customerId      = parseInt($('#customer_id').val());
	$('#details-id').val("0");
	if(delsId >0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>workorder/DeleteRow",
			data: "id="+delsId+"&workorder-id="+workorder_id+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customerId,
			success: function(option){
			  setTimeout(function() {
			  dataSTR  = option.split("##&##");
			  dataGride= dataSTR[0];	
			  $('#ProductGrid').html(dataGride);
              var including_vat_ait = parseInt($('#including_vat').val());
			  total_bill= parseFloat(dataSTR[1]);
			  if(isNullAndUndef(total_bill)){ total_bill = 0; }
			  $('#total_bill').val(total_bill);
			  
              var discount_amount = parseFloat($('#discount_amount').val());
			  if(isNullAndUndef(discount_amount)){ discount_amount = 0; }
              var sub_total = (total_bill - discount_amount);
              	
			  $('#sub_total').val(sub_total);
			
			  TotalVat= parseFloat(dataSTR[2]);	
			  if(isNullAndUndef(TotalVat)){ TotalVat = 0; }
			  //TotalVat = Math.round(TotalVat);
			  $('#vat_amount').val(TotalVat);
			  TotalAit= parseFloat(dataSTR[3]);	
			  if(isNullAndUndef(TotalAit)){ TotalAit = 0; }
			  //TotalAit = Math.round(TotalAit);
			  $('#ait_amount').val(TotalAit);
			  
              if(TotalVat >0){
                vat_percentage = ((TotalVat * 100)/sub_total);
              }else{ vat_percentage=0;}
			  $('#vat_percentage').val(vat_percentage);
			  var grand_total=0;
			  if(including_vat_ait==0){
			    var grand_total = (sub_total + TotalVat);
			  }else{
			    var grand_total = (sub_total - TotalVat);
			  }
			  $('#grand_total').val(grand_total);
              if(TotalAit >0){
                ait_percentage = ((TotalAit * 100)/grand_total);
              }else{ ait_percentage=0;}
			  $('#ait_percentage').val(ait_percentage);
			  
			  var net_amount=0;
			  if(including_vat_ait==0){
			    var net_amount = (grand_total + TotalAit);
			  }else{
			    var net_amount = (grand_total - TotalAit);
			  }
			  $('#net_amount').val(net_amount);
			  
			  }, 150);
			  $('#alert-delete').show(1000);
			  $('#alert-delete').html('Delete successfully!!!');
			}//Success
		});// ajax
		return false;
	}
    });// End reset
    /* End Delete Data*/
    
   function isNullAndUndef(variable) {
        if(variable == null || variable == undefined || variable==""){
	  return true;
	}else if(isNaN(variable)){ return true; }	
   }
	function setlblVATAIT(including_vat_ait){
		if(including_vat_ait==1){
		    $('#lbl_vatp').html(" (Less)");
		    $('#lbl_vat').html(" (Less)");
		    $('#lbl_aitp').html(" (Less)");
		    $('#lbl_ait').html(" (Less)");
		}else{
		    $('#lbl_vatp').html(" (Add)");
		    $('#lbl_vat').html(" (Add)");
		    $('#lbl_aitp').html(" (Add)");
		    $('#lbl_ait').html(" (Add)");
		}
	}
	function calDiscountPersent(){
		var total_bill 		= parseFloat($('#total_bill').val());
		var discount_percentage = parseFloat($('#discount_percentage').val());
		if(isNullAndUndef(total_bill)){ total_bill = 0;}
		if(isNullAndUndef(discount_percentage)){ discount_percentage = 0;}
		if(total_bill >0){
		var discount_amount	= ((total_bill/100) * discount_percentage);
		}else{
		var discount_amount	= 0;	
		}
		$('#discount_amount').val(discount_amount);
		var sub_total = (total_bill - discount_amount);
		if(isNullAndUndef(sub_total)){ sub_total = 0;}
		sub_total = Math.round(sub_total);
		$('#sub_total').val(sub_total);
		
		var including_vat_ait 	= parseInt($('#including_vat').val());		
		if(isNullAndUndef(including_vat_ait)){ including_vat_ait = 0;}

		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		var vat_amount 	   = parseFloat($('#vat_amount').val());
		if(isNullAndUndef(vat_amount)){ vat_amount = 0;}
		if(vat_percentage >0 && vat_amount==0){
			vat_amount = ((sub_total/100) * vat_percentage); 
			if(isNullAndUndef(vat_amount)){ vat_amount = 0; }
			vat_amount = Math.round(vat_amount);
			if(including_vat_ait==0){
			  var net_payble = (sub_total + vat_amount);
			}else{
			  var net_payble = (sub_total - vat_amount);
			}
			
			
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
			if(including_vat_ait==0){
			  var net_payble = (sub_total + vat_amount);
			}else{
			  var net_payble = (sub_total - vat_amount);
			}		
			
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#net_amount').val(net_payble);
		}		
	}
	
	function getSubTotalBill(){
		var total_bill 		= parseFloat($('#total_bill').val());
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
		
		var including_vat_ait 	= parseInt($('#including_vat').val());		
		if(isNullAndUndef(including_vat_ait)){ including_vat_ait = 0;}

		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		var vat_amount 			= parseFloat($('#vat_amount').val());
		if(isNullAndUndef(vat_amount)){ vat_amount = 0;}
		if(vat_percentage >0 && vat_amount==0){
			vat_amount = ((sub_total/100) * vat_percentage); 
			if(isNullAndUndef(vat_amount)){ vat_amount = 0; }
			vat_amount = Math.round(vat_amount);
			
			if(including_vat_ait==0){
			  var net_payble = (sub_total + vat_amount);
			}else{
			  var net_payble = (sub_total - vat_amount);
			}
			
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
						
			if(including_vat_ait==0){
			  var net_payble = (sub_total + vat_amount);
			}else{
			  var net_payble = (sub_total - vat_amount);
			}
			
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
		var including_vat_ait 	= parseInt($('#including_vat').val());		
		if(isNullAndUndef(including_vat_ait)){ including_vat_ait = 0;}
		
		if(sub_total==0){
			var total_bill 		= parseFloat($('#total_bill').val());
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
						
		if(including_vat_ait==0){
		  var grand_total = (sub_total + vat_amount);
		}else{
		  var grand_total = (sub_total - vat_amount);
		}
		if(isNullAndUndef(grand_total)){ grand_total = 0;} grand_total = Math.round(grand_total);
		$('#grand_total').val(grand_total);
		$('#net_amount').val(grand_total);				
	}
	
	
	function calAitPersent(){		
		var grand_total    	    = parseFloat($('#grand_total').val());
		var ait_percentage 	    = parseFloat($('#ait_percentage').val());
		var including_vat_ait 	= parseInt($('#including_vat').val());		
		if(isNullAndUndef(including_vat_ait)){ including_vat_ait = 0;}
		if(isNullAndUndef(grand_total)){ grand_total = 0;}
		if(isNullAndUndef(ait_percentage)){ ait_percentage = 0;}
		
		if(grand_total >0){
		var ait_amount		= ((grand_total/100) * ait_percentage);
		}else{
		var ait_amount		= 0;	
		}
		
		if(grand_total==0){
			var sub_total 		= parseFloat($('#sub_total').val());
			if(isNullAndUndef(sub_total)){ sub_total = 0;}
			if(sub_total==0){
			    var total_bill 	= parseFloat($('#total_bill').val()); 
			   
    			    var discount_percentage = parseFloat($('#discount_percentage').val());
    			    if(isNullAndUndef(discount_percentage)){ discount_percentage = 0;}
    			    var discount_amount     = parseFloat($('#discount_amount').val());		
    			    if(isNullAndUndef(discount_amount)){ discount_amount = 0;}
    			
    			    if(discount_percentage >0 && discount_amount==0){
    				discount_amount = ((total_bill/100) * discount_percentage); 
    				if(isNullAndUndef(discount_amount)){ discount_amount = 0; }
    				discount_amount = Math.round(discount_amount);				
    				var sub_total = (total_bill - discount_amount);
    				
    				if(isNullAndUndef(sub_total)){ sub_total = 0; } 
				    sub_total = Math.round(sub_total);
    				$('#discount_amount').val(discount_amount);
    				$('#sub_total').val(sub_total);
    				
    				if(sub_total >0){
    				  var ait_amount	= ((sub_total/100) * ait_percentage);
    				}else{
    				  var ait_amount	= ((total_bill/100) * ait_percentage);	
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
    				
    				if(isNullAndUndef(sub_total)){ sub_total = 0; } 
				    sub_total = Math.round(sub_total);
    				$('#sub_total').val(sub_total);
    				
    				if(sub_total >0){
    				  var ait_amount	= ((sub_total/100) * vat_percentage);
    				}else{
    				  var ait_amount	= ((total_bill/100) * ait_percentage);	
    				}
    			    }
			}else{
			    var ait_amount		= ((sub_total/100) * ait_percentage);
			}
		}		
		$('#ait_amount').val(ait_amount);
		
		if(including_vat_ait==0){
		  var net_amount  = (grand_total + ait_amount);
		}else{
		  var net_amount  = (grand_total - ait_amount);
		}
		if(isNullAndUndef(net_amount)){ net_amount = 0;} net_amount = Math.round(net_amount);
		$('#net_amount').val(net_amount);				
	}
	function getNetPayble(){
		var total_bill 		    = parseFloat($('#total_bill').val());
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
		var including_vat_ait 	= parseInt($('#including_vat').val());		
		if(isNullAndUndef(including_vat_ait)){ including_vat_ait = 0;}
		var vat_percentage = parseFloat($('#vat_percentage').val());
		if(isNullAndUndef(vat_percentage)){ vat_percentage = 0;}
		var vat_amount 	   = parseFloat($('#vat_amount').val());
		if(isNullAndUndef(vat_amount)){ vat_amount = 0;}
		if(vat_percentage >0 && vat_amount==0){
			vat_amount = ((sub_total/100) * vat_percentage); 
			if(isNullAndUndef(vat_amount)){ vat_amount = 0; }
			vat_amount = Math.round(vat_amount);
			if(including_vat_ait==0){
			  var net_payble = (sub_total + vat_amount);
			}else{
			  var net_payble = (sub_total - vat_amount);
			}
			
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
			if(including_vat_ait==0){
			  var grand_total = (sub_total + vat_amount);
			}else{
			  var grand_total = (sub_total - vat_amount); 
			}
			
			if(isNullAndUndef(grand_total)){ grand_total = 0; } grand_total = Math.round(grand_total);
			$('#grand_total').val(grand_total);
			$('#net_amount').val(grand_total);
		}
		
		if(grand_total==0){
		    var sub_total = parseFloat($('#sub_total').val());
		    if(sub_total==0){
		        var total_bill = parseFloat($('#total_bill').val());
		        sub_total = total_bill;
		        grand_total = total_bill;
		    }
		    grand_total = sub_total;
		}
		
		var ait_percentage = parseFloat($('#ait_percentage').val());
		if(isNullAndUndef(ait_percentage)){ ait_percentage = 0;}
		var ait_amount 	   = parseFloat($('#ait_amount').val());
		if(isNullAndUndef(ait_amount)){ ait_amount = 0;}
		if(ait_percentage >0 && ait_amount==0){		    
			ait_amount = ((grand_total/100) * ait_percentage); 
			if(isNullAndUndef(ait_amount)){ ait_amount = 0; }
			ait_amount = Math.round(ait_amount);
			if(including_vat_ait==0){
			  var net_payble = (grand_total + ait_amount);
			}else{
			  var net_payble = (grand_total - ait_amount);
			}			
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#ait_amount').val(ait_amount);
			$('#net_amount').val(net_payble);
		}else{
			ait_amount = Math.round(ait_amount);
			if(ait_amount >0){
			  ait_percentage = ((ait_amount * 100)/grand_total);
			}else{ ait_amount =0; ait_percentage=0;}
			
			if(isNullAndUndef(ait_percentage)){ ait_percentage = 0; }
			ait_percentage = ait_percentage.toFixed(4);
			$('#ait_percentage').val(ait_percentage);
			$('#ait_amount').val(ait_amount);
			if(including_vat_ait==0){
			  var net_payble = (grand_total + ait_amount);
			}else{
			  var net_payble = (grand_total - ait_amount);
			}
			
			if(isNullAndUndef(net_payble)){ net_payble = 0; } net_payble = Math.round(net_payble);
			$('#net_amount').val(net_payble);
		}
	}
	
	function calTotalAmount(){
	    var total_vat=0; var total_ait=0;
		var unit_price  = parseFloat($('#unit-price').val());
		var quantity    = parseFloat($('#quantity').val());
		var total_bill  = (unit_price * quantity);
		var vat_percent = parseFloat($('#vat_percent').val());
		var ait_percent = parseFloat($('#ait_percent').val());
		total_vat = ((total_bill/100) * vat_percent);
		total_ait = (((total_bill-total_vat)/100) * ait_percent);
		$('#total-vat').val(total_vat);
		$('#total-ait').val(total_ait);
		$('#total-amount').val(total_bill);
	}
	
    function editRow(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>workorder/FillDetails",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#category').val(rsStr[1]).trigger('chosen:updated');
            $('#product-description').val(rsStr[2]);
            $('#product-sku').val(rsStr[3]);
            $('#unit-price').val(rsStr[4]);
            $('#quantity').val(rsStr[5]);
            $('#validity').val(rsStr[6]);
            $('#total-amount').val(rsStr[7]);
            $('#vat_percent').val(rsStr[8]);
            $('#total-vat').val(rsStr[9]);
            $('#ait_percent').val(rsStr[10]);
            $('#total-ait').val(rsStr[11]);
            $('#remarks').val(rsStr[12]);		
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
            url: "<?php echo base_url();?>workorder/FillRecord",
            data: "id="+id,
            success: function(option){
            	//alert(option);
			ArrStr = option.split("@@##@@");
			rsStr  = ArrStr[0].split("##&##");
			//===== Bill List Grid =====
			//var StudentDropdown = ArrStr[1];
            	//alert(rsStr[1]);
            		
        	$('#details-id').val("0");
        	$('#workorder-id').val(rsStr[1]);
        	$('#workorder_no').val(rsStr[2]);
        	$('#customer_id').val(rsStr[3]).trigger("chosen:updated");
        	$('#salesman_id').val(rsStr[4]).trigger("chosen:updated");
        	$('#workorder_type').val(rsStr[5]).trigger("chosen:updated");
        	$('#workorder_date').val(rsStr[6]);
        	$('#oem').val(rsStr[7]);
        	
        	$('#total_bill').val(rsStr[8]);
        	$('#discount_percentage').val(rsStr[9]);
        	$('#discount_amount').val(rsStr[10]);
        	$('#sub_total').val(rsStr[11]);
        	$('#vat_percentage').val(rsStr[12]);
        	$('#vat_amount').val(rsStr[13]);
        	$('#grand_total').val(rsStr[14]);
        	$('#ait_percentage').val(rsStr[15]);
        	$('#ait_amount').val(rsStr[16]);			
        	$('#net_amount').val(rsStr[17]);
        	$('#payment-mode').val(rsStr[18]).trigger("chosen:updated");
        	$('#payment_terms').val(rsStr[19]);	
        	$('#workorder_note').val(rsStr[20]);	
        	$('#description').val(rsStr[21]);
		    $('#institute_id').val(rsStr[22]);	
		    $('#branch_id').val(rsStr[23]);
        	$('#delivery_date').val(rsStr[24]);
        	$('#midman_commission').val(rsStr[25]);
        	$('#including_vat').val(rsStr[26]).trigger("chosen:updated");
		    //==== End for travel company ===
		    getProductList(rsStr[1]);		
            	$('.show-create').show();
            	$('#alert').show();
            	$('#alert').html('Ready to Edit!');
            }//Success
        });// ajax
        return false;
    }
    function getProductList(workorder_id=0){		
        var instituteId     = parseInt($('#institute_id').val());
        var branchId        = parseInt($('#branch_id').val());
		var customerId		= parseInt($('#customer_id').val());
		if(workorder_id==0){ var workorder_id	= parseInt($('#workorder-id').val()); }
		
		var workorder_date 	= $('#workorder_date').val();
    
		if(instituteId >0 && branchId >0 && customerId >0 && workorder_date !=""){
		 $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>workorder/GetProductList",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&workorder_date="+workorder_date+"&customer_id="+customerId+"&workorder-id="+workorder_id,
            success: function(option){
				  dataSTR  = option.split("##&##");
				  dataGride= dataSTR[0];	
				  $('#ProductGrid').html(dataGride);
				  total_bill= parseFloat(dataSTR[1]);	
				  $('#total_bill').val(total_bill);
				  
				  TotalVat= parseFloat(dataSTR[2]);	
				  $('#vat_amount').val(TotalVat);
				  TotalAit= parseFloat(dataSTR[3]);	
				  $('#ait_amount').val(TotalAit);
            }//Success
         });// End datagrid
		}
	}
	function reloadDataGrid(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>workorder/GetRecords",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    
	$('.search').click(function(){
    	var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var workorder_no    = $('#src-workorder_no').val();
        var customerId      = $('#src-customer_id').val();
	    var srcFrom	    = $('#src-date-from').val();
	    var srcTo	    = $('#src-date-to').val();
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>workorder/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-workorder="+workorder_no+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&src-customer="+customerId,
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
        var workorder_no    = $('#src-workorder_no').val();
        var customerId      = $('#src-customer_id').val();
	    var srcFrom	    = $('#src-date-from').val();
	    var srcTo	    = $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>workorder/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-workorder="+workorder_no+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&src-customer="+customerId+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }	
	
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
		
    </script>
</body>
</html>
