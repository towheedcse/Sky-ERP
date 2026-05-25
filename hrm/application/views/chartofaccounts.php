<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("chartofaccount")." ".$this->lang->line("setup");?></title>
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
				<?php echo $this->lang->line("chartofaccount");?>
			    <?php if($hasCreateOption){?>
		       	    <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       	    <?php }?>
			    </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                                <li class="breadcrumb-item active"><?php echo $this->lang->line("chartofaccount");?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("chartofaccount");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                            <div class="card-body">
                                <div id="alert" class="alert alert-success"></div>
                                <form>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label for="company-name" class="control-label"><?php echo $this->lang->line("company_name");?></label>
                                                <div id="com_id">
                                                    <select name="company-name" id="company-name" required="" class="form-control chosen-select">
                                                        <option value=""><?php echo $this->lang->line("select");?> <?php echo $this->lang->line("company");?></option>
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
                                                <label class="control-label" for="group-id"><?php echo $this->lang->line("group_ledger")." ".$this->lang->line("name");?></label>
                                                <div id="group-head">
                                                    <select name="group-id" id="group-id" class="chosen-select" onChange="getSubAccountList(this.value,'sl-level-1')">
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("group_ledger");?></option>
													<?php foreach($gquery->result() as $row){
													echo '<option value="'.$row->group_id.'">'.$row->group_name.'</option>';
													}
													?>	
													</select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="SL Level-1"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("1")." ".$this->lang->line("name");?></label>
                                                <div id="sh-level1-id">
                                                    <select name="sl-level-1" id="sl-level-1" class="chosen-select" onChange="getChildAccountList(this.value,'sl-level-2')">
													  <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("1");?></option>
															
													</select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="SL Level-2 Name"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("2")." ".$this->lang->line("name");?></label>
                                                <select name="sl-level-2" id="sl-level-2" class="chosen-select" onChange="getSubChildAccountList(this.value,'sl-level-3')">
													  <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("2");?></option>
															
												</select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label class="control-label" for="SL Level-3 Name"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("3")." ".$this->lang->line("name");?></label>
                                                <select name="sl-level-3" id="sl-level-3" class="chosen-select">
													  <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("3");?></option>
															
												</select>
                                            </div>
                                        </div>

                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                            <div class="form-group required">
                                                <label class="control-label" for="account-type"><?php echo $this->lang->line("account_type");?></label>
                                                <div id="account-type-id">
                                                    <select name="account-type" id="account-type" class="chosen-select" placeholder="<?php echo $this->lang->line("account_type");?>">
													  <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("account_type");?></option>
													  <option value="1">Salesman</option>			
													  <option value="2">Distributor</option>				  
													  <option value="3">Importer</option>
													  <option value="4">Cash</option>
													  <option value="5">Bank</option>
													  <option value="6">Duties & Taxes</option>
													  <option value="7">Commission</option>
													  <option value="8">Revenue</option>
													  <option value="9">Expense</option>
													  <option value="10">Employee</option>
													  <option value="11">Customer</option>
													  <option value="12">Inventory Item</option>
													  <option value="13">Sales Item</option>
													  <option value="15">Service Charge</option>
													  <option value="16">Fine</option>
													  <option value="17">Discount</option> <!-- Expense-->
													  
													  <option value="18">Cash Salary</option> <!-- Expense-->
													  <option value="19">Basic Salary</option> <!-- Expense-->
													  <option value="20">House Rent</option> <!-- Expense-->
													  <option value="21">Medical Allowance</option> <!-- Expense-->
													  <option value="22">Transport Allowance</option> <!-- Expense-->
													  <option value="23">Festival Bonus</option> <!-- Expense-->
													  <option value="24">Communication Allowance</option> <!-- Expense-->
													  <option value="25">Others Allowance</option> <!-- Expense-->
													  <option value="26">Provident Funds</option> <!-- Liabilities-->
													  <option value="27">Salary Loan</option> <!-- Loan-->
													  <option value="28">T&T Allowance</option>  <!-- Expense-->
													  <option value="29">Others Payble</option> <!-- Expense-->
													  <option value="30">bKash</option>
													  <option value="31">Credit Card</option>
													  <option value="32">Nagad</option>
													  <option value="55">Adjust</option>
													  <option value="56">Others</option>
													</select>
                                                </div>
                                            </div>
                                        </div>
										<div class="col-sm-2 col-md-2 col-lg-2">
                                            <div class="form-group required">
                                                <label class="control-label" for="count-unit"><?php echo $this->lang->line("count_unit");?></label>
                                                <div id="account-type-id">
                                                    <select name="count-unit" id="count-unit" class="chosen-select" placeholder="<?php echo $this->lang->line("count_unit");?>">
													  <option value="20"><?php echo $this->lang->line("select")." ".$this->lang->line("count_unit");?></option>
													  <option value="1">Year</option>
													  <option value="2">Month</option>
													  <option value="3">Days</option>
													  <option value="4">Pcs</option>
													  <option value="5">Dzn</option>
													  <option value="6">Fit</option>
													  <option value="7">Pack</option>
													</select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="Account Name"><?php echo $this->lang->line("account")." ".$this->lang->line("of")." ".$this->lang->line("name");?></label>
                                                <input type="text" required="" placeholder="<?php echo $this->lang->line("account")." ".$this->lang->line("of")." ".$this->lang->line("name");?>" name="account-name" id="account-name" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="Account Ledger Details"><?php echo $this->lang->line("account")." ".$this->lang->line("of")." ".$this->lang->line("details");?></label>
                                                <input type="text" required="" placeholder="<?php echo $this->lang->line("account")." ".$this->lang->line("of")." ".$this->lang->line("details");?>" name="account-details" id="account-details" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="account-id" class="form-control" id="account-id">
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success save-record"><i class="fas fa-save"></i> <?php echo $this->lang->line("save");?></button>
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
			<h3 class="card-title"><i class="fa fa-filter"></i><?php echo $this->lang->line("search")." ".$this->lang->line("chartofaccount");?></h3>
			<div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
			</div>
			<div class="card-body">
				<form action="" method="post" class="hidden-print" role="form">
					<div class="row">
						<div class="col-sm-4 col-md-4 col-lg-4">
		                                    <div class="form-group required">
		                                        <label for="company-name" class="control-label"><?php echo $this->lang->line("company_name");?></label>
		                                        <div id="com_id">
		                                            <select name="src-company" id="src-company" required="" class="form-control chosen-select">
		                                                <option value=""><?php echo $this->lang->line("select");?> <?php echo $this->lang->line("company");?></option>
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
								<label class="control-label" for="src-group-id"><?php echo $this->lang->line("group_ledger")." ".$this->lang->line("name");?></label>
								<select name="src-group-id" id="src-group-id" class="form-control chosen-select" onChange="getSubAccountList(this.value,'src-level-1')">
								<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("group_ledger");?></option>
								<?php foreach($gquery->result() as $row){
								echo '<option value="'.$row->group_id.'">'.$row->group_name.'</option>';
								}
								?>
								</select>
							</div>
						</div>
						<div class="col-sm-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="control-label" for="src-sales-person"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("1")." ".$this->lang->line("name");?></label>
								<select name="src-level-1" id="src-level-1" class="form-control chosen-select" onChange="getChildAccountList(this.value,'src-level-2')">
								<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("1");?></option>
					
								</select>
							</div>
						</div>

		                                <div class="col-sm-8 col-md-8 col-lg-8">
		                                    <div class="form-group required">
		                                        <label class="control-label" for="SL Level-2 Name"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("2")." ".$this->lang->line("name");?></label>
		                                        <select name="src-level-2" id="src-level-2" class="chosen-select">
								  <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("2");?></option>
									
							 </select>
		                                    </div>
		                                </div>
						<div class="col-sm-4 col-md-4 col-lg-4">
							<div class="form-group text-right">
								<br>
								<button type="button" class="btn btn-md btn-info search" style="margin-top:5px; width:105px"><i class="fa fa-search"></i> <?php echo $this->lang->line("search");?> </button>
								<button type="button" onclick="PrintElem('#dataGrid')" class="btn btn-md btn-success print" style="margin-top:5px;width:105px"><span class="glyphicon glyphicon-print"> <?php echo $this->lang->line("print");?></span></button>
							</div>
						</div>	
					</div>
				</form>
			</div>	
		 </div>
		 <div class="clearfix"></div>

                 <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("chartofaccount")." ".$this->lang->line("of")." ".$this->lang->line("list");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">			    
                            <div id="danger-alert" class="alert alert-danger"></div>
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
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>-->
    <!--<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>-->
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
    </script>
    
