<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("short_leave");?> </title>
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
               <?php echo $this->lang->line("short_leave");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("short_leave");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid --> 
							  
					<div id="alert" class="alert alert-success"></div>
					<form id="InputForm">
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("short_leave");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("short_leave")." ".$this->lang->line("details");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								    <?php if($hasApproveOption){?>
									<div class="row">
            		                    <div class="col-sm-8 col-md-8 col-lg-8">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="employee-id"><?php echo $this->lang->line("employee_name");?>:</label>
        		                                <select name="employee-id" id="employee-id" class="chosen-select" required="">
        		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("employee_name");?></option>
        		                                    <?php foreach($emquery->result() as $row){
        		                                        echo '<option value="'.$row->employee_id.'">'.$row->employee_name.' '.$row->designation.'</option>';
        		                                    }
        		                                    ?>
        		                                </select>
            		                        </div>
            		                    </div>
            		                	<div class="col-sm-4 col-md-4 col-lg-4">
            		                		<div class="form-group required">
                                                <label class="control-label" for="status"><?php echo $this->lang->line("status");?>:</label>
                                                <select name="status" id="status" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("status");?>">
                                                    <option value="0"><?php echo $this->lang->line("unapproved");?></option>
                                                    <option value="1"><?php echo $this->lang->line("approved");?></option>
                                                </select>
                                            </div>
                                        </div>
            		                </div>
            		                <?php }else{?>
            		                <input type="hidden" name="employee-id" id="employee-id" value="<?php echo $employee_id;?>">
            		                <?php }?>
									<div class="row">
            		                    <div class="col-sm-4 col-md-4 col-lg-4">
                                            <label class="control-label" for="application_date"><?php echo $this->lang->line("application_date");?></label>						
										
            								<div class="input-group">
            									<input type="text" class="form-control datepicker_mask" name="application_date" id="application_date">
            									<div class="input-group-prepend">
            									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
            									</div>
            								</div>
            		                    </div>
            							<div class="col-sm-4 col-md-4 col-lg-4">
                                            <label class="control-label" for="date"><?php echo $this->lang->line("leave_date");?></label>						
            										
            								<div class="input-group">
            									<input type="text" class="form-control datepicker_mask" name="leave_date" id="leave_date">
            									<div class="input-group-prepend">
            									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
            									</div>
            								</div>
                                        </div>
                                        
            							<div class="col-sm-2 col-md-2 col-lg-2">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="time_from"><?php echo $this->lang->line("time_from");?></label>
            		                            <div class="input-group">
            		                            	<input type="text" class="form-control shift_start_timepicker_mask" required="" placeholder="<?php echo $this->lang->line("time_from");?>" name="time_from" id="time_from">
            		                            	<div class="input-group-prepend">
            					                      <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
            					                    </div>
            									</div>
            		                            
            		                        </div>
            		                    </div>
            		                    <div class="col-sm-2 col-md-2 col-lg-2">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="time_to"><?php echo $this->lang->line("time_to");?></label>
            		                            <div class="input-group">
            		                            	<input type="text" class="form-control shift_end_timepicker_mask" required="" placeholder="<?php echo $this->lang->line("time_to");?>" name="time_to" id="time_to">
            		                            	<div class="input-group-prepend">
            					                      <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
            					                    </div>
            					                </div>    
            		                        </div>
            		                    </div>
            		                </div>
            		                <div class="row">
            		                    <div class="col-sm-8 col-md-8 col-lg-8">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="purpose_of_leave"><?php echo $this->lang->line("purpose_of_leave");?>:</label>
            		                            <input type="text" placeholder="<?php echo $this->lang->line("purpose_of_leave");?>" name="purpose_of_leave" id="purpose_of_leave" class="form-control">
            		                        </div>
            		                    </div>
            		                	<div class="col-sm-4 col-md-4 col-lg-4">
            		                		<div class="form-group required">
                                                <label class="control-label" for="leave_type"><?php echo $this->lang->line("leave_type");?>:</label>
                                                <select name="leave_type" id="leave_type" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("status");?>">
                                                    <option value="1"><?php echo $this->lang->line("short_leave");?></option>
                                                    <option value="2"><?php echo $this->lang->line("early_out");?></option>
                                                </select>
                                            </div>
                                        </div> 
            		                </div>
									<div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<div class="form-group">
												<br>
												<button type="submit" class="btn btn-success save-record"><i class="fa fa-save"></i> Save</button>
												<button type="button" class="btn btn-danger cancel-record"><i class="fa fa-close"></i> Cancel</button>
											</div>
										</div>
									</div>
								</div>
								
							</div> <!-- End Card -->
							<input type="hidden" name="sl_id" id="sl_id" value="">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
          
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("short_leave")." ".$this->lang->line("list");?></h3>
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
    
	//jQuery('#datetimepicker').datetimepicker();	
	
    $(document).ready( function() {
        reloadDataGrid();
		$('#alert').hide(); 
    });
	function isNullAndUndef(variable) {
    	if(variable == null || variable == undefined || variable==""){
		return true;
		}else if(isNaN(variable)){ return true; }	
    }
	
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#sl_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#sl_id').val();
    $('#sl_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>shortleave/DelRecord",
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
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>shortleave/GetRecord",
            data: "leave_type=1",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    
	$('.save-record').click(function(){
        var employee_id     = $('#employee-id').val();
        var application_date= $('#application_date').val();
        var purpose_of_leave= $('#purpose_of_leave').val().replace(/&/g,'U+0026');
        var leave_type      = $('#leave_type').val();
        var leave_date      = $('#leave_date').val();
        var time_from       = $('#time_from').val();
        var time_to         = $('#time_to').val();
        var od_id           = $('#od_id').val();
        //alert(categoryName+categorySlug);
        if(employee_id >0 && application_date!="" && purpose_of_leave!="" && leave_date!="" && time_from!="" && time_to !=""){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>shortleave/AddRecord",
            data: "employee_id="+employee_id+"&application_date="+application_date+"&purpose_of_leave="+purpose_of_leave+"&leave_date="+leave_date+"&time_from="+time_from+"&time_to="+time_to+"&sl_id="+sl_id,
            success: function(option){
            //alert(option);
            
            $('#employee_id').val("");
            <?php if($employee_id >0){?>
            $('#employee_id').trigger("chosen:updated");
            <?php }?>
            $('#application_date').val("");
            $('#purpose_of_leave').val("");
            $('#leave_date').val("");
            $('#time_from').val("");
            $('#time_to').val("");		
            $('#sl_id').val("");
            $('#dataGrid').html(option);
	        $('.show-create').hide();
            $('#alert').show();
            $('#alert').html('Successfully saved!');
            }//Success
        });// ajax
        }//end if
        return false;
	});// End save
	
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>shortleave/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[12]);
            
            $('#employee_id').val(rsStr[1]);
            <?php if($employee_id >0){?>
            $('#employee_id').trigger("chosen:updated");
            <?php }?>
            $('#application_date').val(rsStr[2]);
            $('#purpose_of_leave').val(rsStr[3]);
            $('#leave_date').val(rsStr[4]);
            $('#time_from').val(rsStr[5]);
            $('#time_to').val(rsStr[6]);		
            $('#sl_id').val(rsStr[0]);						
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
	    $('#st-danger-alert').hide();
        jQuery(window).on('resize', resizeChosen);
        $(".chosen-select").val('').trigger("chosen:updated");
        //End Chosen Responsive//
        
        $('#btnDelete').click(function() {
            $('#deleteModal').modal('hide');
        });
        $('.show-create').hide();
        $('#alert-delete').hide();
        $('#alert').hide();

        //Load dataGrid
        reloadDataGrid();
    }); 

    $('.cancel-record').click(function(){
          $('#alert').hide();
          $('#sl_id').val("");
		  location.reload(); 
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>shortleave/GetRecord",
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
