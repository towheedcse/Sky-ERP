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
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("bank")." ".$this->lang->line("salary_sheet");?></h1>
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
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("bank")." ".$this->lang->line("salary_sheet");?></h3>
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
							<div class="col-sm-4 col-md-4 col-lg-4">
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
							<div class="col-sm-4 col-md-4 col-lg-4">
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
							 <br>
							 <button type="button" style="margin-top: 7px;" class="btn btn-md btn-primary search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>
							</div>
							
							<div class="col-sm-2 col-md-2 col-lg-2">
							 <br>
							 <?php if($hasPrintOption){?>
                             
            				  <button type="button" style="margin-top: 7px;" onclick="PrintElem('#dataGrid')" style="margin-top:3px" class="btn btn-md btn-success print"><i class="fas fa-print"></i> <?php echo $this->lang->line("print");?></button>
            				  <!--button type="button" onclick="DownloadPDF()" style="margin-top:3px" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-download"> Download</span></button-->							
            				  
                            <?php }?>
							</div>
							
                           
		                </div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("bank")." ".$this->lang->line("salary_sheet")." ".$this->lang->line("list");?></h3>
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
		if(company_id >0 && branch_id >0 && session_id >0 && month_name>0 && salary_month>0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>salary_sheet/GetBankSalarySheetRecord",
			data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&month-name="+month_name+"&salary-year="+salary_month,
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
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>salary_sheet/GetBankSalarySheetRecord",
		    data: "company-id="+company_id+"&branch-id="+branch_id+"&session-id="+session_id+"&version-id=0&month-name="+month_name+"&salary-year="+salary_month+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
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
