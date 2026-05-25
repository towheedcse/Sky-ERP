<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("product")." ".$this->lang->line("setup");?> </title>
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
               <?php echo $this->lang->line("product");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("product");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid --> 
							  
					<div id="alert" class="alert alert-success"></div>
					<form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>product/AddRecord">
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("product");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("product_information");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="institute_id"><?php echo $this->lang->line("company_name");?></label>
												<select name="institute_id" id="institute_id" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company");?></option>
													<?php foreach($cquery->result() as $row){
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
										<div class="col-sm-4 col-md-4 col-lg-4 img-container" rowspan="4">
											
											<div class="form-group required">
												<label class="control-label" for="account_type"><?php echo $this->lang->line("product_type");?>:</label>
												<select name="account_type" id="account_type" class="chosen-select" required="">    
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("product_type");?></option>
													<option value="12">Inventory Item</option>
													<option value="13">Sales Item</option>
												</select>
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4 img-container" rowspan="4">
											<div class="form-group required">
												<label class="control-label"><?php echo $this->lang->line("product_name");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("product_name");?>" name="product_name" id="product_name" class="form-control" required="">
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4 img-container" rowspan="4">
											<div class="form-group">
												<label><?php echo $this->lang->line("product_code");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("product_code");?>" name="product_code" id="product_code" class="form-control">
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4 img-container" rowspan="4">
											<div class="form-group">
												<label><?php echo $this->lang->line("product_details");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("product_details");?>" name="product_details" id="product_details" class="form-control">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="count_unit"><?php echo $this->lang->line("count_unit");?></label>												
												<select name="count_unit" id="count_unit" class="chosen-select" required="">    
													<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("count_unit");?></option>													
													  <option value="4">Pcs</option>
													  <option value="5">Dzn</option>
													  <option value="6">Fit</option>
													  <option value="7">Pack</option>
												</select>												
											</div>
										</div>
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" for="purchase_price"><?php echo $this->lang->line("purchase_price");?></label>												
												<input type="text" required="" placeholder="<?php echo $this->lang->line("purchase_price");?>" name="purchase_price" id="purchase_price" class="form-control">																								
											</div>
										</div>											
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" for="sales_price"><?php echo $this->lang->line("sales_price");?></label>												
												<input type="text" required="" placeholder="<?php echo $this->lang->line("sales_price");?>" name="sales_price" id="sales_price" class="form-control">																								
											</div>
										</div>											
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" for="reorder_level"><?php echo $this->lang->line("reorder_level");?></label>												
												<input type="text" required="" placeholder="<?php echo $this->lang->line("reorder_level");?>" name="reorder_level" id="reorder_level" class="form-control">																								
											</div>
										</div>
										
										<div class="col-sm-2 col-md-2 col-lg-2">
											<div class="form-group required">
												<label class="control-label" for="status"><?php echo $this->lang->line("status");?></label>												
												<select name="status" id="status" class="chosen-select" required="">
													<option value="0"><?php echo $this->lang->line("inactive");?></option>
													<option value="1"><?php echo $this->lang->line("active");?></option>
												</select>																								
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<button type="submit" class="btn btn-success save-record"><i class="fa fa-save"></i> Save</button>
												<button type="button" class="btn btn-danger cancel-record"><i class="fa fa-close"></i> Cancel</button>
											</div>
										</div>
									</div>
								</div>
								
							</div> <!-- End Card -->
							
							<input type="hidden" name="group_id" id="group_id" value="1">
							<input type="hidden" name="product_id" id="product_id" value="">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
          
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("product")." ".$this->lang->line("list");?></h3>
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
    /*
    $('#InputForm').submit(function(evt) {
                evt.preventDefault();

		var data = new FormData();
		
		//Form data
		var form_data = $('#InputForm').serializeArray();
		$.each(form_data, function (key, input) {
		    data.append(input.name, input.value);
		});

		//File data
		var file_data = $('input[name="student_photo"]')[0].files;
		for (var i = 0; i < file_data.length; i++) {
		    data.append("student_photo[]", file_data[i]);
		}

		//Custom data
		data.append('key', 'value');

                $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>admission/saveData",
                data:data,
                cache:false,
                contentType: false,
                processData: false,
                success: function(data) {                    
			$('#admission_id').val("");
			$('#dataGrid').html(option);
			$('.show-create').hide();
			$('#alert').show();
			$('#alert').html('Successfully saved record!');
                },
                error: function(data) {
		    $('#alert-delete').show();
                    $('#alert-delete').html('Failed to save record. Please try again!!!');
                }
                });
    });
    */
	
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#product_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#product_id').val();
    $('#product_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>product/DelRecord",
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
            url: "<?php echo base_url();?>product/GetRecord",
            success: function(option){			
			$('#product_code').prop('readonly', true);
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>product/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#institute_id').val(rsStr[1]);
            $('#institute_id').trigger("chosen:updated");
            $('#branch_id').val(rsStr[2]);
            $('#branch_id').trigger("chosen:updated");
            $('#account_type').val(rsStr[3]).trigger("chosen:updated");
            $('#product_name').val(rsStr[4]);
            $('#product_code').val(rsStr[5]);
            $('#product_details').val(rsStr[6]);
            $('#count_unit').val(rsStr[7]).trigger("chosen:updated");
            $('#purchase_price').val(rsStr[8]);
            $('#sales_price').val(rsStr[9]);
            $('#reorder_level').val(rsStr[10]);
            $('#status').val(rsStr[11]);
            $('#status').trigger("chosen:updated");			
            $('#product_id').val(rsStr[0]);
			$('#product_code').prop('readonly', true);							
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
          $('#product_id').val("");
		  location.reload(); 
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>product/GetRecord",
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
