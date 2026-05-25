<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("store")." ".$this->lang->line("setup");?> </title>
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
		       <?php echo $this->lang->line("store");?>
		       <?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       <?php }?>
		    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("store");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("store");?></h3>
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
										<label class="control-label" for="company_name"><?php echo $this->lang->line("company_name");?></label>
										<div id="com_id">
											<select name="company_name" id="company_name" class="chosen-select" onChange="getBranchList(this.value,'branch-id')" required="">
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
										<label class="control-label" for="branch-id"><?php echo $this->lang->line("branch_name");?>:</label>
										
										<select name="branch-id" id="branch-id" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
											
										</select>
										
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-6 col-md-6 col-lg-6">
									<div class="form-group required">
										<label class="control-label" for="store_name"><?php echo $this->lang->line("store_name");?></label>
										<input type="text" required="" placeholder="<?php echo $this->lang->line("store_name");?>" name="store_name" id="store_name" class="form-control">
									</div>
								</div>
								<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-group required">
											<label class="control-label" for="status"><?php echo $this->lang->line("status");?>:</label>
											<select name="status" id="status" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("inactive");?></option>
												<option value="1"><?php echo $this->lang->line("active");?></option>
											</select>
										</div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-2 col-md-2 col-lg-2">
									<input type="hidden" name="store_id" class="form-control" id="store_id">
									<button type="button" id="btnSave" style="margin-top: 7px;" class="btn btn-block btn-success save"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("save");?></button>
								</div>
								<div class="col-sm-2 col-md-2 col-lg-2">
									<button type="reset" id="reset" style="margin-top: 7px;" class="btn btn-block btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
								</div>
							</div>
						</div> 
						</form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
				<?php if($hasViewOption){?>
					<div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("store");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
							<div class="row">
								<div class="col-sm-6 col-md-6 col-lg-6">
									<div class="form-group required">
										<label class="control-label" for="src-institute_id"><?php echo $this->lang->line("company_name");?></label>
										<select name="src-institute_id" id="src-institute_id" class="chosen-select" onChange="getBranchList(this.value,'src-branch-id')" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
											<?php foreach($cquery->result() as $row){
												echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-sm-6 col-md-6 col-lg-6">
									<div class="form-group required">
										<label class="control-label" for="src-branch_id"><?php echo $this->lang->line("branch");?></label>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("store");?> <?php echo $this->lang->line("list");?></h3>
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

	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#store_id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
	var delId = $('#store_id').val();
	$('#store_name_id').val("");
	if(delId!=""){
	    $.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>store/DelRecord",
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
	    $('#alert-delete').show();
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>store/GetRecord",
		    success: function(option){
		    $('#dataGrid').html(option);
		    $('#alert-delete').hide(5000);
		    }//Success
		});// End datagrid
	}

	function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>store/FillRecord",
            data: "id="+id,
            success: function(option){
				//alert(option);
				rsStr = option.split("##&##");
				//alert(rsStr[1]);
				$('#company_name').val(rsStr[1]);
				$('#company_name').trigger("chosen:updated");
				getBranchList(rsStr[1],"#branch-id",rsStr[2]);
				$('#branch-id').val(rsStr[2]);
				$('#branch-id').trigger("chosen:updated");
				$('#store_name').val(rsStr[3]);
				$('#status').val(rsStr[4]);
				$('#status').trigger("chosen:updated");
				$('#store_id').val(rsStr[0]);
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
		jQuery(window).on('resize', resizeChosen);
		$(".chosen-select").val('').trigger("chosen:updated");
		//End Chosen Responsive//

		$('#btnDelete').click(function() {
		    $('#deleteModal').modal('hide');
		});
		
		$('#status').val("1");
		$('#status').trigger("chosen:updated");
		$('.show-create').hide();
		$('#alert-delete').hide();
		$('#alert').hide();

		//Load dataGrid
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>store/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid 

		$('#reset').click(function(){
			$('#alert').hide();
			$('#company_name').val("");
            $('#company_name').trigger("chosen:updated");
            $('#branch-id').val("");
            $('#branch-id').trigger("chosen:updated");
            $('#store_name').val("");
            $('#status').val("1");
			$('#store_id').val("");
		});// End reset
	});

	$('.save').click(function(){
        var companyName    	= $('#company_name').val();
        var branchId     	= $('#branch-id').val();
        var store_name   	= $('#store_name').val();
        var status          = $('#status').val();
        var store_id        = $('#store_id').val();
	//alert(categoryName+categorySlug);
	if(companyName!="" && branchId!="" && store_name!=""){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>store/AddRecord",
			data: "institute-id="+companyName+"&branch-id="+branchId+"&store-name="+store_name+"&status="+status+"&store-id="+store_id,
			success: function(option){
			//alert(option);		
			$('#company_name').val("");
			$('#company_name').trigger("chosen:updated");
			$('#branch-id').val("");
			$('#branch-id').trigger("chosen:updated");
			$('#store_name').val("");
			$('#status').val("1");
			$('#store_id').val("");
			$('#dataGrid').html(option);
			$('.show-create').hide();
			$('#alert-delete').hide();
			$('#alert').show();
			$('#alert').html('Successfully saved!');
			}//Success
	    });// ajax
	}
	return false;
	});// End save
    function isNullAndUndefined(variable){
		if(variable == null || variable == undefined || variable == ""){
		    return true;
		}else if(isNaN(variable)){
		    return true;
		}
    }
 	$('.search').click(function(){
		var company_id       = $('#src-institute_id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}

		if(company_id >=0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>store/GetRecord",
			data: "src-institute="+company_id+"&src-branch="+branch_id,
            		success: function(option){
                		$('#dataGrid').html(option);
						$('#alert-delete').hide();
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
		var company_id       = $('#src-institute_id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>store/GetRecord",
            data: "src-institute="+company_id+"&src-branch="+branch_id+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
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
    </script>
</body>
</html>
