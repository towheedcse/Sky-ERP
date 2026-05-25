<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("customer_po");?> </title>
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
               <?php echo $this->lang->line("customer_po");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("customer_po");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                    <div class="card card-primary show-create">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("customer_po");?></h3>
						<div class="card-tools">
						  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
						</div>
					</div> <!-- /.card-header -->  
                    <div class="card-body">                        						    
						<div id="alert" class="alert alert-success"></div>
						<div id="danger-alert" class="alert alert-danger"></div>
                        <div class="container-fluid">
                            <form method="post" id="InputForm" enctype="multipart/form-data">
							<div class="card card-info card-outline">							
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("customer_po")." ".$this->lang->line("details");?></h3><hr>								
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
												<label class="control-label" for="po_no"><?php echo $this->lang->line("po_no");?></label>
												<input type="text" class="form-control" name="po_no" id="po_no" placeholder="<?php echo $this->lang->line("po_no");?>">
											</div>								
										</div> 
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">	
												<label class="control-label" for="po_type"><?php echo $this->lang->line("po_type");?></label>			
											    <select name="sales_type" id="sales_type" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("po_type");?></option>
													<option value="1">Fresh</option>
													<option value="2">Renual</option>
													<option value="3">Addon</option>
												</select>
											</div>								
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">	
												<label class="control-label" for="sales_date"><?php echo $this->lang->line("po_date");?></label>			
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="sales_date" id="sales_date">
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
												<select name="salesman_id" id="salesman_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("sales_person");?></option>
														<?php foreach($smquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">	
												<label class="control-label" for="oem"><?php echo $this->lang->line("oem");?></label>
												<input type="text" class="form-control" name="oem" id="oem" placeholder="<?php echo $this->lang->line("oem");?>">
											</div>	
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">	
												<label class="control-label" for="delivery_date"><?php echo $this->lang->line("delivery_date");?></label>			
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="delivery_date" id="delivery_date">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
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
											<td class="active" id="detail-item4">
											<input type="hidden" name="particulars-id" id="particulars-id" value="856">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("product_description");?>" name="product-description" id="product-description" class="form-control" style="min-width:180px">
											</td>
											<td class="active" id="detail-item1">
											
											<input type="text" placeholder="<?php echo $this->lang->line("product_sku");?>" name="product-sku" id="product-sku" class="form-control" style="width:110px">
											</td>							    
											<td class="active" id="detail-item2">
											<input type="hidden" name="cost_price" id="cost_price" value="0">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("validity");?>" name="validity" id="validity" class="form-control" style="width: 76px;">								
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("quantity");?>" name="quantity" id="quantity" onKeyUp="calTotalAmount()" class="form-control" style="width: 85px;"/>
											</td>					    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("unit_price");?>" name="unit-price" id="unit-price" onKeyUp="calTotalAmount()" class="form-control" style="width:90px">
											</td>						    
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("total");?>" name="total-amount" id="total-amount" class="form-control" style="width:95px">
											
											</td>
											<td class="active" id="detail-item2">
											<input type="text" required="" placeholder="<?php echo $this->lang->line("remarks");?>" name="remarks" id="remarks" class="form-control" style="min-width:120px">
											
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
												<label class="control-label" for="total_bill"><?php echo $this->lang->line("total")." ".$this->lang->line("po_value");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("total")." ".$this->lang->line("po_value");?>" name="total_bill" id="total_bill" class="form-control" value="0" readonly> 
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
												<label class="control-label" for="Payment Mode">Payment Mode:</label>
												<select name="payment-mode" id="payment-mode" class="chosen-select" onChange='showHideBank(this.value)'>
													<option value="1">Cash</option>	
													<option value="2">Cheque</option>	
													<option value="3">Challan</option>	
													<option value="4">bKash</option>	
													<option value="10">Others</option>	
												</select>																
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
												<label class="control-label" for="grand_total"><?php echo $this->lang->line("grand_total");?></label>
												<input type="text" readonly required="" placeholder="<?php echo $this->lang->line("grand_total");?>" name="grand_total" id="grand_total" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("po_attach");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="po_attach" id="po_attach" class="form-control" style="height: 25px;">
														</span>
														<div style="padding-left:3px; width:4.1%; float:right">
													  <span data-toggle="tooltip" data-placement="top" title="PDF Document">
													  </span>
													  </div>
													</span>
													<input type="text" class="form-control" readonly>
												</div>
											</div>
											<input type="hidden" required="" placeholder="<?php echo $this->lang->line("description");?>" name="description" id="description" class="form-control" value="">
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="ait_percentage"><?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> +</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> " name="ait_percentage" id="ait_percentage" class="form-control" onKeyUp="calAITPersent()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="ait_amount"><?php echo $this->lang->line("ait")." ".$this->lang->line("amount");?> +</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("ait")." ".$this->lang->line("amount");?> " name="ait_amount" id="ait_amount" class="form-control" onKeyUp="getNetPayble()">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="net_amount"><?php echo $this->lang->line("net_bill")." ".$this->lang->line("receivable");?></label>
												<input type="text" readonly required="" placeholder="<?php echo $this->lang->line("net_bill")." ".$this->lang->line("receivable");?>" name="net_amount" id="net_amount" class="form-control">
											</div>
										</div>
										
											    <input type="hidden" required="" placeholder="<?php echo $this->lang->line("paid_amount");?> " name="paid_amount" id="paid_amount" class="form-control" onKeyUp="calRemainAmount(this.value)">
											    <input type="hidden" readonly required="" placeholder="<?php echo $this->lang->line("due_amount");?>" name="due_amount" id="due_amount" class="form-control">
											
									</div>
									
									
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="payment_terms"><?php echo $this->lang->line("payment_terms");?></label>
        								        <textarea placeholder="<?php echo $this->lang->line("payment_terms");?>" name="invoice_note3" id="invoice_note3" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
									<div class="row">
        								<div class="col-sm-6 col-md-6 col-lg-6 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="invoice_note1"><?php echo $this->lang->line("note");?></label>
        								  <input type="text" placeholder="Invoice Note 1" name="invoice_note1" id="invoice_note1" class="form-control" value="">
        								    </div>
        								</div>
        								
        								<div class="col-sm-6 col-md-6 col-lg-6 pull-right">
        								    <div class="form-group">
												<label class="control-label" for="invoice_note2"><br></label>
        								  <input type="text" placeholder="Invoice Note 2" name="invoice_note2" id="invoice_note2" class="form-control" value="">
        								    </div>
        								</div>
        							</div>
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
					
							<div class="row">
							    
    							<input type="hidden" name="institute_id" id="institute_id" value="1">
    							<input type="hidden" name="branch_id" id="branch_id" value="1">
    							<input type="hidden" name="session_id" id="session_id" value="4">
    							<input type="hidden" name="store_id" id="store_id" value="4">
							
								<div class="col-sm-3 col-md-3 col-lg-3 pull-right">
								   <div class="pull-left" style="margin-bottom:20px">			
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
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("customer_po");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
							<input type="hidden" name="src-institute_id" id="src-institute_id" value="1">
							<input type="hidden" name="src-branch_id" id="src-branch_id" value="1">
							<input type="hidden" name="src-session_id" id="src-session_id" value="4">
							<input type="hidden" name="src-store_id" id="src-store_id" value="4">							
							<div class="row">
                                
								<div class="col-sm-4 col-md-4 col-lg-4">
									<div class="form-group">
										<label class="control-label" for="src-customer_name"><?php echo $this->lang->line("customer_name");?></label>
										<select name="src-customer_name" id="src-customer_name" class="chosen-select" required="">
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
										<label class="control-label" for="po_no"><?php echo $this->lang->line("po_no");?></label>
										<input type="text" class="form-control" name="src-po_no" id="src-po_no" placeholder="<?php echo $this->lang->line("po_no");?>">
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("customer_po")." ".$this->lang->line("list");?></h3>
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
		jQuery(window).on('resize', resizeGridChosen1);
		resizeGridChosen2();
		jQuery(window).on('resize', resizeGridChosen2);	
		resizeGridChosen3();
		jQuery(window).on('resize', resizeGridChosen3);
		resizeGridChosen4();
		jQuery(window).on('resize', resizeGridChosen4);
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
        $('#session_id').val("4").trigger('chosen:updated');
        $('#store_id').val("4");
        $('#sales_type').val("2").trigger('chosen:updated');
        
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
            $(this).attr('style', 'width: 168px');
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
            $(this).attr('style', 'width: 115px');
        });
    }
	
    $('.save-detail').click(function(){
	var details_id		= parseInt($('#details-id').val());
	var bill_id			= parseInt($('#bill-id').val());
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var customerId      = parseInt($('#customer_id').val());
	var supplierId      = parseInt($('#supplier_id').val());
	var applicantId     = parseInt($('#applicant_id').val());
	var sessionId       = parseInt($('#session_id').val());
	var storeId         = parseInt($('#store_id').val());
	var sales_date 		= $('#sales_date').val();
    var fee_period      = parseInt(sales_date.substring(3, 5));
	var particularsId	= parseInt($('#particulars-id').val());
	var quantity		= parseInt($('#quantity').val());
	var stock_qty		= parseInt($('#stock_qty').val());
	var unit_price 		= parseFloat($('#unit-price').val());
	var total_amount	= parseFloat($('#total-amount').val());
	
	var cost_price		=  parseFloat($('#cost_price').val()); 
	var remarks		    = $('#remarks').val().replace(/&/g,'U+0026');      	
	
	if((sales_date!="" && sales_date!="__/__/____") && (instituteId >0 && branchId >0 && customerId >0 && sessionId >0 && storeId >0 && particularsId >0 && quantity >0 && unit_price >0 && cost_price >0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>sales/AddBill",
			data: "details-id="+details_id+"&bill-id="+bill_id+"&bill-date="+sales_date+"&bill-period="+fee_period+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customerId+"&applicant_id="+applicantId+"&supplier_id="+supplierId+"&session_id="+sessionId+"&store_id="+storeId+"&particulars-id="+particularsId+"&quantity="+quantity+"&stock_qty="+stock_qty+"&unit_price="+unit_price+"&total_amount="+total_amount+"&cost_price="+cost_price+"&remarks="+remarks,
			success: function(option){
				$('#particulars-id').val("").trigger('chosen:updated');
				$('#quantity').val("");
				$('#stock_qty').val("0");
				$('#unit-price').val("");				
				$('#total-amount').val("0");				
				$('#cost_price').val("0");
				$('#details-id').val("0");
				$('#remarks').val("");	
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
		$('#stock_qty').val("");
		$('#unit-price').val("");				
		$('#total-amount').val("0");				
		$('#cost_price').val("0");
		$('#details-id').val("0");
		$('#remarks').val("");
	});// End clear
	
	$('.save-bill').click(function(){
	var details_id		= parseInt($('#details-id').val());
	var bill_id			= parseInt($('#bill-id').val());
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var customerId      = parseInt($('#customer_id').val());
	var sessionId       = parseInt($('#session_id').val());
	var storeId         = parseInt($('#store_id').val());
	var sales_date 	    = $('#sales_date').val();
    var fee_period      = parseInt(sales_date.substring(3, 5));
	
	var discount_percentage	= parseFloat($('#discount_percentage').val());
	var discount_amount		= parseFloat($('#discount_amount').val());
	var vat_percentage 		= parseFloat($('#vat_percentage').val());
	var vat_amount 			= parseFloat($('#vat_amount').val());		
	var total_bill      	= parseFloat($('#total_bill').val());		
	var net_amount      	= parseFloat($('#net_amount').val());		
	var invoice_note1      	= $('#invoice_note1').val();		
	var invoice_note2      	= $('#invoice_note2').val();
	var applicant_id        = parseInt($('#applicant_id').val());
	var sales_type          = parseInt($('#sales_type').val());
	var refund_type         = parseInt($('#refund_type').val());
	var trip_type           = parseInt($('#trip_type').val());
	var travel_date         = $('#travel_date').val();
	var depart_place        = $('#depart_place').val();
	var arrival_place       = $('#arrival_place').val();
	
	var depart_datetime     = $('#depart_datetime').val();
	var arraival_datetime   = $('#arraival_datetime').val();
	var pnr_no              = $('#pnr_no').val();
	var airline             = $('#airline').val();
	var payment_mode      	= parseInt($('#payment-mode').val());		
	var received_account    = parseInt($('#received_account').val());		
	var deposit_amount      = parseFloat($('#deposit_amount').val());			
	var due_amount      	= parseFloat($('#due_amount').val());		
	var paid_amount      	= parseFloat($('#paid_amount').val());		
	var remaining_amount    = parseFloat($('#remaining_amount').val());		
	var description			= $('#description').val().replace(/&/g,'U+0026');		
	var invoice_note3		= $('#invoice_note3').val().replace(/&/g,'U+0026');		
	
	if((sales_date!="" && sales_date!="__/__/____") && (instituteId >0 && branchId >0 && customerId >0 && applicant_id>0 && sessionId >0 && storeId >0 && fee_period >0 && total_bill >0 && received_account>0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>sales/SaveBill",
			data: "details-id="+details_id+"&bill-id="+bill_id+"&bill-date="+sales_date+"&bill-period="+fee_period+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customerId+"&session_id="+sessionId+"&store_id="+storeId+"&discount_percentage="+discount_percentage+"&discount_amount="+discount_amount+"&vat_percentage="+vat_percentage+"&vat_amount="+vat_amount+"&total_bill="+total_bill+"&net_amount="+net_amount+"&invoice_note1="+invoice_note1+"&invoice_note2="+invoice_note2+"&invoice_note3="+invoice_note3+"&payment_mode="+payment_mode+"&received_account="+received_account+"&deposit_amount="+deposit_amount+"&due_amount="+due_amount+"&paid_amount="+paid_amount+"&remaining_amount="+remaining_amount+"&description="+description+"&applicant_id="+applicant_id+"&sales_type="+sales_type+"&refund_type="+refund_type+"&trip_type="+trip_type+"&travel_date="+travel_date+"&depart_place="+depart_place+"&arrival_place="+arrival_place+"&depart_datetime="+depart_datetime+"&arraival_datetime="+arraival_datetime+"&pnr_no="+pnr_no+"&airline="+airline,
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
		$('#customer_id').val("0").trigger("chosen:updated");
		$('#sales_type').val("2").trigger("chosen:updated");
		$('#sales_date').val("");
		$('#travel_date').val("");
		$('#depart_place').val("");
	    $('#arrival_place').val("");
		$('#depart_datetime').val("");
		$('#arraival_datetime').val("");
		$('#pnr_no').val("");
		$('#airline').val("");
		$('#description').val("");
		$('#discount_percentage').val("0");
		$('#discount_amount').val("0");
		$('#vat_percentage').val("0");
		$('#vat_amount').val("0");
		$('#total_bill').val("0");
		$('#net_amount').val("0");
		$('#payment-mode').val("1");
		$('#received_account').val("0").trigger("chosen:updated");
		$('#deposit_amount').val("0");	
		$('#paid_amount').val("0");	
		$('#due_amount').val("0");
		$('#remaining_amount').val("0");
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
				url: "<?php echo base_url();?>sales/DelRecord",
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
    
    function deleteRow(id){
        $('#details-id').val(id);
        return false;
    }
	
    $('.confirmDraft').click(function(){
		var delsId          = $('#details-id').val();
		var bill_id         = $('#bill-id').val();
    	var instituteId     = parseInt($('#institute_id').val());
    	var branchId        = parseInt($('#branch_id').val());
    	var customerId      = parseInt($('#customer_id').val());
    	var sessionId       = parseInt($('#session_id').val());
    	var sales_date 		= $('#sales_date').val();
        var fee_period      = parseInt(sales_date.substring(3, 5));
		$('#details-id').val("0");
		if(delsId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>sales/DeleteRow",
				data: "id="+delsId+"&bill-id="+bill_id+"&institute_id="+instituteId+"&branch_id="+branchId+"&session_id="+sessionId+"&customer_id="+customerId+"&bill-period="+fee_period,
				success: function(option){
				    setTimeout(function() {
    				  dataSTR  = option.split("##&##");
    				  dataGride= dataSTR[0];	
    				  $('#FeesGrid').html(dataGride);
    				  total_bill= dataSTR[1];	
    				  $('#total_bill').val(total_bill);
    				}, 100);
					$('#alert-delete').show(1000);
					$('#alert-delete').html('Delete successfully!!!');
				}//Success
			});// ajax
			return false;
		}
    });// End reset
    /* End Delete Data*/	    	
    function getProductSalesPrice(particulars_id){		
		var instituteId     = parseInt($('#institute_id').val());
		var branchId        = parseInt($('#branch_id').val());
		var sessionId       = parseInt($('#session_id').val());
		var storeId         = parseInt($('#store_id').val()); 
        if(instituteId >0 && branchId >0 && particulars_id >0 && storeId >0){			
    	   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>sales/loadProductSalesPrice",
			data: "institute_id="+instituteId+"&branch_id="+branchId+"&session_id="+sessionId+"&store_id="+storeId+"&particulars-id="+particulars_id,
			success: function(option){				
				rsStr  = option.split("##&##");
				//alert(rsStr[1]);	
				if(parseInt(rsStr[0])>=0){
					$('#unit-price').val(rsStr[0]);
					$('#stock_qty').val(rsStr[1]);
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
    function getBalanceAmount(supplierId,cost_price=0){
        if(supplierId >0){
    	   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>sales/loadSupplierBalance",
			data: "supplier_id="+supplierId+"&cost_price="+cost_price,
			success: function(option){				
				rsStr  = option.split("##&##");
				//alert(rsStr[1]);	
				if(parseInt(rsStr[0])>0){
				    if(rsStr[1]>0){ 
				       var balance = (parseFloat(rsStr[1]) + parseFloat(rsStr[0])); 
				       $('#stock_qty').val(balance);
				    }else{
					    $('#stock_qty').val(rsStr[0]);
				    }
					$('#cost_price').select();
					$('#cost_price').focus();
				}else{					
					$('#stock_qty').val("0");
					$('#supplier_id').focus();
				}
                }//Success
           });// ajax
		}
        return false;
    }
    
    function getAgentBalanceAmount(agentId,deposit_amount=0){
        if(agentId >0){
    	   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>sales/loadSupplierBalance",
			data: "supplier_id="+agentId+"&cost_price="+deposit_amount,
			success: function(option){				
				rsStr  = option.split("##&##");
				var AgentBalance =0;
				//alert(rsStr[1]);	
				/*
				AgentBalance = parseFloat(Math.abs(rsStr[0]));
				if(parseInt(rsStr[0]) <= 0){
				    if(rsStr[1]>0){ 
				       var balance = (parseFloat(rsStr[1]) + AgentBalance); 
				       $('#deposit_amount').val(balance);
				    }else{
					    $('#deposit_amount').val(AgentBalance);
				    }
					$('#sales_type').select();
					$('#sales_type').focus();
				}else{					
					$('#deposit_amount').val("0");
					$('#sales_type').focus();
				}
				*/
				
				$('#deposit_amount').val("0");
				
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
			$('#paid_amount').val(net_payble);
			$('#due_amount').val(0);
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
			$('#paid_amount').val(net_payble);
			$('#due_amount').val(0);
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
			$('#paid_amount').val(net_payble);
			$('#due_amount').val(0);
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
			$('#paid_amount').val(net_payble);
			$('#due_amount').val(0);
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
		$('#paid_amount').val(net_payble);
		$('#due_amount').val(0);				
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
			$('#paid_amount').val(net_payble);
			$('#due_amount').val(0);
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
			$('#paid_amount').val(net_payble);
			$('#due_amount').val(0);
		}		
	}
	
	function calTotalAmount(){
		var unit_price = parseFloat($('#unit-price').val());
		var quantity   = parseFloat($('#quantity').val());
		var total_bill = (unit_price * quantity);
		$('#total-amount').val(total_bill);
	}
	function calDueAmount(){
	    var net_amount      = parseFloat($('#net_amount').val());
		var deposit_amount  = parseFloat($('#deposit_amount').val());
		if(isNullAndUndef(net_amount)){ net_amount = 0; }
		if(isNullAndUndef(deposit_amount)){ deposit_amount = 0; }
		var due_amount =0;
		if(deposit_amount >= net_amount && deposit_amount>0 && net_amount >0){
			due_amount = 0;
		}else if(deposit_amount < net_amount && deposit_amount>0 && net_amount >0){
			due_amount = (net_amount - deposit_amount);
		}else if(deposit_amount==0 && net_amount >0){
			due_amount = net_amount;
		}
		if(due_amount==0 && deposit_amount >0){
			$('#paid_amount').val(0);
		}
		$('#due_amount').val(due_amount);
		calRemainAmount($('#paid_amount').val());
	}
	
	function calRemainAmount(PaidAmount){
		var paid_amount     = parseFloat(PaidAmount);
		var due_amount      = parseFloat($('#due_amount').val());
		var deposit_amount  = parseFloat($('#deposit_amount').val());
		var net_amount      = parseFloat($('#net_amount').val());
		if(isNullAndUndef(due_amount)){ due_amount = 0; }
		if(isNullAndUndef(paid_amount)){ paid_amount = 0; }
		if(isNullAndUndef(deposit_amount)){ deposit_amount = 0; }
		if(isNullAndUndef(net_amount)){ net_amount = 0; }
		var remaining_amount =0;
		if(due_amount >=paid_amount && paid_amount>0 && due_amount >0){
			remaining_amount = (due_amount - paid_amount);
		}else if(paid_amount==0 && due_amount >0){
			remaining_amount = due_amount;
		}else if(deposit_amount>=net_amount){
			remaining_amount = 0;
		}
		$('#remaining_amount').val(remaining_amount);		
	}
    function editRow(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>sales/FillDetails",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#particulars-id').val(rsStr[1]);
            $('#particulars-id').trigger("chosen:updated");
            $('#unit-price').val(rsStr[2]);
            $('#quantity').val(rsStr[3]);
            $('#stock_qty').val(rsStr[4]);
            $('#total-amount').val(rsStr[5]);
            $('#remarks').val(rsStr[6]);
            $('#supplier_id').val(rsStr[7]);
            $('#supplier_id').trigger("chosen:updated");
            getBalanceAmount(rsStr[7],rsStr[8]);
            $('#cost_price').val(rsStr[8]);
            $('#applicant_id').val(rsStr[9]).trigger("chosen:updated");			
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
            url: "<?php echo base_url();?>sales/FillRecord",
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
			$('#institute_id').val(rsStr[2]);	
			$('#branch_id').val(rsStr[3]);		
			$('#customer_id').val(rsStr[4]).trigger("chosen:updated");
			$('#session_id').val(rsStr[5]);
			$('#store_id').val(rsStr[6]);
			$('#fee_period').val(rsStr[7]);
			$('#sales_date').val(rsStr[8]);			
			$('#discount_percentage').val(rsStr[9]);
			$('#discount_amount').val(rsStr[10]);			
			$('#vat_percentage').val(rsStr[11]);
			$('#vat_amount').val(rsStr[12]);
			$('#total_bill').val(rsStr[13]);
			$('#net_amount').val(rsStr[14]);
			$('#description').val(rsStr[15]);
			$('#payment-mode').val(rsStr[16]).trigger("chosen:updated");
			$('#received_account').val(rsStr[17]).trigger("chosen:updated");
			var paid_amount = (parseFloat(rsStr[18]) - parseFloat(rsStr[28]));
			var remaining   = (parseFloat(rsStr[14]) - parseFloat(rsStr[18]));
			var due_amount  = (parseFloat(rsStr[19]) + paid_amount);
			$('#paid_amount').val(paid_amount);
			$('#due_amount').val(due_amount);
			$('#remaining_amount').val(remaining);
			//==== Start for travel company ===
			$('#applicant_id').val(rsStr[20]).trigger("chosen:updated");
			$('#sales_type').val(rsStr[21]).trigger("chosen:updated");
			ShowHideTravel(rsStr[21]);
			$('#refund_type').val(rsStr[22]).trigger("chosen:updated");
			$('#trip_type').val(rsStr[23]).trigger("chosen:updated");
			$('#travel_date').val(rsStr[24]);
			$('#depart_datetime').val(rsStr[25]);
			$('#arraival_datetime').val(rsStr[26]);
			$('#pnr_no').val(rsStr[27]);
			$('#deposit_amount').val(rsStr[28]);
			$('#airline').val(rsStr[29]);
		    $('#depart_place').val(rsStr[30]);
	        $('#arrival_place').val(rsStr[31]);
	        $('#invoice_note1').val(rsStr[32]);
	        $('#invoice_note2').val(rsStr[33]);
	        $('#invoice_note3').val(rsStr[34]);
			//==== End for travel company ===
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
		var customerId		= parseInt($('#customer_id').val());
		var fee_period		= parseInt($('#fee_period').val());
		if(bill_id==0){ var bill_id	= parseInt($('#bill-id').val()); }
		
		var bill_date 		= $('#sales_date').val();
        var fee_period      = parseInt(bill_date.substring(3, 5));
    
		if(instituteId >0 && branchId >0 && sessionId >0 && storeId >0 && customerId >0 && fee_period >0){
		 $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>sales/GetBillList",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&bill_date="+bill_date+"&session_id="+sessionId+"&store_id="+storeId+"&customer_id="+customerId+"&bill-period="+fee_period+"&bill-id="+bill_id,
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
            url: "<?php echo base_url();?>sales/GetRecords",
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
        var customerId   	= $('#src-customer_id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>sales/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-period="+periodId+"&src-session="+sessionId+"&src-store="+storeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&src-customer="+customerId,
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
        var customerId   	= $('#src-customer_id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>sales/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-period="+periodId+"&src-session="+sessionId+"&src-store="+storeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&src-customer="+customerId+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }	
		
    </script>
</body>
</html>
