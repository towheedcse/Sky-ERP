<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("menu")." ".$this->lang->line("setup");?></title>
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
                    <button type="button" class="btn btn-warning" data-dismiss="modal"> <?php echo $this->lang->line("cancel");?></button>
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
		       <?php echo $this->lang->line("menu");?>
		       <?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       <?php }?>
		    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("menu");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("menu");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                            <div class="card-body">
                                <div id="alert" class="alert alert-success"></div>
                                <form>
		                <div class="row">
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="company_id"><?php echo $this->lang->line("company_name");?>:</label>
		                            <div id="com_id">
		                                <select name="company_id" id="company_id" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("company_name");?>">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company_name");?></option>
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
                                    <label class="control-label" for="module_id"><?php echo $this->lang->line("module_name");?>:</label>
                                    <div id="mod_id">
                                        <select name="module_id" id="module_id" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("module_name");?>">
                                            <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("module_name");?></option>
                                            <?php foreach($mquery->result() as $row){
                                                echo '<option value="'.$row->module_id.'">'.$row->module_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group required">
                                    <label class="control-label" for="menu_slug"><?php echo $this->lang->line("menu")." ".$this->lang->line("slug");?>:</label>
                                    <input type="text" required="" placeholder="<?php echo $this->lang->line("menu")." ".$this->lang->line("slug");?>" name="menu_slug" class="form-control" id="menu_slug">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group required">
                                    <label class="control-label" for="menu_name"><?php echo $this->lang->line("menu_name");?>:</label>
                                    <input type="text" required="" placeholder="<?php echo $this->lang->line("menu_name");?>" name="menu_name" class="form-control text-capitalize" id="menu_name">
                                </div>
		                    </div>
		                    <div class="col-sm-4 col-md-4 col-lg-4">
		                        <div class="form-group required">
		                            <label class="control-label" for="order_no"><?php echo $this->lang->line("order_number");?>:</label>
		                            <input type="text" required="" placeholder="<?php echo $this->lang->line("order_number");?>" name="order_no" class="form-control" id="order_no">
		                        </div>
		                    </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group required">
                                    <label class="control-label" for="menu_status"><?php echo $this->lang->line("status");?>:</label>
                                    <div id="menu_status_id">
                                        <select name="menu_status" id="menu_status" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("status");?>">
                                            <option value="0"><?php echo $this->lang->line("inactive");?></option>
                                            <option value="1"><?php echo $this->lang->line("active");?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="menu_id" class="form-control" id="menu_id">
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
				<div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("menu");?></h3>
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
		                                <select name="src-company-id" id="src-company-id" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("company_name");?>">
		                                    <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("company_name");?></option>
		                                    <?php foreach($cquery->result() as $row){
		                                        echo '<option value="'.$row->company_id.'">'.$row->company_name.'</option>';
		                                    }
		                                    ?>
		                                </select>
		                            </div>
		                        </div>
		                    </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group required">
                                    <label class="control-label" for="src-module-id"><?php echo $this->lang->line("module_name");?>:</label>
                                    <div id="mod_id">
                                        <select name="src-module-id" id="src-module-id" class="chosen-select" required="">
                                            <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("module_name");?></option>
                                            <?php foreach($mquery->result() as $row){
                                                echo '<option value="'.$row->module_id.'">'.$row->module_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group required">
                                    <label class="control-label" for="src-menu-id"><?php echo $this->lang->line("menu_name");?>:</label>
                                    <div id="mu_id">
                                        <select name="src-menu-id" id="src-menu-id" class="chosen-select" required="">
                                            <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("menu_name");?></option>
                                            <?php foreach($muquery->result() as $row){
                                                echo '<option value="'.$row->menu_id.'">'.$row->menu_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <br>
                                <button type="button" style="margin-top: 9px;" class="btn btn-sm btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>
                            </div>
		                </div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    <br>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("menu").$this->lang->line("r")." ".$this->lang->line("list");?></h3>
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
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>-->
    <!--<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>-->
    <script>
        $(function() {
	    $('.chosen-select').chosen();
	    $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
	});

	function resizeChosen() {
	    $(".chosen-container").each(function() {
		$(this).attr('style', 'width: 100%');
	    });
	}   
    
        $('#addnew').click(function() {
	 	$(".show-create").show();
        });
   
	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#menu_id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
	   var delId = $('#menu_id').val();
	   $('#menu_id').val("");
           if(delId!=""){
            $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>menu/DelRecord",
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
	    url: "<?php echo base_url();?>menu/GetRecord",
	    success: function(option){
		$('#dataGrid').html(option);
		$('#alert-delete').hide(5000);
	    }//Success
	});// End datagrid
	}

	function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>menu/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#company_id').val(rsStr[1]);
            $('#company_id').trigger("chosen:updated");
            $('#module_id').val(rsStr[2]);
            $('#module_id').trigger("chosen:updated");
            $('#menu_slug').val(rsStr[3]);
            $('#menu_name').val(rsStr[4]);
            $('#order_no').val(rsStr[5]);
            $('#menu_status').val(rsStr[6]);
            $('#menu_status').trigger("chosen:updated");
            $('#menu_id').val(rsStr[0]);
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
		$('#menu_status').val("1");
                $('#menu_status').trigger("chosen:updated");
		$('.show-create').hide();
		$('#alert-delete').hide();
		$('#alert').hide();
		//Load dataGrid
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>menu/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid 

		$('#reset').click(function(){
			$('#alert').hide();
			$('#branch_id').val("");
		});// End reset
	});

	$('.save').click(function(){
        var company_id      = $('#company_id').val();
        var module_id       = $('#module_id').val();
        var menu_slug       = $('#menu_slug').val();
        var menu_name       = $('#menu_name').val();
        var order_no        = $('#order_no').val();
        var menu_status     = $('#menu_status').val();
        var menu_id         = $('#menu_id').val();
        //alert(categoryName+categorySlug);
	    $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>menu/AddRecord",
            data: "company_id="+company_id+"&module_id="+module_id+"&menu_slug="+menu_slug+"&menu_name="+menu_name+"&order_no="+order_no+"&menu_status="+menu_status+"&menu_id="+menu_id,
            success: function(option){
            //alert(option);
            $('#company_id').val("");
            $('#company_id').trigger("chosen:updated");
            $('#module_id').val("");
            $('#module_id').trigger("chosen:updated");
            $('#menu_slug').val("");
            $('#menu_name').val("");
            $('#order_no').val("");
	        $('#menu_status').val("1");
	        $('#menu_status').trigger("chosen:updated");
            $('#menu_id').val("");
            $('#dataGrid').html(option);
	        $('.show-create').hide();
            $('#alert').show();
            $('#alert').html('Successfully saved!');
		    }//Success
	    });// ajax
	    return false;
	});// End save
    	
    function isNullAndUndefined(variable){
        if(variable == null || variable == undefined || variable == ""){
            return true;
        }else if(isNaN(variable)){
            return true;
        }
    }	
	$('.search').click(function(){
		var company_id       = $('#src-company-id').val();	
        var src_module       = $("#src-module-id").val();
		var src_menu         = $("#src-menu-id").val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		if(isNullAndUndefined(src_module)){ src_module=0;}
		if(isNullAndUndefined(src_menu)){ src_menu=0;}
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>menu/GetRecord",
			data: "company-id="+company_id+"&src-module="+src_module+"&src-menu="+src_menu,
            		success: function(option){
                		$('#dataGrid').html(option);
            		}//Success
        	});// ajax
                return false;
    });// End search
	/* Pagination Next Page */
	function nextPage(frm, to, pno) {
		var company_id       = $('#src-company-id').val();	
        var src_module       = $("#src-module-id").val();
		var src_menu         = $("#src-menu-id").val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		if(isNullAndUndefined(src_module)){ src_module=0;}
		if(isNullAndUndefined(src_menu)){ src_menu=0;}
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>menu/GetRecord",
		    data: "company-id="+company_id+"&src-module="+src_module+"&src-menu="+src_menu+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
		    success: function (option) {
		    $('#dataGrid').html(option);
		    }//Success
		});// End datagrid
		return false;
	}
    </script>
</body>
</html>
