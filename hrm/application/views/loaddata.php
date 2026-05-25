<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line('attendance');?></title>
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
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("attendance")." ".$this->lang->line("data_load");?></h1> 
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("data_load");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("data_load");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
                        <form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>loaddata/AddRecord">
		                <div class="row">
		                    <div class="col-sm-3 col-md-3 col-lg-3">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-company-id"><?php echo $this->lang->line("company_name");?>:</label>
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
		                    <div class="col-sm-3 col-md-3 col-lg-3">
		                        <div class="form-group">
		                            <label class="control-label" for="branch-id"><?php echo $this->lang->line("branch_name");?>:</label>
		                            
		                            <select name="branch-id" id="branch-id" class="chosen-select">
		                               <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
		                            </select>
		                        </div>
		                    </div>
		                    <div class="col-sm-3 col-md-3 col-lg-3">
		                        <div class="form-group required">
		                            <label class="control-label" for="session-id"><?php echo $this->lang->line("session_name");?>:</label>
		                            <select name="session-id" id="session-id" class="chosen-select" required="">
										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session_name");?></option>
										<?php foreach($squery->result() as $row){
											echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
										}
										?>		                                    
									</select>
		                        </div>
		                    </div> 
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group required">
                                    <label class="control-label" for="shift-id"><?php echo $this->lang->line("shift");?></label>
                                    <select name="shift-id" id="shift-id" class="chosen-select" required="">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift");?></option>
                                        <?php foreach($shquery->result() as $row){
                                            echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
		                </div>
		                <div class="row">
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <label class="control-label" for="date-from"><?php echo $this->lang->line("from_date");?></label>						
										
								<div class="input-group">
									<input type="text" class="form-control datepicker_mask" name="date-from" id="date-from">
									<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
                            </div>
							<div class="col-sm-3 col-md-3 col-lg-3">
                                <label class="control-label" for="date-to"><?php echo $this->lang->line("to_date");?></label>				
										
								<div class="input-group">
									<input type="text" class="form-control datepicker_mask" name="date-to" id="date-to">
									<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-calendar"></i></span>
									</div>
								</div>
                            </div>
							<div class="col-sm-6 col-md-6 col-lg-6">
								<div class="form-group required">
									<label class="control-label"><?php echo $this->lang->line("data_file");?></label>
									<div class="input-group">
										<span class="input-group-btn">
										   <span class="btn btn-default btn-file">
												Browse… <input type="file" required="" name="data_file" id="data_file" class="form-control" style="height: 25px;">
										   </span>
										  <div id ="optional_image3" style="padding-left:3px; width:4.1%; float:right">
											
										  </div>										  
										</span>
										<input type="text" class="form-control" readonly>
									</div>
									
								</div>
							</div>
		                </div>
											
						<div class="row">
							<div class="col-sm-2 col-md-2 col-lg-2">							 
							 <button type="submit" style="margin-top:7px;" name="process" class="btn btn-block btn-success"><i class="fa fa-upload"></i> <?php echo $this->lang->line("upload");?></button>
							</div>
						</div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
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
		
		$('#company-id').val(1);
		$('#branch-id').val(1);
		$('#session-id').val(4);
		$('#shift-id').val(1);
		
        $('.FeesList').hide();
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
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#img-upload').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imgInp").change(function(){
            readURL(this);
        });
		$('#alert-delete').hide();
		$('#alert').hide();
	});
	
    $('#reset').click(function(){
		$('#src-company-id').val("").trigger("chosen:updated");
		$('#src-branch-id').val("").trigger("chosen:updated");
		$('#src-session-id').val("").trigger("chosen:updated");	
		$('#src-shift-id').val("").trigger("chosen:updated");	
		$('#src-attendance-date').val("").trigger("chosen:updated");
		$("#in_time").val("");
		$("#out_time").val("");
	});   	
    
    function isNullAndUndefined(variable){
		if(variable == null || variable == undefined || variable == ""){
		    return true;
		}else if(isNaN(variable)){
		    return true;
		}
    }
 	$('.process').click(function(){
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var attendance_date	= $('#src-attendance-date').val();
		if(company_id >0 && branch_id >0 && session_id >0 && shift_id>0 && attendance_date!=""){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>student_attendance/GetClassAttendanceRecord",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&class-id=0&group-id=0&shift-id="+shift_id+"&section-id=0&attendance-date="+attendance_date,
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
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var attendance_date	= $('#src-attendance-date').val();
		
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>student_attendance/GetClassAttendanceRecord",
		    data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&class-id=0&group-id=0&shift-id=0&section-id=0&attendance-date="+attendance_date+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
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
    function getGroupList(class_id,placement,group_id=0){
	  var institute_id       = $('#src-company-id').val();
	  if(isNullAndUndefined(institute_id)){ institute_id=0;}
	  $.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>groups/GetAjaxClassGroupList",
		data: "institute-id="+institute_id+"&class-id="+class_id+"&group-id="+group_id,
		success: function(option){
			//alert(option);
			$('#'+placement).html(option);
			$('#'+placement).trigger('chosen:updated');
		}//Success

	  });// ajax
	  return false;
    }	
	function getExamList(palsement,exam_type_id=0){
		var institute_id     = $('#src-company-id').val();
		if(isNullAndUndefined(institute_id)){ institute_id=0;}
		var branch_id        = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var version_id       = $('#src-version-id').val();
		if(isNullAndUndefined(version_id)){ version_id=0;}
		var class_id         = $('#src-class-id').val();
		if(isNullAndUndefined(class_id)){ class_id=0;}
		var group_id		= $('#src-group-id').val();
		if(isNullAndUndefined(group_id)){ group_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}		
		if(institute_id >0 && branch_id >0 && session_id >0 && version_id >0 && class_id >0 && group_id >0 && shift_id >0){
		  $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>exam_attendance/GetAjaxExamList",
            data: "institute-id="+institute_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id="+version_id+"&class-id="+class_id+"&group-id="+group_id+"&shift-id="+shift_id+"&examtype-id="+exam_type_id,
            success: function(option){
             $('#'+palsement).html(option);
			 $('#'+palsement).trigger("chosen:updated");
            }//Success
          });// End datagrid
		}//end if
	}	
	function getSectionList(section_id=0){
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var version_id       = $('#src-version-id').val();
		if(isNullAndUndefined(version_id)){ version_id=0;}
		var class_id         = $('#src-class-id').val();
		if(isNullAndUndefined(class_id)){ class_id=0;}
		var group_id		= $('#src-group-id').val();
		if(isNullAndUndefined(group_id)){ group_id=0;}
		var shift_id		= $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		
		if(company_id >0 && branch_id >0 && session_id >0 && version_id >0 && class_id >0 && group_id >0 && shift_id >0){
		  $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>admission/GetSectionList",
            data: "institute_id="+company_id+"&branch_id="+branch_id+"&session_id="+session_id+"&version_id="+version_id+"&class_id="+class_id+"&group_id="+group_id+"&shift_id="+shift_id+"&section_id="+section_id,
            success: function(option){
             $('#src-section-id').html(option);
			 $('#src-section-id').trigger("chosen:updated");
            }//Success
          });// End datagrid
		}//end if
	}
		
	function getSubjectList(subject_id=0){
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id        = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var version_id       = $('#src-version-id').val();
		if(isNullAndUndefined(version_id)){ version_id=0;}
		var class_id         = $('#src-class-id').val();
		if(isNullAndUndefined(class_id)){ class_id=0;}
		var group_id		 = $('#src-group-id').val();
		if(isNullAndUndefined(group_id)){ group_id=0;}
		var shift_id		 = $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var examtype_id		 = $('#src-examtype-id').val();
		if(isNullAndUndefined(examtype_id)){ examtype_id=0;}
		
		if(company_id >0 && branch_id >0 && session_id >0 && version_id >0 && class_id >0 && group_id >0 && shift_id >0 && examtype_id >0){
		  $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>exam_attendance/GetAjaxSubjectList",
            data: "institute-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id="+version_id+"&class-id="+class_id+"&group-id="+group_id+"&shift-id="+shift_id+"&examtype-id="+examtype_id+"&subject-id="+subject_id,
            success: function(option){
             $('#src-examsubject-id').html(option);
			 $('#src-examsubject-id').trigger("chosen:updated");
            }//Success
          });// End datagrid
		}//end if
	}
    </script>
</body>
</html>
