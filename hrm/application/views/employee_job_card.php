<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line('job_card');?></title>
    <?php require('csslinks4admin.php');?>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">    
    <?php require('adminheader.php');?>
    <?php require('leftmenu.php');?>
    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
              <div class="container-fluid">
                <div id="alert" class="alert alert-success"></div>
                <div id="alert-delete" class="alert alert-danger"></div>
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("employee")." ".$this->lang->line("job_card");?></h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("job_card")." ".$this->lang->line("report");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("job_card");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
                            <form>
		                <div class="row">
		                    
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-employee-id"><?php echo $this->lang->line("employee_name");?>:</label>
		                           
	                                <select name="src-employee-id" id="src-employee-id" class="chosen-select" onChange="getEmployeeDetails(this.value)" required="">
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
		                            <label class="control-label" for="src-company-id"><?php echo $this->lang->line("company_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-company-id" id="src-company-id" class="chosen-select" required="" onChange="getBranchList(this.value,'src-branch-id')">
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
		                            <label class="control-label" for="src-department-id"><?php echo $this->lang->line("department");?>:</label>
		                            <div id="com_id">
		                                <select name="src-department-id" id="src-department-id" class="chosen-select" required="">
											<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("department");?></option>
											<?php foreach($dquery->result() as $row){
												echo '<option value="'.$row->department_id.'">'.$row->department_name.'</option>';
											}
											?>
										</select>
		                            </div>
		                        </div>
		                    </div>
		                    <!--div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-branch-id"><?php //echo $this->lang->line("branch_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-branch-id" id="src-branch-id" class="chosen-select" required="">
		                                    <option value=""><?php //echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
		                                    
		                                </select>
		                            </div>
		                        </div>
		                    </div-->
		                </div>
		                <div class="row">

		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-session-id"><?php echo $this->lang->line("session_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-session-id" id="src-session-id" class="chosen-select" required="">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session_name");?></option>
											<?php foreach($squery->result() as $row){
		                                        echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
		                                    }
		                                    ?>
		                                    
		                                </select>
		                            </div>
		                        </div>
		                    </div>
							
							<div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group required">
                                    <label class="control-label" for="src-shift-id"><?php echo $this->lang->line("shift");?></label>
                                    <select name="src-shift-id" id="src-shift-id" class="chosen-select" required="">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift");?></option>
                                        <?php foreach($shquery->result() as $row){
                                            echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
							
							<div class="col-sm-4 col-md-4 col-lg-4">
		                            <label class="control-label" for="src-month-name"><?php echo $this->lang->line("month_name");?></label>
                                    <select name="src-month-name" id="src-month-name" class="chosen-select" required="">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("month_name");?></option>
                                        <option value="01"><?php echo $this->lang->line("jan");?></option>
                                        <option value="02"><?php echo $this->lang->line("feb");?></option>
                                        <option value="03"><?php echo $this->lang->line("mar");?></option>
                                        <option value="04"><?php echo $this->lang->line("apr");?></option>
                                        <option value="05"><?php echo $this->lang->line("may");?></option>
                                        <option value="06"><?php echo $this->lang->line("jun");?></option>
                                        <option value="07"><?php echo $this->lang->line("jul");?></option>
                                        <option value="08"><?php echo $this->lang->line("aug");?></option>
                                        <option value="09"><?php echo $this->lang->line("sep");?></option>
                                        <option value="10"><?php echo $this->lang->line("oct");?></option>
                                        <option value="11"><?php echo $this->lang->line("nov");?></option>
                                        <option value="12"><?php echo $this->lang->line("dec");?></option>                                        
                                    </select>
		                    </div>
		                </div>
												
						<div class="row">
							<div class="col-sm-2 col-md-2 col-lg-2">							 
							 <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>
							</div>
                            <?php if($hasPrintOption){?>
							<div class="col-sm-2 col-md-2 col-lg-2">
            				  <button type="button" onclick="PrintElem('#dataGrid')" style="margin-top:7px" class="btn btn-block btn-primary print"><span class="glyphicon glyphicon-print"> Print</span></button>
            				</div>
                            <?php }?>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("employee")." ".$this->lang->line("attendance")." ".$this->lang->line("list");?></h3>
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

	$(document).ready(function(){
		//Start Chosen Responsive//
		resizeChosen();
		jQuery(window).on('resize', resizeChosen);
		$(".chosen-select").val('').trigger("chosen:updated");
		//End Chosen Responsive//
		
		$('#alert-delete').hide();
		$('#alert').hide();
		
		//==== Start Temp Set =====		
	    $('#src-company-id').val("1").trigger("chosen:updated");
	    getBranchList(1,'src-branch-id',1);
	    $('#src-branch-id').val("1").trigger("chosen:updated");
	    $('#src-session-id').val("1").trigger("chosen:updated");
	});
	
    $('#reset').click(function(){
		$('#src-company-id').val("").trigger("chosen:updated");
		$('#src-branch-id').val("").trigger("chosen:updated");
		$('#src-session-id').val("").trigger("chosen:updated");		
		$('#src-department-id').val("").trigger("chosen:updated");
		$('#src-shift-id').val("").trigger("chosen:updated");
		$('#src-attendance-date').val("").trigger("chosen:updated");
	});
	
	
    function isNullAndUndefined(variable){
		if(variable == null || variable == undefined || variable == ""){
		    return true;
		}else if(isNaN(variable)){
		    return true;
		}
    }
 	$('.search').click(function(){
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = 0;// $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var department_id       = $('#src-department-id').val();
		if(isNullAndUndefined(department_id)){ department_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var employee_id     = $('#src-employee-id').val();
		var attendance_month= $('#src-month-name').val(); 
		if(company_id >0 && session_id >0 && department_id >0 && shift_id>0 && employee_id >0 && attendance_month >0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>staff_attendance/GetMonthlyJobCardRecord",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&department-id="+department_id+"&shift-id="+shift_id+"&employee-id="+employee_id+"&attendance-month="+attendance_month,
            		success: function(option){
                		$('#dataGrid').html(option);
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
		var company_id      = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var department_id       = $('#src-department-id').val();
		if(isNullAndUndefined(department_id)){ department_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var attendance_date	= $('#src-attendance-date').val();
		if(isNullAndUndefined(attendance_date)){ attendance_date=0;}

		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>staff_attendance/GetDailyAttendanceRecord",
		    data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&department-id="+department_id+"&shift-id="+shift_id+"&attendance-date="+attendance_date+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
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
            $('#src-company-id').val(rsStr[0]).trigger("chosen:updated");
            $('#src-department-id').val(rsStr[1]).trigger("chosen:updated");
            $('#src-department-id').val(rsStr[2]).trigger("chosen:updated");
            
            $('#src-shift-id').val(rsStr[4]).trigger("chosen:updated");
            $('#src-session-id').val(rsStr[5]).trigger("chosen:updated");
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
    </script>
</body>
</html>
