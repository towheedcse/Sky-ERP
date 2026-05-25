<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("distri_po");?> </title>
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
       
    <!--approve Modal-->
    <div class="modal fade" id="approveModal" role="dialog" tabindex="-1" aria-labelledby="confirmApproveLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("approved_modal_header");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h5><?php echo $this->lang->line("approved_message");?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    <button type="button" id="btnApprove" class="btn btn-success confirmApprove"> <?php echo $this->lang->line("approved");?></button>
                </div>
            </div>
        </div>
    </div>
    
       
    <!--unapprove Modal-->
    <div class="modal fade" id="unapproveModal" role="dialog" tabindex="-1" aria-labelledby="confirmUnapproveLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("unapproved_modalheader");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h5><?php echo $this->lang->line("unapproved_message");?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    <button type="button" id="btnUnapprove" class="btn btn-danger confirmUnapprove"> <?php echo $this->lang->line("unapproved");?></button>
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
               <?php echo $this->lang->line("distributor_po");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("distri_po");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                    <div class="card card-primary show-create">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("distributor_po");?></h3>
						<div class="card-tools">
						  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
						</div>
					</div> <!-- /.card-header -->  
                    <div class="card-body">                        						    
						<div id="alert" class="alert alert-success"></div>
						<div id="danger-alert" class="alert alert-danger"></div>
                        <div class="container-fluid">
                            <form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>distributorpo/SavePO">
							<div class="card card-info card-outline">							
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("distri_po")." ".$this->lang->line("details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								
									<div class="row">
										<div class="col-sm-5 col-md-5 col-lg-5">
											<div class="form-group required">
												<label class="control-label" for="distributor_id"><?php echo $this->lang->line("distributor_name");?></label>
												<select name="distributor_id" id="distributor_id" class="chosen-select" required="" onChange="getDistriInfo(this.value)">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("distributor_name");?></option>
														<?php foreach($spquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->head_id.' : '.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">	
												<label class="control-label" for="attention"><?php echo $this->lang->line("attention_person");?></label>
												<input type="text" class="form-control" name="attention" id="attention" placeholder="<?php echo $this->lang->line("attention_person");?>" required="">
											</div>								
										</div> 
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">	
												<label class="control-label" for="po_date"><?php echo $this->lang->line("po_date");?></label>			
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="po_date" id="po_date" required="">
													<div class="input-group-prepend">
													<span class="input-group-text"><i class="fa fa-calendar"></i></span>
													</div>
												</div>
											</div>								
										</div>
									</div>
								
									<div class="row">
										<div class="col-sm-5 col-md-5 col-lg-5">
											<div class="form-group">
												<label class="control-label" for="importer_id"><?php echo $this->lang->line("importer_name");?></label>
												<select name="importer_id" id="importer_id" class="chosen-select">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("importer_name");?></option>
														<?php foreach($imquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->head_id.' : '.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div> 
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="customer_id"><?php echo $this->lang->line("customer_name");?></label>
												<select name="customer_id" id="customer_id" class="chosen-select" required="" onChange="getWorkorderList(this.value)">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("customer_name");?></option>
														<?php foreach($cuquery->result() as $row){
															echo '<option value="'.$row->account_id.'">'.$row->head_id.' : '.$row->account_name.'</option>';
														}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">	
												<label class="control-label" for="workorder_id"><?php echo $this->lang->line("workorder_no");?></label>
												<select name="workorder_id" id="workorder_id" onChange="getFromWorkorder(this.value)" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("workorder_no");?></option>
														
												</select>
										    </div>
										</div>
									</div>
																
									<div class="row">
										<div class="col-sm-9 col-md-9 col-lg-9">
											<div class="form-group">	
												<label class="control-label" for="subject"><?php echo $this->lang->line("subject");?></label>
												<input type="text" class="form-control" name="subject" id="subject" placeholder="<?php echo $this->lang->line("subject");?>">
											</div>	
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">	
												<label class="control-label" for="currency"><?php echo $this->lang->line("currency");?></label>
												<input type="text" class="form-control" name="currency" id="currency" placeholder="<?php echo $this->lang->line("currency");?>" readonly="">
												<input type="hidden" class="form-control" name="currency_id" id="currency_id" value=""/>
											</div>	
										</div>
									</div>
																
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">									<div class="form-group required">	
												<label class="control-label" for="including_vat"><?php echo $this->lang->line("including_vat");?></label>			
											    <select name="including_vat" id="including_vat" class="chosen-select" onChange="setlblVATAIT(this.value)" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("yes")." ".$this->lang->line("or")." ".$this->lang->line("no");?></option>
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
										<h3 class="container-fluid card-title"><?php echo $this->lang->line("product_details")."(".$this->lang->line("buy").")";?></h3>	<hr>								
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
												<label class="control-label" for="total_bill"><?php echo $this->lang->line("total")." ".$this->lang->line("po_value");?></label>
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
													<option value="4">TT</option>		
													<option value="10">Others</option>	
												</select>																
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="vat_percentage"><?php echo $this->lang->line("vat")." ".$this->lang->line("percentage");?> <em id="lbl_vatp">(Add)</em></label>
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
										<div class="col-sm-3 col-md-3 col-lg-3 img-container">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("offer_attach");?></label>
												<div class="input-group">
													<span class="input-group-btn">
														<span class="btn btn-default btn-file">
															Browse… <input type="file" name="offer_attach" id="offer_attach" class="form-control" style="height: 25px;" required="">
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
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="ait_percentage"><?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> <em id="lbl_aitp">Add </em></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("ait")." ".$this->lang->line("percentage");?> " name="ait_percentage" id="ait_percentage" class="form-control" onKeyUp="calAITPersent()">
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
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="payment_terms"><?php echo $this->lang->line("payment_terms");?></label>
        								        <textarea placeholder="<?php echo $this->lang->line("payment_terms");?>" name="payment_terms" id="payment_terms" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="delivery_to"><?php echo $this->lang->line("delivery_to");?></label>
        								  <textarea placeholder="<?php echo $this->lang->line("delivery_to");?>" name="delivery_to" id="delivery_to" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="ship_to"><?php echo $this->lang->line("ship_to");?></label>
        								  <textarea placeholder="<?php echo $this->lang->line("ship_to");?>" name="ship_to" id="ship_to" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="bill_to"><?php echo $this->lang->line("bill_to");?></label>
        								  <textarea placeholder="<?php echo $this->lang->line("bill_to");?>" name="bill_to" id="bill_to" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
					
							<div class="row">
							    
    							<input type="hidden" name="institute_id" id="institute_id" value="1">
    							<input type="hidden" name="branch_id" id="branch_id" value="1">
    							<input type="hidden" name="po-id" id="po-id" value="0">
    							<input type="hidden" name="details-id" id="details-id" value="0">
    							<input type="hidden" name="status" id="status" value="0">  
							
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
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("distributor_po");?></h3>
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
										<label class="control-label" for="src-distributor_id"><?php echo $this->lang->line("distributor_name");?></label>
										<select name="src-distributor_id" id="src-distributor_id" class="chosen-select">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("distributor_name");?></option>
												<?php foreach($spquery->result() as $row){
													echo '<option value="'.$row->account_id.'">'.$row->head_id.' : '.$row->account_name.'</option>';
												}
											?>
										</select>
									</div>
								</div>
								<div class="col-sm-2 col-md-2 col-lg-2">
									<div class="form-group">	
										<label class="control-label" for="src-po_no"><?php echo $this->lang->line("po_no");?></label>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("distributor_po")." ".$this->lang->line("list");?></h3>
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
       
        $('#btnApprove').click(function() {
            $('#approveModal').modal('hide');
        });
        $('#btnUnapprove').click(function() {
            $('#unapproveModal').modal('hide');
        });
        $('#institute_id').val("1");
        $('#branch_id').val("1");        
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
	
    $('.save-detail').click(function(){
	var po_id           = $('#po-id').val();
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var details_id	    = parseInt($('#details-id').val());
	var distributor_id  = parseInt($('#distributor_id').val());
	var importer_id     = parseInt($('#importer_id').val());
	var customer_id     = parseInt($('#customer_id').val());
	var workorder_id    = parseInt($('#workorder_id').val());
	var product_details = $('#product-description').val().replace(/&/g,'U+0026');
	var product_sku	    = $('#product-sku').val().replace(/&/g,'U+0026'); 
	var validity        = $('#validity').val();
	var quantity	    = parseInt($('#quantity').val());
	var unit_price 	    = parseFloat($('#unit-price').val());
	var total_amount    = parseFloat($('#total-amount').val());
	var total_vat	    = parseFloat($('#total-vat').val());
	var total_ait	    = parseFloat($('#total-ait').val());
	var vat_percent	    = parseFloat($('#vat_percent').val());
	var ait_percent	    = parseFloat($('#ait_percent').val());
	var remarks	    = $('#remarks').val().replace(/&/g,'U+0026');
	var including_vat   = parseInt($('#including_vat').val());       	
	
	if((po_date!="" && po_date!="__/__/____" && product_details!="") && (instituteId >0 && branchId >0 && distributor_id >0 && customer_id >0 && workorder_id >0 && quantity >0 && unit_price >=0 && including_vat>=0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>distributorpo/AddProduct",
			data: "po-id="+po_id+"&institute-id="+instituteId+"&branch-id="+branchId+"&details-id="+details_id+"&distributor-id="+distributor_id+"&importer-id="+importer_id+"&customer-id="+customer_id+"&workorder-id="+workorder_id+"&product-details="+product_details+"&product-sku="+product_sku+"&quantity="+quantity+"&validity="+validity+"&unit-price="+unit_price+"&total-amount="+total_amount+"&vat_percent="+vat_percent+"&ait_percent="+ait_percent+"&total_vat="+total_vat+"&total_ait="+total_ait+"&remarks="+remarks,
			success: function(option){
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
	var po_id           = parseInt($('#po-id').val());
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var distributor_id  = parseInt($('#distributor_id').val());
	var importer_id     = parseInt($('#importer_id').val());
	var customer_id     = parseInt($('#customer_id').val());
	var workorder_id    = parseInt($('#workorder-id').val());
	var po_date 	    = $('#po_date').val();
	var attention 	    = $('#attention').val().replace(/&/g,'U+0026');
	var subject 	    = $('#subject').val().replace(/&/g,'U+0026');
	
	var total_bill      = parseFloat($('#total_bill').val());
	var discount_percentage	= parseFloat($('#discount_percentage').val());
	var discount_amount	= parseFloat($('#discount_amount').val());
	var sub_total           = parseFloat($('#sub_total').val());
	var vat_percentage 	= parseFloat($('#vat_percentage').val());
	var vat_amount 		= parseFloat($('#vat_amount').val());
	var grand_total 	= parseFloat($('#grand_total').val());
	var ait_percentage 	= parseFloat($('#ait_percentage').val());
	var ait_amount 		= parseFloat($('#ait_amount').val());			
	var net_amount      	= parseFloat($('#net_amount').val());
	var payment_mode      	= parseInt($('#payment_mode').val());
	var payment_terms 	= $('#payment_terms').val().replace(/&/g,'U+0026');	
	var delivery_to 	= $('#delivery_to').val().replace(/&/g,'U+0026');	
	var ship_to		= $('#ship_to').val().replace(/&/g,'U+0026');	
	var bill_to		= $('#bill_to').val().replace(/&/g,'U+0026');
	var including_vat     	= parseInt($('#including_vat').val()); 			
	
	if((po_date!="" && po_date!="__/__/____" && subject !="" && attention !="") && (instituteId >0 && branchId >0 && customer_id >0 && distributor_id >0 && total_bill >0 && received_account>0 && including_vat>=0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>distributorpo/SavePO",
			data: "po-id="+po_id+"&workorder_id="+workorder_id+"&distributor_id="+distributor_id+"&po_date="+po_date+"&attention="+attention+"&subject="+subject+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customer_id+"&importer_id="+importer_id+"&discount_percentage="+discount_percentage+"&discount_amount="+discount_amount+"&vat_percentage="+vat_percentage+"&vat_amount="+vat_amount+"&ait_percentage="+vat_percentage+"&ait_amount="+ait_amount+"&total_bill="+total_bill+"&sub_total="+sub_total+"&grand_total="+grand_total+"&net_amount="+net_amount+"&payment_terms="+payment_terms+"&payment_mode="+payment_mode+"&delivery_to="+delivery_to+"&ship_to="+ship_to+"&bill_to="+bill_to+"&including_vat="+including_vat,
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
    	$('#distributor_id').val("");
    	$('#attention').val("");
    	$('#customer_id').val("0").trigger("chosen:updated");
    	$('#workorder_id').val("").trigger("chosen:updated");
    	$('#importer_id').val("0").trigger("chosen:updated");
    	$('#including_vat').val("0").trigger("chosen:updated");
    	$('#vat_percent').val("");
    	$('#ait_percent').val("");
		$('#total-vat').val("0");				
		$('#total-ait').val("0");
    	$('#po_date').val("");
    	$('#subject').val("");
    	
    	$('#total_bill').val("0");
    	$('#discount_percentage').val("0");
    	$('#discount_amount').val("0");
    	$('#sub_total').val("0");
    	$('#vat_percentage').val("0");
    	$('#vat_amount').val("0");
    	$('#grand_total').val("0");
    	$('#ait_percentage').val("0");
    	$('#ait_amount').val("0");			
    	$('#net_amount').val("0");
    	$('#payment-mode').val("1").trigger("chosen:updated");
    	$('#payment_terms').val("");	
    	$('#delivery_to').val("");	
    	$('#ship_to').val("");	
    	$('#bill_to').val("");
		$('#ProductGrid').html("");
	}
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#po-id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
		var delId = $('#po-id').val();
		$('#po-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>distributorpo/DelRecord",
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
    
    function deleteRow(id,po_id=0){
        $('#details-id').val(id);
        return false;
    }
	
    $('.confirmDraft').click(function(){
	var delsId          = $('#details-id').val();
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var distributorId   = parseInt($('#distributor_id').val());
	var importerId      = parseInt($('#importer_id').val());
    var customerId      = parseInt($('#customer_id').val());
	var workorderId     = $('#workorder_id').val();
	var po_id           = $('#po-id').val();
	$('#details-id').val("0");
	if(delsId >0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>distributorpo/DeleteRow",
			data: "id="+delsId+"&institute-id="+instituteId+"&branch-id="+branchId+"&distributor-id="+distributorId+"&customer-id="+customerId+"&importer-id="+importerId+"&workorder-id="+workorderId+"&po-id="+po_id,
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
    
    /* Start Approved Data*/
    function approvePO(id){
        $('#po-id').val(id);
        return false;
    }
	
    $('.confirmApprove').click(function(){
	var delId = $('#po-id').val();
	$('#po-id').val("0");
	if(delId >0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>distributorpo/ApprovePO",
			data: "id="+delId,
			success: function(option){
				$('#alert-success').show(1000);
				$('#alert-success').html('successfully PO approved!!!');
				reloadDataGrid();
			}//Success
		});// ajax
		return false;
	}
    });// End reset
    /* End Approved Data*/
    
    /* Start Approved Data*/
    function unapprovePO(id){
        $('#po-id').val(id);
        return false;
    }
	
    $('.confirmUnapprove').click(function(){
	var delId = $('#po-id').val();
	$('#po-id').val("0");
	if(delId >0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>distributorpo/UnapprovePO",
			data: "id="+delId,
			success: function(option){
				$('#alert-success').show(1000);
				$('#alert-success').html('successfully PO Unapproved!!!');
				reloadDataGrid();
			}//Success
		});// ajax
		return false;
	}
    });// End reset
    /* End Approved Data*/
    
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
						
		if(including_vat_ait==0){
		  var grand_total = (sub_total + vat_amount);
		}else{
		  var grand_total = (sub_total - vat_amount);
		}
		if(isNullAndUndef(grand_total)){ grand_total = 0;} grand_total = Math.round(grand_total);
		$('#grand_total').val(grand_total);
		$('#net_amount').val(grand_total);				
	}
	
	function calAITPersent(){		
		var grand_total    = parseFloat($('#grand_total').val());
		var ait_percentage = parseFloat($('#ait_percentage').val());
		var including_vat_ait 	= parseInt($('#including_vat').val());		
		if(isNullAndUndef(including_vat_ait)){ including_vat_ait = 0;}
		if(isNullAndUndef(grand_total)){ grand_total = 0;}
		if(isNullAndUndef(ait_percentage)){ ait_percentage = 0;}
		
		if(grand_total >0){
		var ait_amount		= ((grand_total/100) * ait_percentage);
		}else{
		var grand_total		= 0;	
		}
		
		if(grand_total==0){
			var sub_total 			= parseFloat($('#sub_total').val());
			if(isNullAndUndef(sub_total)){ sub_total = 0;}
			if(sub_total==0){
			    var total_bill 		= parseFloat($('#total_bill').val()); 
			   
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
    				
    				if(isNullAndUndef(sub_total)){ sub_total = 0; } sub_total = Math.round(sub_total);
    				$('#sub_total').val(sub_total);
    				
    				if(sub_total >0){
    				  var ait_amount		= ((sub_total/100) * vat_percentage);
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
			  var grand_total = (sub_total + vat_amount);
			}else{
			  var grand_total = (sub_total - vat_amount); 
			}
			
			if(isNullAndUndef(grand_total)){ grand_total = 0; } 
			grand_total = Math.round(grand_total);
			$('#grand_total').val(grand_total);
			$('#net_amount').val(grand_total);
		}
		
		if(grand_total==0){
		    var sub_total = parseFloat($('#sub_total').val());
		    if(sub_total==0){
		        var total_bill = parseFloat($('#total_bill').val());
		        sub_total   = total_bill;
		        grand_total = total_bill;
		    }
		    grand_total = sub_total;
		}
		
		var ait_percentage = parseFloat($('#ait_percentage').val());
		if(isNullAndUndef(ait_percentage)){ ait_percentage = 0;}
		var ait_amount 		= parseFloat($('#ait_amount').val());
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
			if(ait_amount >0 && ait_percentage==0){
			  ait_percentage = ((ait_amount * 100) / grand_total);
			}
			
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
		var unit_price = parseFloat($('#unit-price').val());
		var quantity   = parseFloat($('#quantity').val());
		var total_bill = (unit_price * quantity);
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
            url: "<?php echo base_url();?>distributorpo/FillDetails",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            $('#product-description').val(rsStr[1]);
            $('#product-sku').val(rsStr[2]);
            $('#unit-price').val(rsStr[3]);
            $('#quantity').val(rsStr[4]);
            $('#validity').val(rsStr[5]);
            $('#total-amount').val(rsStr[6]);
            $('#vat_percent').val(rsStr[7]);
            $('#total-vat').val(rsStr[8]);
            $('#ait_percent').val(rsStr[9]);
            $('#total-ait').val(rsStr[10]);
            $('#remarks').val(rsStr[11]);		
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
            url: "<?php echo base_url();?>distributorpo/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
    		ArrStr = option.split("@@##@@");
    		rsStr  = ArrStr[0].split("##&##");
    		//===== Dropdown List =====
    		var Dropdown = ArrStr[1];
    		$('#workorder_id').html(Dropdown);
    		var PGrid = ArrStr[2];
    		$('#ProductGrid').html(PGrid);
            		
        	$('#details-id').val("0");
        	$('#po-id').val(rsStr[1]);
        	$('#distributor_id').val(rsStr[3]).trigger("chosen:updated");
        	$('#importer_id').val(rsStr[4]).trigger("chosen:updated");
        	$('#customer_id').val(rsStr[5]).trigger("chosen:updated");
        	$('#workorder_id').val(rsStr[6]).trigger("chosen:updated");
        	$('#po_date').val(rsStr[7]);
        	$('#currency_id').val(rsStr[8]);
        	$('#currency').val(rsStr[9]);
        	$('#attention').val(rsStr[10]);
        	$('#subject').val(rsStr[11]);
        	
        	$('#total_bill').val(rsStr[12]);
        	$('#discount_percentage').val(rsStr[13]);
        	$('#discount_amount').val(rsStr[14]);
        	$('#sub_total').val(rsStr[15]);
        	$('#vat_percentage').val(rsStr[16]);
        	$('#vat_amount').val(rsStr[17]);
        	$('#grand_total').val(rsStr[18]); 
        	$('#ait_percentage').val(rsStr[19]);
        	$('#ait_amount').val(rsStr[20]);			
        	$('#net_amount').val(rsStr[21]); 
        	$('#payment-mode').val(rsStr[22]).trigger("chosen:updated");
        	$('#payment_terms').val(rsStr[23]);	
        	$('#delivery_to').val(rsStr[24]);	
        	$('#ship_to').val(rsStr[25]);	
        	$('#bill_to').val(rsStr[26]);	
        	$('#status').val(rsStr[27]);
		    $('#institute_id').val(rsStr[28]);	
		    $('#branch_id').val(rsStr[29]);
        	$('#including_vat').val(rsStr[30]).trigger("chosen:updated");
				
		    $('.show-create').show();
		    $('#alert').show();
		    $('#alert').html('Ready to Edit!');
            }//Success
        });// ajax
        return false;
    }
    
	
    function getDistriInfo(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/getDistriInfo",
            data: "id="+id,
            success: function(option){
                //alert(option);
                rsStr = option.split("##&##");
                //alert(rsStr[1]);
                $('#attention').val(rsStr[0]);
                $('#currency').val(rsStr[1]);
                $('#currency_id').val(rsStr[2]);
                $('#ship_to').val(rsStr[3]);
                $('#bill_to').val(rsStr[4]);
                if(parseInt(rsStr[2])==1){
                  $('#importer_id').val("0");
		          $('#importer_id').prop('disabled', true).trigger("chosen:updated");
                }else{
		          $('#importer_id').prop('disabled', false).trigger("chosen:updated");
                }
            }//Success
        });// ajax
        return false;
    }
    
	
    function getWorkorderList(id,workorder_id=0){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/getWorkorderList",
            data: "id="+id+"&workorder_id="+workorder_id,
            success: function(option){
                //alert(option);
		        $('#workorder_id').html(option).trigger("chosen:updated");
            }//Success
        });// ajax
        return false;
    }
    
    function getFromWorkorder(workorder_id){
	    var instituteId     = parseInt($('#institute_id').val());
	    var branchId        = parseInt($('#branch_id').val());
    	var distributorId   = parseInt($('#distributor_id').val());
	    var importerId      = parseInt($('#importer_id').val());
    	var customerId      = parseInt($('#customer_id').val());
    	if(isNullAndUndef(importerId)){ importerId = 0;} 
	    var poId            = $('#po-id').val();
	    if(isNullAndUndef(poId)){ poId = "";}
	    if(distributorId >0 && importerId >=0 && customerId >0 && parseInt(workorder_id) >0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/InsertFromWorkorder",
            data: "institute-id="+instituteId+"&branch-id="+branchId+"&distributor-id="+distributorId+"&importer-id="+importerId+"&customer-id="+customerId+"&po-id="+poId+"&workorder-id="+workorder_id,
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
            }//Success
          });// ajax
	    }
        return false;
    }
    function getProductList(po_id=0){
    	var instituteId     = parseInt($('#institute_id').val());
    	var branchId        = parseInt($('#branch_id').val());
    	var distributorId   = parseInt($('#distributor_id').val());
    	var customerId      = parseInt($('#customer_id').val());
	    var workorderId     = $('#workorder_id').val();
	    if(po_id==0){
	    var po_id           = $('#po-id').val();
	    }

	    if(instituteId >0 && branchId >0 && distributorId >0 && customerId >0 && workorderId >0){
	    $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/GetProductList",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&distributor_id="+distributorId+"&customer_id="+customerId+"&workorder_id="+workorder_id+"&po-id="+po_id,
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
            url: "<?php echo base_url();?>distributorpo/GetRecords",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
          });// End datagrid
        }
    
	$('.search').click(function(){
    	var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var poNo            = $('#src-po_no').val();
        var distributorId   = $('#src-distributor').val();
	    var srcFrom	        = $('#src-date-from').val();
	    var srcTo	        = $('#src-date-to').val();
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-distributor="+distributorId+"&src-po_no="+poNo+"&srcFrom="+srcFrom+"&srcTo="+srcTo,
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
        var poId            = $('#src-po_id').val();
        var distributorId   = $('#src-distributor').val();
	    var srcFrom	        = $('#src-date-from').val();
	    var srcTo	        = $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-distributor="+distributorId+"&src-po_id="+poId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
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
