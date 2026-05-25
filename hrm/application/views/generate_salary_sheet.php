<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line('salary_sheet');?></title>
    <?php require('csslinks4admin.php');?>
	<style>
	#dataGrid > table td, #dataGrid > .table th {
		padding: .25rem ;
	}
	.grid-control {
		padding: .375rem .16rem !important;
	}
	</style>
</head>
<body class="hold-transition sidebar-mini">
	<!--Conform Modal-->
    <div class="modal fade" id="confirmModal" role="dialog" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                	<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("finalize_modal_header");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h5><?php echo $this->lang->line("finalize_message");?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    <button type="button" id="btnConfirm" class="btn btn-primary confGenerate"><i class="fa fa-check"></i> <?php echo $this->lang->line("generate");?></button>
                </div>
            </div>
        </div>
    </div>
	<!--Approved Modal-->
    <div class="modal fade" id="ApprovedModal" role="dialog" tabindex="-1" aria-labelledby="ApprovedLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                	<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("approved_modal_header");?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <h5><?php echo $this->lang->line("approved_message");?></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
                    <button type="button" id="btnApproved" class="btn btn-primary ConfApproved"><i class="fa fa-check"></i> <?php echo $this->lang->line("approved");?></button>
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
                <div id="alert" class="alert alert-success"></div>
                <div id="alert-delete" class="alert alert-danger"></div>
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("monthly")." ".$this->lang->line("salary_sheet");?></h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("salary_sheet");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("salary_sheet");?></h3>
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
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-branch-id"><?php echo $this->lang->line("branch_name");?>:</label>
		                            <div id="com_id">
		                                <select name="src-branch-id" id="src-branch-id" class="chosen-select" required="">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
		                                    
		                                </select>
		                            </div>
		                        </div>
		                    </div>

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
		                </div>
		                <div class="row">							
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group required">
                                    <label class="control-label" for="src-month-name"><?php echo $this->lang->line("salary_month");?></label>
									<select name="src-month-name" id="src-month-name" class="chosen-select" required="">
										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("salary_month");?></option>
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
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group required">
                                    <label class="control-label" for="src-salary-year"><?php echo $this->lang->line("salary_year");?></label>
									<select name="src-salary-year" id="src-salary-year" class="chosen-select" required="">
										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("salary_year");?></option>
										<?php 
										$years = date("Y") -5; $cyear=date("Y");
										while($years <=$cyear){
										$years++;	
										?>
										<option value="<?php echo $years;?>"><?php echo $years;?></option>
										<?php }?>                                       
									</select>
                                </div>
                            </div>
							<div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group required">
                                    <label class="control-label" for="src-with-bonus"><?php echo $this->lang->line("festival_bonus");?></label>
									<select name="src-with-bonus" id="src-with-bonus" class="chosen-select" required="">
										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("festival_bonus");?></option>
										<option value="1"><?php echo $this->lang->line("yes");?></option>
										<option value="0"><?php echo $this->lang->line("no");?></option>
									</select>
                                </div>
                            </div>
							<div class="col-sm-6 col-md-6 col-lg-6">
							 <br>
							 <button type="button" style="margin-top: 7px;" class="btn btn-md btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>
							 <?php if($hasPrintOption){?>
							 <button type="button" style="margin-top: 7px; margin-left: 10px;" class="btn btn-md btn-default" onclick="PrintElem('#dataGrid')"><i class="fas fa-print"></i> <?php echo $this->lang->line("print");?></button>
							 <?php }?>
							 <?php if($hasGenerateOption){?>
							 <button type="button" style="margin-top: 7px; margin-left: 10px;" data-toggle='modal' data-target='#confirmModal' class="btn btn-md btn-primary generate"><i class="fas fa-check"></i> <?php echo $this->lang->line("generate");?></button>
							 <?php }?>
							 <?php if($hasApprovedOption){?>
							 <button type="button" style="margin-top: 7px; margin-left: 10px;" data-toggle='modal' data-target='#ApprovedModal' class="btn btn-md btn-primary approved"><i class="fas fa-check"></i> <?php echo $this->lang->line("approved");?></button>
							 <?php }?>
							</div>
		                </div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("monthly")." ".$this->lang->line("salary_sheet")." ".$this->lang->line("list");?></h3>
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
		$('.generate').prop( "disabled",true);
		$('.approved').prop( "disabled",true);
	});
	
    $('#reset').click(function(){
		$('#src-company-id').val("").trigger("chosen:updated");
		$('#src-branch-id').val("").trigger("chosen:updated");
		$('#src-session-id').val("").trigger("chosen:updated");
		$('#src-month-name').val("").trigger("chosen:updated");
		$('#src-salary-year').val("").trigger("chosen:updated");
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
		var month_name		= $('#src-month-name').val();
		if(isNullAndUndefined(month_name)){ month_name=0;}
		var salary_month	= $('#src-salary-year').val();
		if(isNullAndUndefined(salary_month)){ salary_month=0;}
		var with_bonus	= $('#src-with-bonus').val();
		if(isNullAndUndefined(with_bonus)){ with_bonus=0;}
		if(company_id >0 && branch_id >0 && session_id >0 && month_name>0 && salary_month>0 && with_bonus>=0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>salary_sheet/GetSalarySheetRecord",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&month-name="+month_name+"&salary-year="+salary_month+"&with-bonus="+with_bonus,
            		success: function(option){
                		$('#dataGrid').html(option); 
						<?php if($hasGenerateOption){?>
						if($('#status').val()==0){
							$('.generate').prop( "disabled",false);
						}else{
							$('.generate').prop( "disabled",true);
						}
						<?php }?>
						<?php if($hasApprovedOption){?>
						if($('#status').val()==1){
							$('.approved').prop( "disabled",false);
							$('.generate').prop( "disabled",true);
						}else if($('#status').val() >1){
							$('.approved').prop( "disabled",true);
							$('.generate').prop( "disabled",true);
						}
						<?php }?>
						
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
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var month_name		= $('#src-month-name').val();
		if(isNullAndUndefined(month_name)){ month_name=0;}
		var salary_month	= $('#src-salary-year').val();
		if(isNullAndUndefined(salary_month)){ salary_month=0;}
		var with_bonus	= $('#src-with-bonus').val();
		if(isNullAndUndefined(with_bonus)){ with_bonus=0;}
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>salary_sheet/GetSalarySheetRecord",
		    data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&month-name="+month_name+"&salary-year="+salary_month+"&with-bonus="+with_bonus+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
		    success: function (option) {
		    $('#dataGrid').html(option);
			<?php if($hasGenerateOption){?>
			if($('#status').val()==0){
				$('.generate').prop( "disabled",false);
			}else{
				$('.generate').prop( "disabled",true);
			}
			<?php }?>
			<?php if($hasApprovedOption){?>
			if($('#status').val()==1){
				$('.approved').prop( "disabled",false);
				$('.generate').prop( "disabled",true);
			}else if($('#status').val() >1){
				$('.approved').prop( "disabled",true);
				$('.generate').prop( "disabled",true);
			}
			<?php }?>
			
		    }//Success
		});// End datagrid
		return false;
    }
	function setSalary(institute_id,branch_id,session_id,employee_id,salary_month,salary_year,field_id,field_value,salary_id){
		
		if(isNullAndUndefined(salary_id)){ salary_id=0;}
		if(field_id==25){
		  	
    		if((institute_id>0 && branch_id>0 && session_id>0 && employee_id>0 && salary_month>0 && salary_year>0) && field_id>0 && field_value !="" && salary_id>0){
    		    $.ajax({
    		        type: 'POST',
    		        url: "<?php echo base_url();?>salary_sheet/SaveRecord",
    		        data: "company-id="+institute_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&employee-id="+employee_id+"&month-name="+salary_month+"&salary-year="+salary_year+"&field-id="+field_id+"&field-value="+field_value+"&salary-id="+salary_id,
    		        success: function(option){
    		            //alert(option);
    		            $('#dataGrid').html(option);
    					$('#alert-delete').hide();
    		            $('#alert').show();
    		            $('#alert').html('Successfully saved!');
    		        }//Success
    		    });// ajax
    		}  
		
		}else{
		    if(isNullAndUndefined(field_value)){ field_value=0;}		
			
    		if((institute_id>0 && branch_id>0 && session_id>0 && employee_id>0 && salary_month>0 && salary_year>0) && field_id>0 && field_value>=0 && salary_id>0){
    		    $.ajax({
    		        type: 'POST',
    		        url: "<?php echo base_url();?>salary_sheet/SaveRecord",
    		        data: "company-id="+institute_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&employee-id="+employee_id+"&month-name="+salary_month+"&salary-year="+salary_year+"&field-id="+field_id+"&field-value="+field_value+"&salary-id="+salary_id,
    		        success: function(option){
    		            //alert(option);
    		            $('#dataGrid').html(option);
    					$('#alert-delete').hide();
    		            $('#alert').show();
    		            $('#alert').html('Successfully saved!');
    		        }//Success
    		    });// ajax
    		}
		}
		return false;
	}
	
 	/* Start Confirm Data*/		
	$('.confGenerate').click(function(){
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var month_name		= $('#src-month-name').val();
		if(isNullAndUndefined(month_name)){ month_name=0;}
		var salary_month	= $('#src-salary-year').val();
		if(isNullAndUndefined(salary_month)){ salary_month=0;}
		var with_bonus	= $('#src-with-bonus').val();
		if(isNullAndUndefined(with_bonus)){ with_bonus=0;}
		
		if(company_id >0 && branch_id >0 && session_id >0 && month_name>0 && salary_month>0 && with_bonus>=0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>salary_sheet/GenerateSalarySheet",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&month-name="+month_name+"&salary-year="+salary_month+"&status=1&with-bonus="+with_bonus,
            		success: function(option){
                		$('#dataGrid').html(option);
						<?php if($hasGenerateOption){?>
						if($('#status').val()==0){
							$('.generate').prop( "disabled",false);
						}else{
							$('.generate').prop( "disabled",true);
						}
						<?php }?>
						<?php if($hasApprovedOption){?>
						if($('#status').val()==1){
							$('.approved').prop( "disabled",false);
							$('.generate').prop( "disabled",true);
						}else if($('#status').val() >1){
							$('.approved').prop( "disabled",true);
							$('.generate').prop( "disabled",true);
						}
						<?php }?>						
            		}//Success
        	});// ajax
		}else{
		  $('#alert').hide();
		  $('#alert').html('');
		  $('#alert-delete').show();
		  $('#alert-delete').html('Record did not found! Please fill data in required fields');		
	    }
        return false;
	});// End Confirm
	$('#btnConfirm').click(function() {
		 $('#confirmModal').modal('hide');
	});
	/* End Confirm Data*/
		
	/* Start Approved Data*/		
	$('.ConfApproved').click(function(){
		var company_id       = $('#src-company-id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var branch_id       = $('#src-branch-id').val();
		if(isNullAndUndefined(branch_id)){ branch_id=0;}
		var session_id       = $('#src-session-id').val();
		if(isNullAndUndefined(session_id)){ session_id=0;}
		var month_name		= $('#src-month-name').val();
		if(isNullAndUndefined(month_name)){ month_name=0;}
		var salary_month	= $('#src-salary-year').val();
		if(isNullAndUndefined(salary_month)){ salary_month=0;}
		var with_bonus	= $('#src-with-bonus').val();
		if(isNullAndUndefined(with_bonus)){ with_bonus=0;}
		
		if(company_id >0 && branch_id >0 && session_id >0 && month_name>0 && salary_month>0 && with_bonus>=0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>salary_sheet/ApprovedSalarySheet",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&month-name="+month_name+"&salary-year="+salary_month+"&status=2&with-bonus="+with_bonus,
            		success: function(option){
                		$('#dataGrid').html(option);
						<?php if($hasGenerateOption){?>
						if($('#status').val()==0){
							$('.generate').prop( "disabled",false);
						}else{
							$('.generate').prop( "disabled",true);
						}
						<?php }?>
						<?php if($hasApprovedOption){?>
						if($('#status').val()==1){
							$('.approved').prop( "disabled",false);
							$('.generate').prop( "disabled",true);
						}else if($('#status').val() >1){
							$('.approved').prop( "disabled",true);
							$('.generate').prop( "disabled",true);
						}
						<?php }?>						
            		}//Success
        	});// ajax
		}else{
		  $('#alert').hide();
		  $('#alert').html('');
		  $('#alert-delete').show();
		  $('#alert-delete').html('Record did not found! Please fill data in required fields');		
	    }
        return false;
	});// End Approved
	
	$('#btnApproved').click(function() {
		 $('#ApprovedModal').modal('hide');
	});
	/* End Approved Data*/
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
