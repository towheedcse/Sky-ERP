<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("billing");?> </title>
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
               <?php echo $this->lang->line("billing")." (".$this->lang->line("salary").")";?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("salary");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                               
                <?php if($hasViewOption){?>
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-search"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("billing")." (".$this->lang->line("salary").")";?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
						<div class="row">
									<div class="col-sm-4 col-md-4 col-lg-4">
										<div class="form-group required">
											<label class="control-label" for="src-institute_id"><?php echo $this->lang->line("company_name");?></label>
											<div id="s_id">
												<select name="src-institute_id" id="src-institute_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
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
											<label class="control-label" for="src-branch_id"><?php echo $this->lang->line("branch");?></label>
											<div id="s_id">
												<select name="src-branch_id" id="src-branch_id" class="chosen-select" required="" onChange="GetSrcFeePeriodList(this.value)">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch");?></option>
													<?php foreach($bquery->result() as $row){
														echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>
									
									<div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-fee_period"><?php echo $this->lang->line("fee_period");?></label>
											<select name="src-fee_period" id="src-fee_period" class="chosen-select" required="">
												<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("fee_period");?></option>
												<?php foreach($fpquery->result() as $row){
													if($this->session->userdata('language')=="en"){
														$period_name = $row->period_name_en;
													}else{
														$period_name = $row->period_name_bn;
													}
													echo '<option value="'.$row->period_no.'">'.$period_name.'</option>';
												}
												?>
											</select>
										</div>
									</div>
									
									<div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-session_id"><?php echo $this->lang->line("session");?></label>
											<select name="src-session_id" id="src-session_id" class="chosen-select" required="">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("session");?></option>
												<?php foreach($squery->result() as $row){
													echo '<option value="'.$row->sessions_id.'">'.$row->session_name.'</option>';
												}
												?>
											</select>
										</div>
									</div>
								 
								</div>
															
								<div class="row">							 

									<div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-version_id"><?php echo $this->lang->line("version");?></label>
											<select name="src-version_id" id="src-version_id" class="chosen-select" required="">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("version");?></option>
												<?php foreach($vquery->result() as $row){
													echo '<option value="'.$row->version_id.'">'.$row->version_name.'</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-2 col-md-2 col-lg-2">
										<div class="form-group required">
											<label class="control-label" for="src-shift_id"><?php echo $this->lang->line("shift");?></label>
											<select name="src-shift_id" id="src-shift_id" class="chosen-select" required="" onChange="getSrcSectionList()">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift");?></option>
												<?php foreach($shquery->result() as $row){
													echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-2 col-md-2 col-lg-2">                                    
										<div class="form-group required">	
											<label class="control-label" for="src-date-from"><?php echo $this->lang->line("from_date");?></label>						
											
											<div class="input-group">
											<input type="text" class="form-control datepicker_nomask" name="src-date-from" id="src-date-from">
											<div class="input-group-prepend">
											  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
										</div>									
									</div>
									<div class="col-sm-2 col-md-2 col-lg-2">                                    
										<div class="form-group required">	
											<label class="control-label" for="src-date-to"><?php echo $this->lang->line("to_date");?></label>						
											
											<div class="input-group">
											<input type="text" class="form-control datepicker_nomask" name="src-date-to" id="src-date-to">
											<div class="input-group-prepend">
											  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
											</div>
										</div>
										</div>									
									</div>
									<div class="col-sm-4 col-md-4 col-lg-4">
										<div class="form-group required">
											<label class="control-label" for="src-employee-id"><?php echo $this->lang->line("employee_name");?></label>
											<select name="src-admission_id" id="src-employee-id" class="chosen-select" required="">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("employee");?></option>
													<?php foreach($etquery->result() as $row){
														echo '<option value="'.$row->employee_id.'">'.$row->employee_name.'</option>';
													}
												?>
											</select>
										</div>
									</div>
							</div>
							<div class="row">								
								<div class="form-group text-right">
								<button type="button" class="btn btn-md btn-info search" style="margin-top:5px"><span class="glyphicon glyphicon-search"> Search</span></button>
								<?php if($hasPrintOption){?>
								<button type="button" onclick="PrintElem('#dataGrid')" class="btn btn-md btn-success print" style="margin-top:5px"><span class="glyphicon glyphicon-print"> Print</span></button>
								<?php }?>
								</div>						
							</div>
					    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
					
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("billing")." (".$this->lang->line("salary").")"." ".$this->lang->line("list");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">							
						    <div id="alert-delete" class="alert alert-danger"></div>
                            <div id="dataGrid"></div>
							<input type="hidden" name="bill-id" id="bill-id" value="0">
							<input type="hidden" name="employee-id" id="employee-id" value="0">
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
	//jQuery('#datetimepicker').datetimepicker();	
	
    $(document).ready( function() {
        reloadDataGrid(); 
        $('.FeesList').hide(); 
		resizeChosen();
		jQuery(window).on('resize', resizeChosen);
		resizeGridChosen1();
		jQuery(window).on('resize', resizeGridChosen1);
		resizeGridChosen2();
		jQuery(window).on('resize', resizeGridChosen2);	
		resizeGridChosen3();
		jQuery(window).on('resize', resizeGridChosen3);		
		resizeGridChosen4();
		jQuery(window).on('resize', resizeGridChosen4);		
		resizeGridChosen5();
		jQuery(window).on('resize', resizeGridChosen5);		
		resizeGridChosen6();
		jQuery(window).on('resize', resizeGridChosen6);			
		resizeGridChosen7();
		jQuery(window).on('resize', resizeGridChosen7);	
    });
	function resizeChosen() {
        $(".chosen-container").each(function() {
            $(this).attr('style', 'width: 100%');
        });
    }
    function resizeGridChosen1() {
        $("#detail-item1 .chosen-container").each(function() {
            $(this).attr('style', 'width: 200px');
        });
    }
    function resizeGridChosen2() {
        $("#detail-item2 .chosen-container").each(function() {
            $(this).attr('style', 'width: 170px');
        });
    }
    function resizeGridChosen3() {
        $("#detail-item3 .chosen-container").each(function() {
            $(this).attr('style', 'width: 140px');
        });
    }
    function resizeGridChosen4() {
        $("#detail-item4 input").each(function() {
            $(this).attr('style', 'width: 160px');
        });
    } 
    function resizeGridChosen5() {
        $("#detail-item5 input").each(function() {
            $(this).attr('style', 'width: 130px');
        });
    }  
    function resizeGridChosen6() {
        $("#detail-item6 input").each(function() {
            $(this).attr('style', 'width: 70px');
        });
    }   
    function resizeGridChosen7() {
        $("#detail-item7 .chosen-container").each(function() {
            $(this).attr('style', 'width: 240px');
        });
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

    $('#reset').click(function(){
          $('#alert').hide();
          $('#employee-id').val("");
    });// End reset
	function reloadDataGrid(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>salary_bill/GetSalaryBillRecords",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
	$('.search').click(function(){
    	var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var periodId        = $('#src-fee_period').val();
        var sessionId       = $('#src-session_id').val();
        var versionId       = $('#src-version_id').val();
        var shiftId   		= $('#src-shift_id').val();
        var employeeId   	= $('#src-employee-id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>salary_bill/GetSalaryBillRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-period="+periodId+"&src-session="+sessionId+"&src-version="+versionId+"&src-shift="+shiftId+"&src-employee="+employeeId,
            success: function(option){
                $('#dataGrid').html(option);
            }//Success
        });// ajax
        return false;
    });// End search
	
    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        var instituteId     = $('#src-institute_id').val();
        var branchId        = $('#src-branch_id').val();
        var periodId        = $('#src-fee_period').val();
        var sessionId       = $('#src-session_id').val();
        var versionId       = $('#src-version_id').val();
        var shiftId   		= $('#src-shift_id').val();
        var employeeId   	= $('#src-employee-id').val();
		var srcFrom	 		= $('#src-date-from').val();
		var srcTo	 		= $('#src-date-to').val();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>salary_bill/GetSalaryBillRecords",
            data: "src-institute="+instituteId+"&src-branch="+branchId+"&src-period="+periodId+"&src-session="+sessionId+"&src-version="+versionId+"&src-shift="+shiftId+"&src-employee="+employeeId+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }	
	/* Start Delete Data*/
    function deleteRecord(id){
        $('#bill-id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
		var delId = $('#bill-id').val();
		$('#bill-id').val("0");
		if(delId >0){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>salary_bill/DelBillRecord",
				data: "id="+delId,
				success: function(option){
					$('#alert-delete').show(1000);
					$('#alert-delete').html('Successfully deleted all monthly salary bill');
					reloadDataGrid();
				}//Success
			});// ajax
			return false;
		}
    });// End reset
    /* End Delete Data*/
	function GetSrcFeePeriodList(period_id=0){
		var institute_id = parseInt($('#src-institute_id').val());
		var branch_id 	 = parseInt($('#src-branch_id').val());
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>admission/GetAjaxPeriodList",
            data: "institute-id="+institute_id+"&branch-id="+branch_id+"&period-num="+period_id,
            success: function(option){                
				//$('#src-fee_period').html(option);
				//$('#src-fee_period').trigger("chosen:updated");
            }//Success

        });// ajax
        return false;
    }
    </script>
</body>
</html>
