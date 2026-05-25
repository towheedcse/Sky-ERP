<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("leave");?></title>
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
                    <button type="button" class="btn btn-warning" data-dismiss="modal"> <?php echo $this->lang->line("cancel");?></button>
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
			<?php echo $this->lang->line("leave");?>
			<?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
			<?php }?>
		    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("leave");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("leave");?></h3>
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
		                            <label class="control-label" for="employee_id"><?php echo $this->lang->line("employee_name");?>:</label>
		                            
	                                <select name="employee_id" id="employee_id" class="chosen-select" required="" onChange="getEmployeeInfo(this.value)">
	                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("employee_name");?></option>
	                                    <?php foreach($equery->result() as $row){
	                                        echo '<option value="'.$row->employee_id.'">'.$row->employee_name.', '.$row->designation.'</option>';
	                                    }
	                                    ?>
	                                </select>
		                            
		                        </div>
		                    </div>
				            <div class="col-sm-2 col-md-2 col-lg-2">
		                        <div class="form-group required">
		                            <label class="control-label" for="leave_nature"><?php echo $this->lang->line("leave_nature");?>:</label>
		                            <select name="leave_nature" id="leave_nature" class="chosen-select" required="">
	                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("leave_nature");?></option>
	                                    <?php foreach($lquery->result() as $rowl){
	                                        echo '<option value="'.$rowl->category_id.'">'.$rowl->leave_type.'</option>';
	                                    }
	                                    ?>
	                                </select>
		                        </div>
		                    </div>
				            <div class="col-sm-2 col-md-2 col-lg-2">
		                        <div class="form-group required">
		                            <label class="control-label" for="leave_type"><?php echo $this->lang->line("leave_type");?>:</label>
		                            <select name="leave_type" id="leave_type" class="chosen-select" required="">
	                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("leave_type");?></option>
	                                    <option value="1">Full Day</option>
	                                    <option value="2">Half Day</option>
	                                    <option value="3">Early Out</option>
	                                    
	                                </select>
		                        </div>
		                    </div>
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <label class="control-label" for="application_date"><?php echo $this->lang->line("application_date");?></label>						
										
								<div class="input-group">
									<input type="text" class="form-control datepicker_mask" name="application_date" id="application_date">
									<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
                            </div>
		                </div>
		                <div class="row">
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <label class="control-label" for="leave_from"><?php echo $this->lang->line("leave_from");?></label>						
										
								<div class="input-group">
									<input type="text" class="form-control datepicker_mask" name="leave_from" id="leave_from">
									<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
                            </div>
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <label class="control-label" for="leave_to"><?php echo $this->lang->line("leave_to");?></label>						
										
								<div class="input-group">
									<input type="text" class="form-control datepicker_mask" name="leave_to" id="leave_to">
									<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="leave_purpose"><?php echo $this->lang->line("purpose_of_leave");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("purpose_of_leave");?>" name="leave_purpose" id="leave_purpose" class="form-control">
		                        </div>
		                    </div> 
		                </div>
		                <div class="row">
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group required">
		                            <label class="control-label" for="recommended_by"><?php echo $this->lang->line("recommended_by");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("recommended_by");?>" name="recommended_by" id="recommended_by" class="form-control">
		                        </div>
                            </div>
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group required">
		                            <label class="control-label" for="section_chief"><?php echo $this->lang->line("section_chief");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("section_chief");?>" name="section_chief" id="section_chief" class="form-control" value="<?php echo $section_chief;?>">
		                        </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="dept_head"><?php echo $this->lang->line("head_of_department");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("head_of_department");?>" name="dept_head" id="dept_head" class="form-control" value="<?php echo $dept_head;?>">
		                        </div>
		                    </div> 
		                </div>
		                <div class="row">
							<div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group required">
		                            <label class="control-label" for="leave_address"><?php echo $this->lang->line("leave_address");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("leave_address");?>" name="leave_address" id="leave_address" class="form-control">
		                        </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="leave_mobile"><?php echo $this->lang->line("mobile");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("mobile");?>" name="leave_mobile" id="leave_mobile" class="form-control">
		                        </div>
		                    </div> 
		                </div>
		                
		                
		                <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <button type="button" id="btnSave" style="margin-top: 2px;" class="btn btn-success save"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("save");?></button>
                            
                                <button type="reset" id="reset" style="margin-top: 2px;" class="btn btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
                            </div>
		                	<div class="col-sm-6 col-md-6 col-lg-6">
		                	   <input type="hidden" name="leave_id" class="form-control" id="leave_id">  
                            </div>
                             
		                </div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
		            <?php if($hasViewOption){?>
		            <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("emoloyee")." ".$this->lang->line("leave");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">					
							<div class="row">
								<div class="col-sm-4 col-md-4 col-lg-4">
									<div class="form-group"> 
										<label class="control-label" for="src-employee-id"><?php echo $this->lang->line("employee_name");?></label>
										<select name="src-employee-id" id="src-employee-id" class="chosen-select">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("employee_name");?></option>
    	                                    <?php foreach($equery->result() as $row){
    	                                        echo '<option value="'.$row->employee_id.'">'.$row->employee_name.', '.$row->designation.'</option>';
    	                                    }
    	                                    ?>
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
								<div class="col-sm-2 col-md-2 col-lg-2"> 
								
								<button type="button" class="btn btn-md btn-info search" style="margin-top:32px"><span class="glyphicon glyphicon-search"> <?php echo $this->lang->line("search");?></span></button>
																	
								</div>
							</div>
                        </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("leave")." ".$this->lang->line("list");?></h3>
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
	function resizeChosen(){
	    $(".chosen-container").each(function(){
		    $(this).attr('style', 'width: 100%');
	    });
	}
    
    $('#addnew').click(function() {
	 	$(".show-create").show();
    });
    
    $(document).ready(function(){
		//Start Chosen Responsive//
        resizeChosen();
        jQuery(window).on('resize', resizeChosen);
		$(".chosen-select").val('').trigger("chosen:updated");
		$('#alert').hide();
		$('.alert-success').hide();
		$('#alert-delete').hide();
		$('.show-create').hide();
	
		var employee_id=<?php echo $employee_id;?>;
		if(employee_id >0){
		    $('.show-create').show();
		}
        $('#employee_id').val(employee_id);
		$('#employee_id').trigger("chosen:updated");
		//Load dataGrid
		reloadDataGrid();
	
    });

	$('#reset').click(function(){
		$('#alert').hide();
	    $('#employee_id').val("").trigger("chosen:updated");
	    $('#leave_nature').val("").trigger("chosen:updated");
	    $('#leave_type').val("").trigger("chosen:updated");
	    $('#application_date').val("");
	    $('#leave_from').val("");
	    $('#leave_to').val("");
	    $('#leave_purpose').val("");
	    $('#recommended_by').val("");
	    $('#section_chief').val("");
	    $('#dept_head').val("");
	    $('#leave_address').val("");
	    $('#leave_mobile').val("");
	    $('#leave_id').val("");
	});// End reset
	
	function isNullAndUndef(variable) {
    	if(variable == null || variable == undefined || variable==""){
		return true;
		}else if(isNaN(variable)){ return true; }	
    }
   
	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#leave_id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
	    var delId = $('#leave_id').val();
	   	$('#leave_id').val("");
		if(delId!=""){
		    $.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>leavemanage/DelRecord",
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
        url: "<?php echo base_url();?>leavemanage/GetRecord",
        success: function(option){
        $('#dataGrid').html(option);
        $('#alert-delete').hide();
        }//Success
      });// End datagrid
      
	}
    
	function editRecord(id){
		$.ajax({
		type: 'POST',
		    url: "<?php echo base_url();?>leavemanage/FillRecord",
		    data: "id="+id,
		    success: function(option){
		    //alert(option);
		    rsStr = option.split("##&##");
		    //alert(rsStr[1]);
		    $('#employee_id').val(rsStr[1]).trigger("chosen:updated");
		    $('#leave_nature').val(rsStr[2]).trigger("chosen:updated");
		    $('#leave_type').val(rsStr[3]).trigger("chosen:updated");
		    $('#application_date').val(rsStr[4]);
		    $('#leave_from').val(rsStr[5]);
		    $('#leave_to').val(rsStr[6]);
		    $('#leave_purpose').val(rsStr[7]);
		    $('#recommended_by').val(rsStr[8]);
		    $('#section_chief').val(rsStr[9]);
		    $('#dept_head').val(rsStr[10]);
		    $('#leave_address').val(rsStr[11]);
		    $('#leave_mobile').val(rsStr[12]);
		    $('#leave_id').val(rsStr[0]);
		    $('.show-create').show();
		    $('#alert').show();
		    $('#alert').html('Ready to Edit!');
		    }//Success
		});// ajax
	        return false;
	}

	$('.save').click(function(){
		var employee_id     	= $('#employee_id').val();
		var leave_nature        = $('#leave_nature').val();
		var leave_type          = $('#leave_type').val();
		var application_date    = $('#application_date').val();
		var leave_from          = $('#leave_from').val();
		var leave_to            = $('#leave_to').val();
		var leave_purpose       = $('#leave_purpose').val().replace(/&/g,'U+0026');
		var recommended_by      = $('#recommended_by').val();
		var section_chief       = $('#section_chief').val();
		var dept_head           = $('#dept_head').val();
		var leave_address       = $('#leave_address').val().replace(/&/g,'U+0026');
		var leave_mobile        = $('#leave_mobile').val();
		var leave_id            = $('#leave_id').val();
		if(employee_id >0 && leave_type >0 && leave_nature >0 && application_date!="" && leave_from!="" && leave_to!=""){
    		$.ajax({
    		    type: 'POST',
    		    url: "<?php echo base_url();?>leavemanage/AddRecord",
    		    data: "employee_id="+employee_id+"&leave_type="+leave_type+"&leave_nature="+leave_nature+"&application_date="+application_date+"&leave_from="+leave_from+"&leave_to="+leave_to+"&leave_purpose="+leave_purpose+"&recommended_by="+recommended_by+"&section_chief="+section_chief+"&dept_head="+dept_head+"&leave_address="+leave_address+"&leave_mobile="+leave_mobile+"&leave_id="+leave_id,
    		    success: function(option){
    		    //alert(option);
        	    $('#employee_id').val("").trigger("chosen:updated");
        	    $('#leave_nature').val("").trigger("chosen:updated");
        	    $('#leave_type').val("").trigger("chosen:updated");
        	    $('#application_date').val("");
        	    $('#leave_from').val("");
        	    $('#leave_to').val("");
        	    $('#leave_purpose').val("");
        	    $('#recommended_by').val("");
        	    $('#section_chief').val("");
        	    $('#dept_head').val("");
        	    $('#leave_address').val("");
        	    $('#leave_mobile').val("");
        	    $('#leave_id').val("");
    		    $('#dataGrid').html(option);
    		    $('.show-create').hide();
    		    $('#alert').show();
    		    $('#alert').html('Successfully saved!');
    		    }//Success
    		});// ajax
		}
	    return false;
	});// End save

    
	$('.search').click(function(){
    	var employeeId      = $('#src-employee-id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>leavemanage/GetRecord",
            data: "src-employee-id="+employeeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo,
            success: function(option){
                $('#dataGrid').html(option);
            }//Success
        });// ajax
        return false;
    });// End search
    
	/* Pagination Next Page */
	function nextPage(frm, to, pno){
    	var employeeId      = $('#src-employee-id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>leavemanage/GetRecord",
		    data: "src-employee-id="+employeeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
		    success: function (option) {
		    $('#dataGrid').html(option);
		    }//Success
		});// End datagrid
        	return false;
	}
	
	function getEmployeeInfo(id){
		$.ajax({
		type: 'POST',
		    url: "<?php echo base_url();?>leavemanage/getAjaxEmployeeInfo",
		    data: "id="+id,
		    success: function(option){
		    //alert(option);
		    rsStr = option.split("##&##");
		    //alert(rsStr[1]);
		    $('#section_chief').val(rsStr[1]);
		    $('#dept_head').val(rsStr[2]);
		    $('#leave_address').val(rsStr[3]);
		    //$('#leave_mobile').val(rsStr[4]);
		    }//Success
		});// ajax
	        return false;
	}
	$('#btnDelete').click(function() {
		$('#deleteModal').modal('hide');
	});
    </script>
</body>
</html>
