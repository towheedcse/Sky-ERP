<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("od");?> </title>
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
               <?php echo $this->lang->line("outstation_duty");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("od");?></li>
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
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("od")." ".$this->lang->line("setup");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("od")." ".$this->lang->line("details");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
            		                    <div class="col-sm-4 col-md-4 col-lg-4">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="employee-id"><?php echo $this->lang->line("employee_name");?>:</label>
            		                            <div id="com_id">
            		                                <select name="employee-id" id="employee-id" class="chosen-select" onChange="getEmployeeDetails(this.value)" required="">
            		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("employee_name");?></option>
            		                                    <?php foreach($emquery->result() as $row){
            		                                        echo '<option value="'.$row->employee_id.'">'.$row->employee_name.' '.$row->designation.'</option>';
            		                                    }
            		                                    ?>
            		                                </select>
            		                            </div>
            		                        </div>
            		                    </div>
            		                    <div class="col-sm-4 col-md-4 col-lg-4">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="company-id"><?php echo $this->lang->line("company_name");?>:</label>
            		                            <div id="com_id">
            		                                <select name="company-id" id="company-id" class="chosen-select" required="" onChange="getBranchList(this.value,'branch-id')">
            		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company_name");?></option>
            		                                    <?php foreach($iquery->result() as $row){
            		                                        echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
            		                                    }
            		                                    ?>
            		                                </select>
            		                            </div>
            		                        </div>
            		                    </div>
            
            		                    <div class="col-sm-4 col-md-4 col-lg-4">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="branch-id"><?php echo $this->lang->line("branch_name");?>:</label>
        		                                <select name="branch-id" id="branch-id" class="chosen-select" required="">
        		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
        		                                    <?php foreach($bquery->result() as $row){
														echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
													}
													?>
        		                                </select>
            		                        </div>
            		                    </div>
            		                </div>
            		                <div class="row">
            							<div class="col-sm-4 col-md-4 col-lg-4">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="department-id"><?php echo $this->lang->line("department");?>:</label>
            		                            <select name="department-id" id="department-id" class="chosen-select" required="" onChange="GetSectionList(this.value,'section_id')">
            											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("department");?></option>
            											<?php foreach($dquery->result() as $row){
            												echo '<option value="'.$row->department_id.'">'.$row->department_name.'</option>';
            											}
            											?>
            									</select>
            		                        </div>
            		                    </div>
            		                    <div class="col-sm-4 col-md-4 col-lg-4">
            		                        <div class="form-group required">
												<label class="control-label" for="section_id"><?php echo $this->lang->line("section");?></label>
												<select name="section_id" id="section_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("section");?></option>
													
												</select>
											</div>
            		                    </div>
            		                    
            							<div class="col-sm-4 col-md-4 col-lg-4">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="session-id"><?php echo $this->lang->line("session_name");?>:</label>
            		                            <div id="com_id">
            		                                <select name="session-id" id="session-id" class="chosen-select" required="">
            		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session_name");?></option>
            											<?php foreach($squery->result() as $row){
            		                                        echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
            		                                    }
            		                                    ?>
            		                                </select>
            		                            </div>
            		                        </div>
                                        </div>
            		                </div>
            		                
									<div class="row">
            		                    <div class="col-sm-4 col-md-4 col-lg-4">
                                            <div class="form-group required">
                                                <label class="control-label" for="shift-id"><?php echo $this->lang->line("shift");?></label>
                                                <select name="shift-id" id="shift-id" class="chosen-select" required="" onChange="getEmployeeList('employee-id')">
                                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift");?></option>
                                                    <?php foreach($shquery->result() as $row){
                                                        echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
            		                    </div>
            
            		                    <div class="col-sm-8 col-md-8 col-lg-8">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="remarks"><?php echo $this->lang->line("remarks");?>:</label>
            		                            <input type="text" placeholder="<?php echo $this->lang->line("remarks");?>" name="remarks" id="remarks" class="form-control">
            		                        </div>
            		                    </div>
            		                </div>
            		                <div class="row">
            							<div class="col-sm-4 col-md-4 col-lg-4">
                                            <label class="control-label" for="date"><?php echo $this->lang->line("od_form");?></label>						
            										
            								<div class="input-group">
            									<input type="text" class="form-control datepicker_mask" name="od_from" id="od_from">
            									<div class="input-group-prepend">
            									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
            									</div>
            								</div>
                                        </div>
            							<div class="col-sm-4 col-md-4 col-lg-4">
                                            <label class="control-label" for="date"><?php echo $this->lang->line("od_to");?></label>						
            										
            								<div class="input-group">
            									<input type="text" class="form-control datepicker_mask" name="od_to" id="od_to">
            									<div class="input-group-prepend">
            									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
            									</div>
            								</div>
                                        </div>
                                        
            							<div class="col-sm-2 col-md-2 col-lg-2">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="in_time"><?php echo $this->lang->line("in_time");?></label>
            		                            <div class="input-group">
            		                            	<input type="text" class="form-control shift_start_timepicker_mask" required="" placeholder="<?php echo $this->lang->line("in_time");?>" name="in_time" id="in_time">
            		                            	<div class="input-group-prepend">
            					                      <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
            					                    </div>
            									</div>
            		                            
            		                        </div>
            		                    </div>
            		                    <div class="col-sm-2 col-md-2 col-lg-2">
            		                        <div class="form-group required">
            		                            <label class="control-label" for="in_time"><?php echo $this->lang->line("out_time");?></label>
            		                            <div class="input-group">
            		                            	<input type="text" class="form-control shift_end_timepicker_mask" required="" placeholder="<?php echo $this->lang->line("out_time");?>" name="out_time" id="out_time">
            		                            	<div class="input-group-prepend">
            					                      <span class="input-group-text"><i class="fa fa-clock-o"></i></span>
            					                    </div>
            					                </div>    
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
							<input type="hidden" name="od_id" id="od_id" value="">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
          
				<?php if($hasViewOption){?>
				    
					<div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("emoloyee")." ".$this->lang->line("od");?></h3>
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
		                                    <?php foreach($emquery->result() as $row){
		                                        echo '<option value="'.$row->employee_id.'">'.$row->employee_name.' '.$row->designation.'</option>';
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("od")." ".$this->lang->line("list");?></h3>
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
        $('#od_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#od_id').val();
    $('#od_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>outstationduty/DelRecord",
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
            url: "<?php echo base_url();?>outstationduty/GetRecord",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    
	$('.save-record').click(function(){
        var company_id      = $('#company-id').val();
        var branch_id       = $('#branch-id').val();
        var department_id   = $('#department-id').val();
        var section_id      = $('#section_id').val();
        var session_id      = $('#session-id').val();
        var shift_id        = $('#shift-id').val();
        var employee_id     = $('#employee-id').val();
        var od_from         = $('#od_from').val();
        var od_to           = $('#od_to').val();
        var in_time         = $('#in_time').val();
        var out_time        = $('#out_time').val();
        var remarks         = $('#remarks').val();
        var od_id           = $('#od_id').val();
        //alert(categoryName+categorySlug);
        if(company_id >0 && branch_id >0 && department_id >0 && section_id >0 && session_id >0 && shift_id >0 && employee_id >0 && od_from!="" && od_to!="" && in_time!="" && out_time!=""){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>outstationduty/AddRecord",
            data: "company_id="+company_id+"&branch_id="+branch_id+"&department_id="+department_id+"&section_id="+section_id+"&session_id="+session_id+"&shift_id="+shift_id+"&employee_id="+employee_id+"&od_from="+od_from+"&od_to="+od_to+"&in_time="+in_time+"&out_time="+out_time+"&remarks="+remarks+"&od_id="+od_id,
            success: function(option){
            //alert(option);
            $('#company-id').val("").trigger("chosen:updated");
            $('#branch-id').val("").trigger("chosen:updated");
            $('#department-id').val("").trigger("chosen:updated");
            $('#section_id').val("").trigger("chosen:updated");
            $('#session-id').val("").trigger("chosen:updated");
            $('#shift-id').val("").trigger("chosen:updated");
            $('#od_from').val("");
            $('#od_to').val("");
            $('#in_time').val("");
            $('#out_time').val("");			
            $('#od_id').val("");
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
            url: "<?php echo base_url();?>outstationduty/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[12]);
            $('#company-id').val(rsStr[1]).trigger("chosen:updated");
            $('#branch-id').val(rsStr[2]).trigger("chosen:updated");
            $('#department-id').val(rsStr[3]).trigger("chosen:updated");
            GetEditSectionList(rsStr[1],rsStr[3],'section_id',rsStr[4]);
            $('#section_id').val(rsStr[4]).trigger("chosen:updated");
            $('#session-id').val(rsStr[5]).trigger("chosen:updated");
            $('#shift-id').val(rsStr[6]).trigger("chosen:updated");
            getEditEmployeeList(rsStr[1],rsStr[2],rsStr[3],rsStr[4],rsStr[6],'employee-id',rsStr[7]);
            $('#employee-id').val(rsStr[7]).trigger("chosen:updated");
            $('#od_from').val(rsStr[8]);
            $('#od_to').val(rsStr[9]);
            $('#in_time').val(rsStr[10]);
            $('#out_time').val(rsStr[11]);
            $('#remarks').val(rsStr[12]);			
            $('#od_id').val(rsStr[0]);						
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
          $('#od_id').val("");
		  location.reload(); 
    });// End reset
     
    
	$('.search').click(function(){
    	var employeeId      = $('#src-employee-id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>outstationduty/GetRecord",
            data: "src-employee-id="+employeeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo,
            success: function(option){
                $('#dataGrid').html(option);
            }//Success
        });// ajax
        return false;
    });// End search
    
    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
    	var employeeId      = $('#src-employee-id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>outstationduty/GetRecord",
            data: "src-employee-id="+employeeId+"&srcFrom="+srcFrom+"&srcTo="+srcTo+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
    function getEmployeeDetails(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetEmployeeDetails",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[4]); alert(rsStr[5]);
            $('#company-id').val(rsStr[0]).trigger("chosen:updated");
            $('#branch-id').val(rsStr[1]).trigger("chosen:updated");
            $('#department-id').val(rsStr[2]).trigger("chosen:updated");
            setTimeout(function() {				 
		      GetEditSectionList(rsStr[0],rsStr[2],'section_id',rsStr[3]);
            $('#section_id').val(rsStr[3]).trigger("chosen:updated");
		    }, 500);
            $('#shift-id').val(rsStr[4]).trigger("chosen:updated");
            $('#session-id').val(rsStr[5]).trigger("chosen:updated");
            $('#od_from').val(rsStr[6]);
            $('#in_time').val(rsStr[7]);
            $('#out_time').val(rsStr[8]);
            }//Success
        });// ajax
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
    function GetSectionList(department_id,placement,section_id=0){
	    var company_id = $("#company-id").val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetAjaxSectionList",
            data: "company-id="+company_id+"&department-id="+department_id+"&section-id="+section_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success
        
        });// ajax
        return false;
    }
    
    function getEmployeeList(placement,employee_id=0){
        var company_id      = $('#company-id').val();
        var branch_id       = $('#branch-id').val();
        var department_id   = $('#department-id').val();
        var section_id      = $('#section_id').val();
        var shift_id        = $('#shift-id').val();
        //alert(company_id+"b-"+branch_id+",d-"+department_id+",s-"+section_id+",sf"+shift_id);
        if(company_id >0 && branch_id >0 && department_id >0 && section_id >0 && shift_id >0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetAjaxEmployeeList",
            data: "company_id="+company_id+"&branch_id="+branch_id+"&department_id="+department_id+"&section_id="+section_id+"&shift_id="+shift_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          
        }
        return false;
    }
    
    
    function GetEditSectionList(company_id,department_id,placement,section_id=0){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetAjaxSectionList",
            data: "company-id="+company_id+"&department-id="+department_id+"&section-id="+section_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success
        
        });// ajax
        return false;
    }
    
    function getEditEmployeeList(company_id,branch_id,department_id,section_id,shift_id,placement,employee_id=0){
       
        //alert(company_id+"b-"+branch_id+",d-"+department_id+",s-"+section_id+",sf"+shift_id);
        if(company_id >0 && branch_id >0 && department_id >0 && section_id >0 && shift_id >0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>employee/GetAjaxEmployeeList",
            data: "company_id="+company_id+"&branch_id="+branch_id+"&department_id="+department_id+"&section_id="+section_id+"&shift_id="+shift_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          
        }
        return false;
    }
    </script>
</body>
</html>
