<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("importer")." ".$this->lang->line("setup");?> </title>
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
               <?php echo $this->lang->line("importer");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("importer");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid --> 
							  
					<div id="alert" class="alert alert-success"></div>
					<form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>importer/AddRecord">
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("importer");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">                        
                            
							<div class="card card-info card-outline">
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("importer_details");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
								    <div class="row">
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group required">
												<label class="control-label" for="importer_name"><?php echo $this->lang->line("importer_name");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("importer_name");?>" name="importer_full_name" class="form-control" id="importer_full_name">
											</div>
										</div>
										
										<div class="col-sm-6 col-md-6 col-lg-6 img-container">
											<div class="form-group required">
												<label class="control-label" for="Short Code"><?php echo $this->lang->line("short_code");?>:</label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("short_code");?>" name="short_code" class="form-control" id="short_code">
											</div>
										</div>
										<input type="hidden" name="customer_photo" id="mainupload" class="form-control" style="height: 25px;">
									</div>
								    <div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<div class="form-group">
												<label class="control-label" for="present_address"><?php echo $this->lang->line("importer_address");?>:</label>
												<textarea type="text" rows="1" placeholder="<?php echo $this->lang->line("importer_address");?>" name="present_address" class="form-control" id="present_address"></textarea>
											</div>
										</div>
									</div>
								    <div class="row">
									    <div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group">
												<label class="control-label" for="billing_address"><?php echo $this->lang->line("billing_address");?>:</label>
												<textarea type="text" rows="1" placeholder="<?php echo $this->lang->line("billing_address");?>" name="billing_address" class="form-control" id="billing_address"></textarea>
											</div>
										</div>
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group">
												<label class="control-label" for="shipping_address"><?php echo $this->lang->line("shipping_address");?>:</label>
												<textarea type="text" rows="1" placeholder="<?php echo $this->lang->line("shipping_address");?>" name="shipping_address" class="form-control" id="shipping_address"></textarea>
											</div>
										</div>
									</div>
									<!--div class="row invisible">
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="login_id"><?php echo $this->lang->line("login_id");?></label>
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="password"><?php echo $this->lang->line("password");?></label>											
												
											</div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">
												<label class="control-label" for="confirm_password"><?php echo $this->lang->line("confirm_password");?></label>	
											</div>
										</div>
									</div-->
									
									<input type="hidden" placeholder="<?php echo $this->lang->line("login_id");?>" name="login_id" class="form-control" id="login_id" value="<?php echo rand(1000,100000);?>">
									<input type="hidden" placeholder="<?php echo $this->lang->line("password");?>" name="password" class="form-control" id="password" value="123456">											
									<input type="hidden" placeholder="<?php echo $this->lang->line("confirm_password");?>" name="confirm_password" class="form-control" id="confirm_password" value="123456">
									
								</div>
								
							</div> <!-- End Card -->
							
							<div class="card card-info card-outline">								
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("contact_person_info");?></h3>	<hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-group">
												<label class="control-label" for="contact_person"><?php echo $this->lang->line("contact_person");?>: </label>
												<input type="text" placeholder="<?php echo $this->lang->line("contact_person");?>" name="contact_person" class="form-control" id="contact_person">
											</div>
										</div>
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="designation"><?php echo $this->lang->line("designation");?>: </label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("designation");?>" name="designation" class="form-control" id="designation">
											</div>
										</div>
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group required">
												<label class="control-label" for="currency"><?php echo $this->lang->line("currency");?>:</label>
												<select name="currency" id="currency" class="chosen-select" required="">
													<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("currency");?></option>        
												<?php foreach($ctquery->result() as $row){
													echo '<option value="'.$row->currency_id.'">'.$row->currency_name.'</option>';
												}
												?>
												</select>
											</div>
										</div>
										
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label class="control-label" ><?php echo $this->lang->line("mobile");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("mobile");?>" name="mobile" class="form-control" id="mobile">
											</div>
											<input type="hidden" name="division" class="form-control" id="division" value="0">
										    <input type="hidden" name="district" class="form-control" id="district" value="0">
										    <input type="hidden" name="thana" class="form-control" id="thana" value="0">
										    <input type="hidden" name="permanent_address" class="form-control" id="permanent_address" value="">
										    <input type="hidden" class="form-control" name="gender" id="gender" value="">
        									<input type="hidden" class="form-control" name="nationality" id="nationality" value="">
        									<input type="hidden" class="form-control" name="salesman_id" id="salesman_id" value="">
										</div>
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label class="control-label" ><?php echo $this->lang->line("phone");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("phone");?>" name="phone" class="form-control" id="phone">
											</div>
										</div>
										
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label class="control-label" ><?php echo $this->lang->line("fax");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("fax");?>" name="fax" class="form-control" id="fax">
											</div>
										</div>
										
										<div class="col-sm-3 col-md-3 col-lg-3">
											<div class="form-group">
												<label for="email"><?php echo $this->lang->line("email");?>:</label>
										<input type="text" placeholder="<?php echo $this->lang->line("email");?>" name="email" class="form-control" id="email">
											</div>
										</div>
									</div>
								</div>
								
							</div> <!-- End Card -->
						    
							<div class="row">
							   
								<div class="col-sm-6 col-md-6 col-lg-6">
									<div class="form-group">
										<br>
										<button type="submit" class="btn btn-success save-record"><i class="fa fa-save"></i> Save</button>
										<button type="button" class="btn btn-danger cancel-record"><i class="fa fa-close"></i> Cancel</button>
									</div>
								</div> 
							</div>
							<input type="hidden" name="institute_id" id="institute_id" value="1">
							<input type="hidden" name="branch_id" id="branch_id" value="1">
							<input type="hidden" name="group_id" id="group_id" value="1">
							<input type="hidden" name="importer_type" id="importer_type" value="1"> <!-- Regular -->
							<input type="hidden" name="account_type" id="account_type" value="3">
							<input type="hidden" name="importer_code" id="importer_code" value="">
							<input type="hidden" name="importer_id" id="importer_id" value="">
						</div> <!-- End Card Body-->
					</div> <!-- End Card -->
                    </form>                
          
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("importer")." ".$this->lang->line("list");?></h3>
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
        $('#importer_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#importer_id').val();
    $('#importer_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>importer/DelRecord",
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
            url: "<?php echo base_url();?>importer/GetRecord",
            success: function(option){			
			$('#login_id').prop('readonly', false);
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>importer/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
           
            $('#importer_id').val(rsStr[0]);
            $('#institute_id').val(rsStr[1]);
            $('#branch_id').val(rsStr[2]);
            $('#importer_type').val(rsStr[3]);
            $('#importer_full_name').val(rsStr[4]);
            $('#billing_address').val(rsStr[5]);
            $('#shipping_address').val(rsStr[6]);
            $('#contact_person').val(rsStr[7]);
            $('#designation').val(rsStr[8]); 
            $('#gender').val(rsStr[9]); 
            $('#nationality').val(rsStr[10]);
            $('#salesman_id').val(rsStr[11]);
            $('#division').val(rsStr[12]);
            $('#district').val(rsStr[13]);
            $('#thana').val(rsStr[14]);
            $('#present_address').val(rsStr[15]);
            $('#permanent_address').val(rsStr[16]);
            $('#mobile').val(rsStr[17]);
            $('#phone').val(rsStr[18]);
            $('#fax').val(rsStr[19]);
            $('#email').val(rsStr[20]);
            $('#currency').val(rsStr[21]);
            
            $('#login_id').val(rsStr[22]);
            $('#password').val(rsStr[23]); $('#confirm_password').val(rsStr[23]); 
			$('#login_id').prop('readonly', true);
			setTimeout(function() {
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
			}, 50);
			
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
        $('#institute_id').val("1");
        $('#branch_id').val("1");
        $('#importer_type').val("1");
        $('#currency').val("1");
        
		//$('#institute_id').val(1).trigger("chosen:updated");
	    $('#nationality').val("Bangladeshi").trigger("chosen:updated");
	    
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
          $('#importer_id').val("");
		  location.reload(); 
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>importer/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
    
    function getDistrictList(division_id,placement,district_id=0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>importer/GetAjaxDistrictList",
            data: "division-id="+division_id+"&district-id="+district_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          return false;
    } 
    
    function getAreaList(district_id,placement,area_id=0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>importer/GetAjaxAreaList",
            data: "district-id="+district_id+"&area-id="+area_id,
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
