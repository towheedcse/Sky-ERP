<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("holiday")." ".$this->lang->line("setup");?> </title>
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
               <?php echo $this->lang->line("holiday")." ".$this->lang->line("setup");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("holiday");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid --> 
							  
					<div id="alert" class="alert alert-success"></div>
					<form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>holiday/AddRecord">
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("holiday");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("holiday");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="institute_id"><?php echo $this->lang->line("company_name");?></label>
												<select name="institute_id" id="institute_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
													<?php foreach($iquery->result() as $row){
														echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
													}
													?>
												</select>												
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="branch_id"><?php echo $this->lang->line("branch");?></label>
												<select name="branch_id" id="branch_id" class="chosen-select" required="" onChange="GetAjaxFeePeriodList(this.value)">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch");?></option>
													<?php foreach($bquery->result() as $row){
														echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="session_id"><?php echo $this->lang->line("session");?></label>
												<select name="session_id" id="session_id" class="chosen-select" required="">
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
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="holiday_name"><?php echo $this->lang->line("holiday_name");?></label>
												
												<input type="text" placeholder="<?php echo $this->lang->line("holiday_name");?>" name="holiday_name" id="holiday_name" class="form-control">
												
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<label class="control-label" for="date-from"><?php echo $this->lang->line("from_date");?></label>						
													
											<div class="input-group">
												<input type="text" class="form-control datepicker_mask" name="from_date" id="date-from">
												<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<label class="control-label" for="date-to"><?php echo $this->lang->line("to_date");?></label>				
													
											<div class="input-group">
												<input type="text" class="form-control datepicker_mask" name="to_date" id="date-to">
												<div class="input-group-prepend">
												<span class="input-group-text"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
										</div>
										
									</div>
									<div class="row">
									<div class="col-sm-9 col-md-9 col-lg-9">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("holiday_image");?></label>
												<div class="input-group">
													<span class="input-group-btn">
													   <span class="btn btn-default btn-file">
															Browse… <input type="file" name="holiday_image" id="holiday_image" class="form-control" style="height: 25px;">
													   </span>
													  <div id ="optional_image3" style="padding-left:3px; width:4.1%; float:right">
														
													  </div>										  
													</span>
													<input type="text" class="form-control" readonly>
												</div>
												
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
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
							<input type="hidden" name="is_holiday" id="is_holiday" value="1">
							<input type="hidden" name="holiday_id" id="holiday_id" value="">
							<input type="hidden" name="version_id" id="version_id" value="0">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
          
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("holiday")." ".$this->lang->line("list");?></h3>
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
		$('#alert').hide();
        $("#imgInp").change(function(){
            readURL(this);
        });     
    });
	function isNullAndUndef(variable) {
    	if(variable == null || variable == undefined || variable==""){
		return true;
		}else if(isNaN(variable)){ return true; }	
    }
	
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#holiday_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#holiday_id').val();
    $('#holiday_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>holiday/DelRecord",
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
            url: "<?php echo base_url();?>holiday/GetRecord",
            data: "is_holiday=1",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>holiday/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[5]);
            $('#institute_id').val(rsStr[1]);
            $('#institute_id').trigger("chosen:updated");
            $('#branch_id').val(rsStr[2]);
            $('#branch_id').trigger("chosen:updated");
            $('#session_id').val(rsStr[3]).trigger("chosen:updated");
            $('#version_id').val(rsStr[4]);
            $('#holiday_name').val(rsStr[5]);
            $('#date-from').val(rsStr[6]);
            $('#date-to').val(rsStr[7]);
            $('#is_holiday').val(rsStr[8]);
            $('#status').val(rsStr[9]).trigger("chosen:updated");			
            $('#holiday_id').val(rsStr[0]);						
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
          $('#holiday_id').val("");
		  location.reload(); 
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>holiday/GetRecord",
            data: "is_holiday=1&from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
    </script>
</body>
</html>
