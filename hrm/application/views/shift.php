<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("shift")." ".$this->lang->line("setup");?> </title>
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
		       <?php echo $this->lang->line("shift");?>
		       <?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       <?php }?>
		    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("shift");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("shift");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                            <div class="card-body">
                                <div id="alert" class="alert alert-success"></div>
                                <form>
		                <div class="row">
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="company_name"><?php echo $this->lang->line("company_name");?></label>
		                            <div id="com_id">
		                                <select name="company_name" id="company_name" class="chosen-select" required="">
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
		                        <div class="form-group required">
		                            <label class="control-label" for="shift_name"><?php echo $this->lang->line("shift_name");?></label>
		                            <input type="text" required="" placeholder="<?php echo $this->lang->line("shift_name");?>" name="shift_name" class="form-control text-capitalize" id="shift_name">
		                        </div>
		                    </div>
		                    <div class="col-sm-4 col-md-4 col-lg-4">
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
		                        <div class="form-group required">
		                            <label class="control-label" for="shift_start"><?php echo $this->lang->line("shift_start");?></label>
		                            <div class="input-group">
		                            	<input type="text" class="form-control shift_start_timepicker_mask" required="" placeholder="<?php echo $this->lang->line("shift_time");?>" name="shift_start" id="shift_start">
		                            	<div class="input-group-prepend">
					                      <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
					                    </div>
									</div>
		                            
		                        </div>
		                    </div>
		                    <div class="col-sm-2 col-md-2 col-lg-2">
		                        <div class="form-group required">
		                            <label class="control-label" for="shift_end"><?php echo $this->lang->line("shift_end");?></label>
		                            <div class="input-group">
		                            	<input type="text" class="form-control shift_end_timepicker_mask" required="" placeholder="<?php echo $this->lang->line("shift_time");?>" name="shift_end" id="shift_end">
		                            	<div class="input-group-prepend">
					                      <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
					                    </div>
					                </div>    
		                        </div>
		                    </div>
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group">
		                            <label class="control-label" for="shift_capacity"><?php echo $this->lang->line("shift_capacity");?></label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("shift_capacity");?>" name="shift_capacity" class="form-control" id="shift_capacity">
		                        </div>
		                    </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                            	<br>
								<input type="hidden" name="shift_id" class="form-control" id="shift_id">
                                <button type="button" id="btnSave" style="margin-top: 7px;" class="btn btn-block btn-success save"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("save");?></button>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                            	<br>
                                <button type="reset" id="reset" style="margin-top: 7px;" class="btn btn-block btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
                            </div>
                        </div> 
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
		    <?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("shift");?> <?php echo $this->lang->line("list");?></h3>
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

		jQuery('.shift_start_timepicker_mask').datetimepicker({
			datepicker:false,
			mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
			format:'H:i'
		});

		jQuery('.shift_end_timepicker_mask').datetimepicker({
			datepicker:false,
			mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
			//format:'H:i A'
			format:'H:i'
		});

	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#shift_id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
	var delId = $('#shift_id').val();
	$('#shift_id').val("");
	if(delId!=""){
	    $.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>shift/DelRecord",
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
		    url: "<?php echo base_url();?>shift/GetRecord",
		    success: function(option){
		    $('#dataGrid').html(option);
		    $('#alert-delete').hide(5000);
		    }//Success
		});// End datagrid
	}

	function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>shift/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#company_name').val(rsStr[1]);
            $('#company_name').trigger("chosen:updated");
            $('#shift_name').val(rsStr[2]);
            $('#shift_start').val(rsStr[3]);
			$('#shift_end').val(rsStr[4]);
			$('#shift_capacity').val(rsStr[5]);
            $('#status').val(rsStr[6]);
            $('#status').trigger("chosen:updated");
            $('#shift_id').val(rsStr[0]);
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
			url: "<?php echo base_url();?>shift/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid 

		$('#reset').click(function(){
			$('#alert').hide();
			$('#company_name').val("");
			$('#company_name').trigger("chosen:updated");
			$('#shift_name').val("");
			$('#shift_start').val("");
			$('#shift_end').val("");
			$('#shift_capacity').val("");
			$('#status').val("1");
			$('#status').trigger("chosen:updated");
			$('#shift_id').val("");
		});// End reset
	});

	$('.save').click(function(){
        var companyName    	= $('#company_name').val();
        var shiftName     	= $('#shift_name').val();
        var shiftStart     	= $('#shift_start').val();
        var shiftEnd     	= $('#shift_end').val();
        var shiftCapacity   = $('#shift_capacity').val();
        var status          = $('#status').val();
        var shiftId       	= $('#shift_id').val();
	//alert(categoryName+categorySlug);
	$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>shift/AddRecord",
		data: "company-name="+companyName+"&shift-name="+shiftName+"&shift-start="+shiftStart+"&shift-end="+shiftEnd+"&shift-capacity="+shiftCapacity+"&status="+status+"&shift-id="+shiftId,
		success: function(option){
		//alert(option);
		$('#company_name').val("");
		$('#company_name').trigger("chosen:updated");
		$('#shift_name').val("");
		$('#shift_start').val("");
		$('#shift_end').val("");
		$('#shift_capacity').val("");
		$('#status').val("1");
		$('#status').trigger("chosen:updated");
		$('#shift_id').val("");
		$('#dataGrid').html(option);
		$('.show-create').hide();
		$('#alert').show();
		$('#alert').html('Successfully saved!');
		    }//Success
	    });// ajax
	return false;
	});// End save

	/* Pagination Next Page */
	function nextPage(frm, to, pno) {
	    //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>shift/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
	}
    </script>
</body>
</html>
