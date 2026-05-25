<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("challan");?> </title>
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
               <?php echo $this->lang->line("delivery_challan");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("challan");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                    <div class="card card-primary show-create">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("delivery_challan");?></h3>
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
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("challan")." ".$this->lang->line("details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								
									<div class="row">
										<div class="col-sm-5 col-md-5 col-lg-5">
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
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">	
												<label class="control-label" for="workorder_id"><?php echo $this->lang->line("workorder_no");?></label>
												<select name="workorder_id" id="workorder_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("workorder_no");?></option>
														
												</select>
										    </div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">	
												<label class="control-label" for="challan_date"><?php echo $this->lang->line("challan_date");?></label>			
												
												<div class="input-group">
													<input type="text" class="form-control datepicker_mask" name="challan_date" id="challan_date" required="">
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
										<h3 class="container-fluid card-title"><?php echo $this->lang->line("product_details")."(".$this->lang->line("sales").")";?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">								
									<table class="table table-condensed table-bordered"> <!-- class table-condensed -->
									   <thead>
										<tr>
											<th class="active"><?php echo $this->lang->line("product_description");?> *</th>
											<th class="active"><?php echo $this->lang->line("product_sku");?></th>
											<th class="active"><?php echo $this->lang->line("license_no");?></th>
											<th class="active"><?php echo $this->lang->line("start_date");?></th>
											<th class="active"><?php echo $this->lang->line("end_date");?></th>
											<th class="active"><?php echo $this->lang->line("quantity");?> *</th>
											<th class="active"><?php echo $this->lang->line("remarks");?></th>
										</tr>
									   </thead>
									   <tbody>
										<tr>									
											<td class="active" id="detail-item1">
											<input type="text" placeholder="<?php echo $this->lang->line("product_description");?>" name="product-description" id="product-description" class="form-control" style="min-width:280px">
											</td>
											<td class="active" id="detail-item2">
											
											<input type="text" placeholder="<?php echo $this->lang->line("product_sku");?>" name="product-sku" id="product-sku" class="form-control" style="width:220px">
											</td>							    
											<td class="active" id="detail-item3">
											<input type="text" placeholder="<?php echo $this->lang->line("license_no");?>" name="validity" id="validity" class="form-control" style="width: 110px;">								
											</td>							    
											<td class="active" id="detail-item3">
											<input type="text" placeholder="<?php echo $this->lang->line("start_date");?>" name="start_date" id="start_date" class="form-control datepicker_mask" style="width: 110px;">								
											</td>							    
											<td class="active" id="detail-item3">
											<input type="text" placeholder="<?php echo $this->lang->line("end_date");?>" name="end_date" id="end_date" class="form-control datepicker_mask" style="width: 110px;">								
											</td>
											<td class="active" id="detail-item4">
											<input type="text" placeholder="<?php echo $this->lang->line("quantity");?>" name="quantity" id="quantity" onKeyUp="calTotalAmount()" class="form-control" style="width: 90px;"/>
											</td>
											<td class="active" id="detail-item4">
											<input type="text" placeholder="<?php echo $this->lang->line("remarks");?>" name="remarks" id="remarks" class="form-control" style="min-width:90px">
											
											<input type="hidden" placeholder="<?php echo $this->lang->line("unit_price");?>" name="unit-price" id="unit-price" onKeyUp="calTotalAmount()" value="0" class="form-control" style="width:85px">
											<input type="hidden" placeholder="<?php echo $this->lang->line("total");?>" name="total-amount" id="total-amount" value="0" class="form-control" style="width:92px">
											
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
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("delivery_details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="delivery_address"><?php echo $this->lang->line("delivery_address");?></label>
        								        <textarea placeholder="<?php echo $this->lang->line("delivery_address");?>" name="delivery_address" id="delivery_address" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
									<div class="row">
        								<div class="col-sm-12 col-md-12 col-lg-12 pull-right">
        								    <div class="form-group required">
												<label class="control-label" for="delivery_note"><?php echo $this->lang->line("delivery_note");?></label>
        								  <textarea placeholder="<?php echo $this->lang->line("delivery_note");?>" name="delivery_note" id="delivery_note" row="7" class="form-control"></textarea>
        								    </div>
        								</div>
        							</div>
								</div> <!-- End container-fluid -->
							</div> <!-- End card-outline -->
					
							<div class="row">
							    
    							<input type="hidden" name="institute_id" id="institute_id" value="1">
    							<input type="hidden" name="branch_id" id="branch_id" value="1">
    							<input type="hidden" name="challan-id" id="challan-id" value="0">
    							<input type="hidden" name="details-id" id="details-id" value="0">
    							<input type="hidden" name="status" id="status" value="0">  
							
								<div class="col-sm-3 col-md-3 col-lg-3 pull-right">
								   <div class="pull-left" style="margin-bottom:20px"><button type="submit" class="btn btn-md btn-success save-bill"><span class="glyphicon glyphicon-saved"> <?php echo $this->lang->line("save");?> </span></button>&nbsp;&nbsp;			
									<button type="reset" id="bill-clear" class="btn btn-md btn-warning"><span class="glyphicon glyphicon-refresh"> <?php echo $this->lang->line("clear");?> </span></button> 
									
								   </div>
								</div>
							</div>
				 
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->    
                               
                    
				</div> <!-- End Card --> 
          
            <?php if($hasViewOption){?>
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("delivery_challan");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
							<input type="hidden" name="src-institute_id" id="src-institute_id" value="1">
							<input type="hidden" name="src-branch_id" id="src-branch_id" value="1">						
							<div class="row">
								
								<div class="col-sm-3 col-md-3 col-lg-3">
									<div class="form-group required">
										<label class="control-label" for="src-customer"><?php echo $this->lang->line("customer_name");?></label>
										<select name="src-customer" id="src-customer" class="chosen-select" onChange="getSrcWorkorderList(this.value)">
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
										<label class="control-label" for="src-workorder"><?php echo $this->lang->line("workorder_no");?></label>
										<select name="src-workorder" id="src-workorder" class="chosen-select">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("workorder_no");?></option>
												
										</select>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("delivery_challan")." ".$this->lang->line("list");?></h3>
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
            $(this).attr('style', 'width: 280px');
        });
    }
    function resizeGridChosen2() {
        $("#detail-item2 .chosen-container").each(function() {
            $(this).attr('style', 'width: 220px');
        });
    }
    function resizeGridChosen3() {
        $("#detail-item3 .chosen-container").each(function() {
            $(this).attr('style', 'width: 115px');
        });
    }
    function resizeGridChosen4() {
        $("#detail-item4 .chosen-container").each(function() {
            $(this).attr('style', 'width: 80px');
        });
    }
	
    $('.save-detail').click(function(){
	var challan_id      = $('#challan-id').val();
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var details_id		= parseInt($('#details-id').val());
	var customer_id     = parseInt($('#customer_id').val());
	var workorder_id    = parseInt($('#workorder_id').val());
	var product_details	= $('#product-description').val().replace(/&/g,'U+0026');
	var product_sku	    = $('#product-sku').val().replace(/&/g,'U+0026'); 
	var validity    	= $('#validity').val();
	var challan_date 	= $('#challan_date').val();
	var start_date 	    = $('#start_date').val();
	var end_date 	    = $('#end_date').val();
	var quantity		= parseInt($('#quantity').val());
	var unit_price 		= parseFloat($('#unit-price').val());
	var total_amount	= parseFloat($('#total-amount').val());
	var remarks		    = $('#remarks').val().replace(/&/g,'U+0026');      	
	if(isNullAndUndef(unit_price)){ unit_price = 0;}
	if(isNullAndUndef(total_amount)){ total_amount = 0;}
	
	if((challan_date!="" && challan_date!="__/__/____" && product_details!="") && (instituteId >0 && branchId >0 && customer_id >0 && workorder_id >0 && quantity >0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>deliverychallan/AddProduct",
			data: "challan-id="+challan_id+"&institute-id="+instituteId+"&branch-id="+branchId+"&details-id="+details_id+"&customer-id="+customer_id+"&workorder-id="+workorder_id+"&product-details="+product_details+"&product-sku="+product_sku+"&quantity="+quantity+"&validity="+validity+"&start_date="+start_date+"&end_date="+end_date+"&unit-price="+unit_price+"&total-amount="+total_amount+"&remarks="+remarks,
			success: function(option){
				$('#product-description').val("");
				$('#product-sku').val("");
				$('#validity').val("");
				$('#start_date').val("");
				$('#end_date').val("");
				$('#quantity').val("");
				$('#unit-price').val("");				
				$('#total-amount').val("0");
				$('#details-id').val("0");
				$('#remarks').val("");	
				$('#danger-alert').hide();
				$('#alert').show(1000);
				$('#alert').html('Record has been added successfully!!!');
				setTimeout(function() {
				  dataSTR  = option.split("##&##");
				  dataGride= dataSTR[0];	
				  $('#ProductGrid').html(dataGride);
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
		$('#product-description').val("");
		$('#product-sku').val("");
		$('#validity').val("");
		$('#start_date').val("");
		$('#end_date').val("");
		$('#quantity').val("0");
		$('#unit-price').val("0");				
		$('#total-amount').val("0");
		$('#details-id').val("0");
		$('#remarks').val("");
	});// End clear
	
	$('.save-bill').click(function(){
	var challan_id      = parseInt($('#challan-id').val());
	var instituteId     = parseInt($('#institute_id').val());
	var branchId        = parseInt($('#branch_id').val());
	var customer_id     = parseInt($('#customer_id').val());
	var workorder_id    = parseInt($('#workorder_id').val());
	var challan_date 	= $('#challan_date').val();
	var delivery_address= $('#delivery_address').val().replace(/&/g,'U+0026');	
	var delivery_note 	= $('#delivery_note').val().replace(/&/g,'U+0026');	
	var status 	        = $('#status').val();		
	
	if((challan_date!="" && challan_date!="__/__/____") && (instituteId >0 && branchId >0 && customer_id >0 && workorder_id >0)){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>deliverychallan/SaveDC",
			data: "challan-id="+challan_id+"&institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customer_id+"&workorder_id="+workorder_id+"&challan_date="+challan_date+"&delivery_address="+delivery_address+"&delivery_note="+delivery_note+"&status="+status,
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
    	$('#challan-id').val("0");			
    	$('#details-id').val("0");
    	$('#customer_id').val("0").trigger("chosen:updated");
    	$('#workorder_id').val("").trigger("chosen:updated");
    	$('#challan_date').val("");	
    	$('#delivery_address').val("");	
    	$('#delivery_note').val("");
		$('#ProductGrid').html("");
	}
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#challan-id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
		var delId = $('#challan-id').val();
		$('#challan-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>deliverychallan/DelRecord",
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
    	var customerId      = parseInt($('#customer_id').val());
		var workorderId     = $('#workorder_id').val();
		var challan_id      = $('#challan-id').val();
		$('#details-id').val("0");
		if(delsId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>deliverychallan/DeleteRow",
				data: "id="+delsId+"&institute-id="+instituteId+"&branch-id="+branchId+"&customer-id="+customerId+"&workorder-id="+workorderId+"&challan-id="+challan_id,
				success: function(option){
				    setTimeout(function() {
    				  dataSTR  = option.split("##&##");
    				  dataGride= dataSTR[0];	
    				  $('#ProductGrid').html(dataGride);
    				}, 100);
					$('#alert-delete').show(1000);
					$('#alert-delete').html('Delete successfully!!!');
				}//Success
			});// ajax
			return false;
		}
    });// End reset
    /* End Delete Data*/
    
    /* Start Approved Data*/
    function approveDC(id){
        $('#challan-id').val(id);
        return false;
    }
	
    $('.confirmApprove').click(function(){
		var delId = $('#challan-id').val();
		$('#challan-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>deliverychallan/ApproveDC",
				data: "id="+delId,
				success: function(option){
					$('#alert-success').show(1000);
					$('#alert-success').html('successfully DC approved!!!');
					reloadDataGrid();
				}//Success
			});// ajax
			return false;
		}
    });// End reset
    /* End Approved Data*/
    
    /* Start Approved Data*/
    function unapprovePO(id){
        $('#challan-id').val(id);
        return false;
    }
	
    $('.confirmUnapprove').click(function(){
		var delId = $('#challan-id').val();
		$('#challan-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>deliverychallan/UnapproveDC",
				data: "id="+delId,
				success: function(option){
					$('#alert-success').show(1000);
					$('#alert-success').html('successfully DC Unapproved!!!');
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
	
	function calTotalAmount(){
		var unit_price = parseFloat($('#unit-price').val());
		var quantity   = parseFloat($('#quantity').val());
		if(isNullAndUndef(unit_price)){ unit_price = 0;}
		var total_bill = (unit_price * quantity);
		$('#total-amount').val(total_bill);
	}
	
    function editRow(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>deliverychallan/FillDetails",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            $('#product-description').val(rsStr[1]);
            $('#product-sku').val(rsStr[2]);
            $('#unit-price').val(rsStr[3]);
            $('#quantity').val(rsStr[4]);
            $('#validity').val(rsStr[5]);
            $('#start_date').val(rsStr[6]);
            $('#end_date').val(rsStr[7]);
            $('#total-amount').val(rsStr[8]);
            $('#remarks').val(rsStr[9]);		
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
            url: "<?php echo base_url();?>deliverychallan/FillRecord",
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
        	$('#challan-id').val(rsStr[1]);
        	$('#customer_id').val(rsStr[3]).trigger("chosen:updated");
        	$('#workorder_id').val(rsStr[4]).trigger("chosen:updated");
        	$('#challan_date').val(rsStr[5]);
        	$('#delivery_address').val(rsStr[6]);	
        	$('#delivery_note').val(rsStr[7]);
        	$('#status').val(rsStr[8]);
			$('#institute_id').val(rsStr[9]);	
			$('#branch_id').val(rsStr[10]);
				
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
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
	
    function getSrcWorkorderList(id,workorder_id=0){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>distributorpo/getWorkorderList",
            data: "id="+id+"&workorder_id="+workorder_id,
            success: function(option){
                //alert(option);
		        $('#src-workorder').html(option).trigger("chosen:updated");
            }//Success
        });// ajax
        return false;
    }
    
    function getProductList(challan_id=0){
    	var instituteId     = parseInt($('#institute_id').val());
    	var branchId        = parseInt($('#branch_id').val());
    	var customerId      = parseInt($('#customer_id').val());
		var workorderId     = $('#workorder_id').val();
		if(challan_id==0){
		var challan_id      = $('#challan-id').val();
		}
    
		if(instituteId >0 && branchId >0 && customerId >0 && workorderId >0){
		 $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>deliverychallan/GetProductList",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&customer_id="+customerId+"&workorder_id="+workorder_id+"&challan-id="+challan_id,
            success: function(option){
				  dataSTR  = option.split("##&##");
				  dataGride= dataSTR[0];	
				  $('#ProductGrid').html(dataGride);
            }//Success
         });// End datagrid
		}
	}
	function reloadDataGrid(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>deliverychallan/GetRecords",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    
	$('.search').click(function(){
    	var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var customerId      = $('#src-customer').val();
        var workorderId     = $('#src-workorder').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>deliverychallan/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-customer="+customerId+"&src-workorder="+workorderId+"&srcFrom="+srcFrom+"&srcTo="+srcTo,
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
        var customerId      = $('#src-customer').val();
        var workorderId     = $('#src-workorder').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>deliverychallan/GetRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-customer="+customerId+"&src-workorder="+workorderId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
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
