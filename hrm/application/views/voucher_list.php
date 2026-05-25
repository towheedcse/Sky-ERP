<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("voucher")." ".$this->lang->line("list");?></title>
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
                    <?php echo $this->lang->line("voucher")." ".$this->lang->line("list");?>
                    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("voucher")." ".$this->lang->line("list");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                         
            <?php if($hasViewOption){?>
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("voucher");?></h3>
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
								 
								 <div class="col-sm-2 col-md-2 col-lg-2">
									<div class="form-group">
										<label class="control-label" for="src-session_id"><?php echo $this->lang->line("session");?></label>
										<select name="src-session_id" id="src-session_id" class="chosen-select">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session");?></option>
											<?php foreach($sequery->result() as $row){
												echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
											}
											?>
										</select>
									</div>
								 </div>								
								  <div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-voucher-type"><?php echo $this->lang->line("voucher_type");?></label>
											<select name="src-voucher-type" id="src-voucher-type" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("voucher_type");?></option>
												<option value="1"><?php echo $this->lang->line("payments_voucher");?></option>
												<option value="2"><?php echo $this->lang->line("received_voucher");?></option>
												<option value="3"><?php echo $this->lang->line("expense_voucher");?></option>
												<option value="4"><?php echo $this->lang->line("journal_voucher");?></option>
											</select>
										</div>
								  </div>
								</div>
								<div class="row">
								  <div class="col-sm-4 col-md-4 col-lg-4">
										<div class="form-group">
											<label class="control-label" for="src-fee_period"><?php echo $this->lang->line("payment_period");?></label>
											<select name="src-fee_period" id="src-fee_period" class="chosen-select">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("payment_period");?></option>
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
										<label class="control-label" for="src-customer_id"><?php echo $this->lang->line("account")." ".$this->lang->line("name");?></label>
										<select name="src-customer_id" id="src-customer_id" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("account");?></option>
											<?php if($tah_account){?>												
												<optgroup label="Teacher">
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
												<?php foreach($eah_account->result() as $row){ 
												echo '<option value="'.$row->account_id.'">'.$row->account_name.', Mob: '.$row->mobile.'</option>';
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
												<?php foreach($squery->result() as $irow){
													$account_name =$irow->account_name;
													$account_name.=", CNO: ".$irow->head_id.", Mob: ".$irow->mobile.", ".$irow->class_name;
													echo '<option value="'.$irow->account_id.'">'.$account_name.'</option>';
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("voucher")." ".$this->lang->line("list");?></h3>
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
		//reloadDataGrid();
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
	
	
	function getSrcCustomerList(plasement_id,customer_id=0){
		var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
		
		if(instituteId >0 && branchId >0){
		  $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>voucher/GetCustomerList",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&customer="+customer_id,
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
		$('#voucher-type').val("2");
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
	
	function reloadDataGrid(){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>voucher/GetAllRecords",
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
		var fee_period		= $('#src-fee_period').val();
		if(isNullAndUndef(fee_period)){fee_period=0;} 
		var voucher_type	= $('#src-voucher-type').val();		
		var srcAccHead      = $("#src-customer_id").val();
		var srcPaymode      = $("#src-payment-mode").val();
		var srcFrom	    	= $('#srcFrom').val();
		var srcTo	   	 	= $('#srcTo').val();
		$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>voucher/GetAllRecords",
		data: "src-institute-id="+institute_id+"&src-branch-id="+branch_id+"&src-session-id="+session_id+"src-fee-period="+fee_period+"&src-account-id="+srcAccHead+"&src-payment-mode="+srcPaymode+"&receive-from="+srcFrom+"&receive-to="+srcTo+"&voucher-type="+voucher_type,
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
		var fee_period		= $('#src-fee_period').val();
		if(isNullAndUndef(fee_period)){fee_period=0;}
		var voucher_type	= $('#src-voucher-type').val();
		var srcAccHead      = $("#src-admission_id").val();
		var srcPaymode      = $("#src-payment-mode").val();
		var srcFrom	    	= $('#srcFrom').val();
		var srcTo	   	 	= $('#srcTo').val();
		$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>voucher/GetAllRecords",
		data: "src-institute-id="+institute_id+"&src-branch-id="+branch_id+"&src-session-id="+session_id+"&src-fee-period="+fee_period+"&src-account-id="+srcAccHead+"&src-payment-mode="+srcPaymode+"&receive-from="+srcFrom+"&voucher-type="+voucher_type+"&receive-to="+srcTo+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
		success: function (option) {
			$('#dataGrid').html(option);
		}//Success
		});// End datagrid
		return false;
	}
	
	</script>
</body>
</html>
