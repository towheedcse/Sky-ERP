<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line('user_list');?></title>
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
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("user_list");?></h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("user_list");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("user")." ".$this->lang->line("marksheet");?></h3>
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
		                    <div class="col-sm-2 col-md-2 col-lg-2">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-branch-id"><?php echo $this->lang->line("branch_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-branch-id" id="src-branch-id" class="chosen-select" required="">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
		                                    
		                                </select>
		                            </div>
		                        </div>
		                    </div>

		                    <div class="col-sm-2 col-md-2 col-lg-2">
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
							<div class="col-sm-2 col-md-2 col-lg-2">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-version-id"><?php echo $this->lang->line("version_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-version-id" id="src-version-id" class="chosen-select" required="">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("version_name");?></option>
		                                    <?php foreach($vquery->result() as $row){
		                                        echo '<option value="'.$row->version_id.'">'.$row->version_name.'</option>';
		                                    }
		                                    ?>
		                                </select>
		                            </div>
		                        </div>
		                    </div>
							<div class="col-sm-2 col-md-2 col-lg-2">
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
		                </div>
		                <div class="row">		                    
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-class-id"><?php echo $this->lang->line("class_name");?>:</label>
		                            <select name="src-class-id" id="src-class-id" class="chosen-select" onChange="getGroupList(this.value,'src-group-id')" required="">
										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("class_name");?></option>
										<?php foreach($cquery->result() as $row){
											echo '<option value="'.$row->class_id.'">'.$row->class_name.'</option>';
										}
										?>
										
									</select>
		                        </div>
		                    </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group required">
                                    <label class="control-label" for="src-group-id"><?php echo $this->lang->line("group");?></label>
                                    <select name="src-group-id" id="src-group-id" class="chosen-select" required="" onChange="getSectionList()">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("group");?></option>
                                        <?php foreach($gquery->result() as $row){
                                            echo '<option value="'.$row->group_id.'">'.$row->group_name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group required">
                                    <label class="control-label" for="src-section-id"><?php echo $this->lang->line("section_name");?></label>
                                    <select name="src-section-id" id="src-section-id" class="chosen-select" required="">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("section_name");?></option>
                                        
                                    </select>
                                </div>
                            </div>
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label class="control-label" for="src-user-type"><?php echo $this->lang->line("user_type");?></label>
                                    <select name="src-user-type" id="src-user-type" class="chosen-select" required="">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("user_type");?></option>
                                        <option value="1"><?php echo $this->lang->line("student");?></option>
                                        <option value="2"><?php echo $this->lang->line("employee");?></option>
                                        
                                    </select>
                                </div>
                            </div>
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group">
                                    <label class="control-label" for="src-status"><?php echo $this->lang->line("status");?></label>
                                    <select name="src-status" id="src-status" class="chosen-select" required="">
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("status");?></option>
                                        <option value="1"><?php echo $this->lang->line("active");?></option>
                                        <option value="0"><?php echo $this->lang->line("inactive");?></option>
                                        
                                    </select>
                                </div>
                            </div>
		                </div>
												
						<div class="row">
							<div class="col-sm-2 col-md-2 col-lg-2">
							 <button type="button" style="margin-top: 7px;" class="btn btn-block btn-md btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>							 
							</div>							
							<?php if($hasPrintOption){?> 			
							<div class="col-sm-2 col-md-2 col-lg-2">
							<button type="button" onclick="PrintElem('#dataGrid')" style="margin-top:7px" class="btn btn-md btn-block btn-primary print"><i class="fas fa-print"></i> <?php echo $this->lang->line("print");?></button>
							</div>
							<?php }?>							
							<?php if($hasDownloadOption){?> 			
							<div class="col-sm-2 col-md-2 col-lg-2">
							<button type="button" onclick="DownloadPDF()" style="margin-top:7px" class="btn btn-md btn-block btn-info"><i class="fas fa-print"></i> <?php echo $this->lang->line("download");?></button>
							</div>
							<?php }?>							
							<div class="col-sm-2 col-md-2 col-lg-2">
							 <button type="reset" id="reset" style="margin-top: 7px;" class="btn btn-block btn-md btn-warning clear"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
							</div>
							<div class="col-sm-4 col-md-4 col-lg-4"></div>
						</div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
		    <?php if($hasViewOption){?>
                    <div class="card box-primary user-list">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("user_list");?></h3>
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
		$('#alert').hide(); $('.user-list').hide();
	});
	function DownloadPDF(){
	    var FileName= "UserList";
		var session_id       = $('#src-session-id').val();
		FileName+=session_id;
		var version_id       = $('#src-version-id').val();
		FileName+=version_id;
		var class_id         = $('#src-class-id').val();
		FileName+=class_id;
		var group_id		= $('#src-group-id').val();
		FileName+=group_id;
		var shift_id		= $('#src-shift-id').val();
		FileName+=shift_id;
		var section_id		= $('#src-section-id').val();
		FileName+=section_id;
		
		if(class_id >0){
		var url ="<?php echo base_url();?>assets/pdf/"+FileName+".pdf";
		window.open(url, '_blank');
		}
		//window.location.href = url;	
    }
    $('#reset').click(function(){
		$('#src-company-id').val("").trigger("chosen:updated");
		$('#src-branch-id').val("").trigger("chosen:updated");
		$('#src-session-id').val("").trigger("chosen:updated");		
		$('#src-version-id').val("").trigger("chosen:updated");
		$('#src-class-id').val("").trigger("chosen:updated");		
		$('#src-group-id').val("").trigger("chosen:updated");
		$('#src-shift-id').val("").trigger("chosen:updated");
		$('#src-section-id').val("").trigger("chosen:updated");	
		$('#src-user-type').val("").trigger("chosen:updated");	
		$('#dataGrid').html("");
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
		var section_id		= $('#src-section-id').val();
		if(isNullAndUndefined(section_id)){ section_id=0;}
		var user_type		= $('#src-user-type').val();
		if(isNullAndUndefined(user_type)){ user_type=0;}
		var status	= $('#src-status').val();
		if(isNullAndUndefined(status)){ status=0;}
		if(company_id >0 && branch_id >0 && session_id >0 && version_id >0 && class_id >0 && group_id>0 && shift_id>0 && section_id>0 && status >0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>users/GetUserList",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id="+version_id+"&class-id="+class_id+"&group-id="+group_id+"&shift-id="+shift_id+"&section-id="+section_id+"&user-type="+user_type+"&status="+status,
            		success: function(option){
            		    $('.user-list').show();
                		$('#dataGrid').html(option);
            		}//Success
        	});// ajax
		}else{
		  $('#alert').hide(); 
		  $('#alert').html('');
		  $('#alert-delete').show();
		  $('#alert-delete').html('Marksheet did not found! Please fill data in required fields');		
	    }
        return false;
    });// End search

    
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
            url: "<?php echo base_url();?>exam_marksheet/GetAjaxRunningExamList",
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
	
	function getAdmitedStudents(admission_id=0){
		var instituteId      = $('#src-company-id').val();
		if(isNullAndUndefined(instituteId)){ instituteId=0;}
		var branchId         = $('#src-branch-id').val();
		if(isNullAndUndefined(branchId)){ branchId=0;}
		var sessionId        = $('#src-session-id').val();
		if(isNullAndUndefined(sessionId)){ sessionId=0;}
		var versionId        = $('#src-version-id').val();
		if(isNullAndUndefined(versionId)){ versionId=0;}
		var classId          = $('#src-class-id').val();
		if(isNullAndUndefined(classId)){ classId=0;}
		var groupId		     = $('#src-group-id').val();
		if(isNullAndUndefined(groupId)){ groupId=0;}
		var shift_id		 = $('#src-shift-id').val();
		if(isNullAndUndefined(shift_id)){ shift_id=0;}
		var section_id		= $('#src-section-id').val();
		if(isNullAndUndefined(section_id)){ section_id=0;}
		
		if(instituteId >0 && branchId >0 && sessionId >0 && versionId >0 && classId >0 && groupId >0 && shift_id >0){
		  $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>billing/GetAdmitedStudents",
            data: "institute_id="+instituteId+"&branch_id="+branchId+"&session_id="+sessionId+"&version_id="+versionId+"&class_id="+classId+"&group_id="+groupId+"&shift_id="+shift_id+"&section_id="+section_id+"&admission_id="+admission_id,
            success: function(option){
             $('#src-admission-id').html(option);
			 $('#src-admission-id').trigger("chosen:updated");
			 //getFeesList();
            }//Success
          });// End datagrid
		}//end if
	}
    </script>
</body>
</html>