<script type="text/javascript">
	$(document).ready(function () {
		resizeChosen();
		$('#btnSave').click(function() {
			setTimeout(function() {$('#addModal').modal('hide');}, 600);
		});
		$('#reset').click(function() {
			$(this).find('form').trigger('reset');
			$('#account-id').val("");	
			$('#company-name').val("0");	
			$('#company-name').trigger('chosen:updated');	
			$('#group-id').val("0");	
			$('#group-id').trigger('chosen:updated');
			$('#sl-level-1').val("0");	
			$('#sl-level-1').trigger('chosen:updated');
			$('#sl-level-2').val("0");	
			$('#sl-level-2').trigger('chosen:updated');
			$('#sl-level-3').val("0");	
			$('#sl-level-3').trigger('chosen:updated');
			$('#account-type').val("0");	
			$('#account-type').trigger('chosen:updated');
			$('#count-unit').val("20");	
			$('#count-unit').trigger('chosen:updated');
			
			$('#account-name').val("");
			$('#account-details').val("");
		});
		$('.close').click(function() {
			$(this).find('form').trigger('reset');
			$('#account-id').val("");	
			$('#company-name').val("0");	
			$('#company-name').trigger('chosen:updated');	
			$('#group-id').val("0");	
			$('#group-id').trigger('chosen:updated');
			$('#sl-level-1').val("0");	
			$('#sl-level-1').trigger('chosen:updated');
			$('#sl-level-2').val("0");	
			$('#sl-level-2').trigger('chosen:updated');
			$('#sl-level-3').val("0");	
			$('#sl-level-3').trigger('chosen:updated');
			$('#account-type').val("0");	
			$('#account-type').trigger('chosen:updated');
			$('#count-unit').val("20");	
			$('#count-unit').trigger('chosen:updated');
			$('#account-name').val("");
			$('#account-details').val("");
		});
		$('#btnDelete').click(function() {
			$('#deleteModal').modal('hide');
		});
		$('#addModal').on('hidden.bs.modal', function () {
			$('#alert').hide();
			$(this).find('form').trigger('reset');
		});
		$('.show-create').hide();
		$('#alert').hide();
		$('#danger-alert').hide();
		//Load dataGrid
		reloadDataGrid();
		$('#reset').click(function(){
			$(this).find('form').trigger('reset');
			$('#account-id').val("");	
			$('#company-name').val("0");	
			$('#company-name').trigger('chosen:updated');	
			$('#group-id').val("0");	
			$('#group-id').trigger('chosen:updated');
			$('#sl-level-1').val("0");	
			$('#sl-level-1').trigger('chosen:updated');
			$('#sl-level-2').val("0");	
			$('#sl-level-2').trigger('chosen:updated');
			$('#sl-level-3').val("0");	
			$('#sl-level-3').trigger('chosen:updated');
			$('#account-type').val("0");	
			$('#account-type').trigger('chosen:updated');
			$('#count-unit').val("20");	
			$('#count-unit').trigger('chosen:updated');
			$('#account-name').val("");
			$('#account-details').val("");
		});// End reset
	});
