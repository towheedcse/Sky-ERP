<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
        <?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("role")." ".$this->lang->line("setup");?>
    </title>
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
		    <?php echo $this->lang->line("login_account");?>
		       <?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       <?php }?>
		    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("login_account");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->		    
                        <div id="alert" class="alert alert-success"></div>
			<div id="alert-delete" class="alert alert-danger"></div>
		    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("login_account");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                            <div class="card-body">
                                <form>
		                <div class="row">
		                    <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="company-id"><?php echo $this->lang->line("company_name");?>:</label>
		                            <div id="com_id">
		                                <select name="company-id" id="company-id" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("company_name");?>">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company_name");?></option>
		                                    <?php foreach($cquery->result() as $row){
		                                        echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
		                                    }
		                                    ?>
		                                </select>
		                            </div>
		                        </div>
		                    </div>
                            	    <div class="col-sm-6 col-md-6 col-lg-6">
	                              <div class="form-group required">
	                              <label class="control-label" for="branch"><?php echo $this->lang->line("branch_name");?>:</label>
	                                <div id="branch">
	                                <select name="branch-id" id="branch-id" class="chosen-select" required="">
	                                    <option value=""><?php echo $this->lang->line("select");?> <?php echo $this->lang->line("branch");?></option>
	                                    <?php foreach($bquery->result() as $row){
	                                        echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
	                                    }
	                                    ?>
	                                </select>
	                                </div>
	                              </div>
                            	    </div>
                        	</div>
		                <div class="row">		                    
		                    <div class="col-sm-6 col-md-6 col-lg-6">
	                              	<div class="form-group required">
	                              	<label class="control-label" for="employee-id"><?php echo $this->lang->line("user")." ".$this->lang->line("name");?>:</label>
			                    <div id="emp_id">
			                        <select name="employee-id" id="employee-id" class="chosen-select" required="">
			                            <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("user");?></option>
			                            <?php foreach($empquery->result() as $row){
			                                echo '<option value="'.$row->account_id.'">'.$row->account_name.'</option>';
			                            }
			                            ?>
			                        </select>
			                    </div>
	                          	</div>
		                    </div>
				   <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="user-name"><?php echo $this->lang->line("login")." ".$this->lang->line("id");?>:</label>
		                            <input type="text" required="" placeholder="<?php echo $this->lang->line("email")." ".$this->lang->line("or")." ".$this->lang->line("mobile");?>" name="user-name" class="form-control" id="user-name" onKeyUp="chkUserName(this.value)">
		                        </div>
				    </div>
                        	</div>
                        	<div class="row">		                    
			            <div class="col-sm-6 col-md-6 col-lg-6">
			                <div class="form-group required">
			                    <label class="control-label" for="password"><?php echo $this->lang->line("password");?>:</label>
			                    <input type="password" required="" placeholder="<?php echo $this->lang->line("password");?>" name="password" class="form-control" id="password">
			                </div>
			            </div>
			            <div class="col-sm-6 col-md-6 col-lg-6">
			                <div class="form-group required">
			                    <label class="control-label" for="password"><?php echo $this->lang->line("confirm")." ".$this->lang->line("password");?>:</label>
			                    <input type="password" required="" placeholder="<?php echo $this->lang->line("confirm")." ".$this->lang->line("password");?>" name="confirm-password" class="form-control" id="confirm-password" onKeyUp="chkConfPass()">
			                </div>
			            </div>
		                </div>

			
                        <div class="row">                            		                    
	                    <div class="col-sm-6 col-md-6 col-lg-6">
                              	<div class="form-group required">
                              	<label class="control-label" for="user-role"><?php echo $this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("role");?>:</label>
		                    <div id="user-role">
                                        <select name="role-id" id="role-id" class="chosen-select" required="">
                                            <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("role");?></option>
                                            <?php foreach($rquery->result() as $row){
                                                echo '<option value="'.$row->role_id.'">'.$row->role_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                          	</div>
	                    </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group required">
                                    <label class="control-label" for="user-status"><?php echo $this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("status");?>:</label>
                                    <div id="menu_status_id">
                                        <select name="user-status" id="user-status" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("status");?>">
                                            <option value="0"><?php echo $this->lang->line("inactive");?></option>
                                            <option value="1"><?php echo $this->lang->line("active");?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="user-id" class="form-control" id="user-id" value="">
			    <input type="hidden" name="isValid" class="form-control" id="isValid">
                        </div>

		        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <button type="button" id="btnSave" style="margin-top: 7px;" class="btn btn-block btn-success save"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("save");?></button>
                            </div>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("login_account")." ".$this->lang->line("of")." ".$this->lang->line("list");?></h3>
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
    
        $('#addnew').click(function() {
	 	$(".show-create").show();
        });

	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#user-id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
        var delId = $('#user-id').val();
        $('#user-id').val("");
        if(delId!=""){
            $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>users/DelRecord",
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
        $('#alert-delete').show();
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>users/GetRecord",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(5000);
            }//Success
        });// End datagrid
	}

	function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>users/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#user-id').val(rsStr[0]);
            $('#employee-id').val(rsStr[1]);
            $('#employee-id').trigger("chosen:updated");
            $('#company-id').val(rsStr[2]);
            $('#company-id').trigger("chosen:updated");
            $('#branch-id').val(rsStr[3]);
            $('#branch-id').trigger("chosen:updated");
            $('#user-name').val(rsStr[4]);
            $('#password').val(rsStr[5]);
            $('#confirm-password').val(rsStr[5]);
            $('#role-id').val(rsStr[6]);
            $('#role-id').trigger("chosen:updated");
            $('#user-status').val(rsStr[7]);
            $('#user-status').trigger("chosen:updated");
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
		$('#user_status').val("1");
                $('#user_status').trigger("chosen:updated");
		$('.show-create').hide();
		$('#alert-delete').hide();
		$('#alert').hide();

		//Load dataGrid
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>users/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid 

		$('#reset').click(function(){
			$('#alert').hide();
			$('#user-id').val("");
			$('#employee-id').val("");
			$('#employee-id').trigger("chosen:updated");
			$('#company-id').val("");
			$('#company-id').trigger("chosen:updated");
			$('#branch-id').val("");
			$('#branch-id').trigger("chosen:updated");
			$('#user-name').val("");
			$('#password').val("");
			$('#confirm-password').val("");
			$('#role-id').val("");
			$('#role-id').trigger("chosen:updated");
			$('#user-status').val("");
		});// End reset
	});

	$('.save').click(function(){
		var user_id 	= $('#user-id').val();
		var employee_id = $('#employee-id').val();
		var company_id	= $('#company-id').val();
		var branch_id	= $('#branch-id').val();
		var user_name	= $('#user-name').val();
		var password	= $('#confirm-password').val();
		var role_id	= $('#role-id').val();
		var status	= $('#user-status').val();
	    	//alert(categoryName+categorySlug);
        	$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>users/AddRecord",
		data: "user-id="+user_id+"&employee-id="+employee_id+"&company-id="+company_id+"&branch-id="+branch_id+"&user-name="+user_name+"&password="+password+"&role-id="+role_id+"&status="+status,
		success: function(option){
		//alert(option);
		$('#user-id').val("");
		$('#employee-id').val("");
		$('#employee-id').trigger("chosen:updated");
		$('#company-id').val("");
		$('#company-id').trigger("chosen:updated");
		$('#branch-id').val("");
		$('#branch-id').trigger("chosen:updated");
		$('#user-name').val("");
		$('#password').val("");
		$('#confirm-password').val("");
		$('#role-id').val("");
		$('#role-id').trigger("chosen:updated");
		$('#user-status').val("");
		$('#dataGrid').html(option);
		$('.show-create').hide();
		$('#alert').show();
		$('#alert').html('Successfully saved!');
		}//Success
            });// ajax
            return false;
       });// End save

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
	//alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>users/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
                $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }

    function chkUserName(UserName){ 
	 if($('#user-id').val()==""){
		 $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>users/chkUserName",
			data: "user-name="+UserName,
			success: function(option){
			if(option==0){
				$('#alert-delete').hide();
				$('#alert').show();
				$('#alert').html("The user name is available");
				$('#isValid').val("1");
			}else{
				$('#alert').hide();
				$('#alert-delete').show();
				$('#alert-delete').html("The user name is not available");
				$('#isValid').val("0");
			}
			}//Success
	
		});// ajax
	}
	return false;
    }
    function chkConfPass(){
	 if($('#user-id').val()!=""){
		 var new_password   = $('#password').val(); 
		 var conf_password  = $('#confirm-password').val();
		 if(conf_password.length >= new_password.length){
			 if(new_password==conf_password){
				$('#alertDanger').hide();
				$('#alert').show();
				$('#alert').html("Your new password & confirm password is equal");
				$('#isvalid').val("1");
			}else{
				$('#alert').hide();
				$('#alertDanger').show();
				$('#alertDanger').html("Your new password & confirm password is not equal");
				$('#isvalid').val("0");
			}
		 }
	 }
	return false;
    }
    </script>
</body>
</html>