</script>
<script>
	/* Start Delete Data*/
	function deleteRecord(id){
		$('#account-id').val(id);
		return false;
	}
	$('.confirm').click(function(){
		var delid = $('#account-id').val();
		$('#account-id').val("");
		if(delid!=""){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>chartofaccounts/DeleteRecord",
				data: "id="+delid,
				success: function(option){
					$('#danger-alert').show(1000);
					$('#danger-alert').html('Record deleted successfully!!!');
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
			url: "<?php echo base_url();?>chartofaccounts/GetRecords",
			success: function(option){
				$('#dataGrid').html(option);
				$('#danger-alert').hide();
			}//Success
		});// End datagrid
	}
	$('.search').click(function(){
		var company_id 	     = $("#src-company").val();
        var srcGroupId       = $("#src-group-id").val();
		var srcSubHead1      = $("#src-level-1").val();
		var srcSubHead2      = $("#src-level-2").val();
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>chartofaccounts/GetRecords",
			data: "src-company-id="+company_id+"&src-group-id="+srcGroupId+"&src-level-1="+srcSubHead1+"&src-level-2="+srcSubHead2,
            		success: function(option){
                		$('#dataGrid').html(option);
            		}//Success
        	});// ajax
                return false;
        });// End search
	function editRecord(id){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>chartofaccounts/FillRecord",
			data: "id="+id,
			success: function(option){
				//alert(option);
				ArrStr = option.split("@@##@@");
				rsStr  = ArrStr[0].split("##&##");				
				//alert(rsStr[1]);
				//getSubAccountList(rsStr[1],"sl-level-1",rsStr[4]);
				$('#sl-level-1').html(ArrStr[1]);
		        $('#sl-level-1').trigger('chosen:updated');
				//getChildAccountList(rsStr[4],"sl-level-2",rsStr[5]);
				$('#sl-level-2').html(ArrStr[2]);
		        $('#sl-level-2').trigger('chosen:updated');
				$('#sl-level-3').html(ArrStr[3]);
		        $('#sl-level-3').trigger('chosen:updated');
				$('#account-id').val(rsStr[0]);
				$('#company-name').val(rsStr[1]);	
				$('#company-name').trigger('chosen:updated');				
				$('#group-id').val(rsStr[3]);
				$('#group-id').trigger('chosen:updated');	
				$('#sl-level-1').val(rsStr[4]);
				$('#sl-level-1').trigger('chosen:updated');
				$('#sl-level-2').val(rsStr[5]);
				$('#sl-level-2').trigger('chosen:updated');
				$('#sl-level-3').val(rsStr[6]);
				$('#sl-level-3').trigger('chosen:updated');
				$('#account-type').val(rsStr[7]);
				$('#account-type').trigger('chosen:updated');
				$('#account-name').val(rsStr[8]);
				$('#account-details').val(rsStr[9]);
				$('#count-unit').val(rsStr[10]);	
				$('#count-unit').trigger('chosen:updated');
	    		$('.show-create').show();
				$('#alert').show();
				$('#alert').html('Ready to Edit!');
			}//Success

		});// ajax
		return false;
	 }

	$('.save-record').click(function(){
		var companyName		= $('#company-name').val();
		var GroupHead		= $('#group-id').val();
		var Subsidiary1Id	= $('#sl-level-1').val();
		var Subsidiary2Id	= $('#sl-level-2').val();
		var Subsidiary3Id	= $('#sl-level-3').val();
		var accountType		= $('#account-type').val(); 
		var accountName		= $('#account-name').val().replace(/&/g,'U+0026'); 
		var accountDetails	= $('#account-details').val().replace(/&/g,'U+0026');
		var account_id		= $('#account-id').val();
		var count_unit		= $('#count-unit').val();
		if((accountName !="" && accountDetails !="") && (parseInt(companyName) >0) && (parseInt(GroupHead) >0) && (parseInt(Subsidiary1Id) >0) && (parseInt(Subsidiary2Id) >0)){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>chartofaccounts/AddRecord",
				data: "company-name="+companyName+"&group-id="+GroupHead+"&sl-level-1="+Subsidiary1Id+"&sl-level-2="+Subsidiary2Id+"&sl-level-3="+Subsidiary3Id+"&account-type="+accountType+"&account-name="+accountName+"&account-details="+accountDetails+"&count-unit="+count_unit+"&account-id="+account_id,
				success: function(option){
					//alert(option);
					$(this).find('form').trigger('reset');
					$('#account-id').val("");	
					$('#company-name').val("0");	
					$('#company-name').trigger('chosen:updated');	
					$('#group-id').val("0");	
					$('#group-id').trigger('chosen:updated');
					$('#sl-level-1').val("0");	
					$('#sl-level-1').trigger('chosen:updated');
					$('#sl-level-2').val("0");	
					$('#sl-level-2').trigger('chosen:updated');
					$('#sl-level-3').val("0");	
					$('#sl-level-3').trigger('chosen:updated');
					$('#account-type').val("0");	
					$('#account-type').trigger('chosen:updated');
					$('#count-unit').val("20");	
					$('#count-unit').trigger('chosen:updated');
					$('#account-name').val("");
					$('#account-details').val("");
					$('#dataGrid').html(option);
					$('.show-create').hide();
					$('#alert').show();
					$('#alert').html('Record has been saved successfully!!!');
				}//Success
			});// ajax
		}else{			
			$('#danger-alert').show();
			$('#danger-alert').html('Record did not saved! Please fill data in required fields');
			return false;
		}
		return false;
	});// End save
	
	/* Pagination Next Page */
	function nextPage(frm, to, pno) {
		var srcTopHead      = $("#src-top-head").val();
		var srcSubHead      = $("#src-sub-head").val();
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>chartofaccounts/GetRecords",
			data: "src-top-head="+srcTopHead+"&src-sub-head="+srcSubHead+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
			success: function (option) {
				$('#dataGrid').html(option);
			}//Success
		});// End datagrid
		return false;

	}
	function getSubAccountList(group_id,placement,sub_head=0){ 
		var company_id = $("#company-name").val(); 
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>chartofaccounts/GetAjaxSubAccountList",
		    data: "company-id="+company_id+"&group-id="+group_id+"&sub-head="+sub_head,
		    success: function(option){
		        //alert(option);
		        $('#'+placement).html(option);
		        $('#'+placement).trigger('chosen:updated');
		    }//Success

		});// ajax
		return false;
    	}
	function getChildAccountList(sub_head,placement,child_head=0){
		var company_id = $("#company-name").val();
		var group_id   = $("#parents-head").val();
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>chartofaccounts/GetAjaxChildAccountList",
		    data: "company-id="+company_id+"&group-id="+group_id+"&sub-head="+sub_head+"&child-head="+child_head,
		    success: function(option){
		        //alert(option);
		        $('#'+placement).html(option);
		        $('#'+placement).trigger('chosen:updated');
		    }//Success

		});// ajax
		return false;
    	}
	function getSubChildAccountList(sub2_id,placement,sub3_id=0){
		var company_id = $("#company-name").val();
		var group_id   = $("#parents-head").val();
		var sub1_id    = $("#sl-level-1").val();
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>chartofaccounts/GetAjaxSubChildAccountList",
		    data: "company-id="+company_id+"&group-id="+group_id+"&sub1-id="+sub1_id+"&sub2-id="+sub2_id+"&sub3-id="+sub3_id,
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
