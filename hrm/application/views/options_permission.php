<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("options")." ".$this->lang->line("permission");?></title>
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
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("options")." ".$this->lang->line("permission");?></h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("options")." ".$this->lang->line("permission");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("options");?></h3>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("options")." ".$this->lang->line("permission")." ".$this->lang->line("list");?></h3>
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
        	$('#alert').hide();
		//Load dataGrid
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>options_permission/GetPermissionRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid
	});

      function savePermission(module_id, menu_id, options_id, role_id, frm, to, isChecked){
        if($(isChecked).prop('checked')==true){
            setPermission(module_id, menu_id, options_id, role_id, frm, to, "insert");
            //alert('checked'+module_id+" "+role_id);
        }else{
            setPermission(module_id, menu_id, options_id, role_id, frm, to, "delete");
        }
      }

      function setPermission(module_id, menu_id, options_id, role_id, frm, to, action_type){
        var company_id       = $('#src-company-id').val();	
        var src_module       = $("#src-module-id").val();
		var src_menu         = $("#src-menu-id").val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		if(isNullAndUndefined(src_module)){ src_module=0;}
		if(isNullAndUndefined(src_menu)){ src_menu=0;}
	   
		if(module_id>0 && menu_id>0 && options_id>0 && role_id>0){
		    $.ajax({
		        type: 'POST',
		        url: "<?php echo base_url();?>options_permission/SaveRecord",
		        data: "company-id="+company_id+"&module-id="+module_id+"&menu-id="+menu_id+"&options_id="+options_id+"&role_id="+role_id+"&action_type="+action_type+"&src-module="+src_module+"&src-menu="+src_menu+"&from=" + frm + "&to=" + to,
		        success: function(option){
		            //alert(option);
		            $('#dataGrid').html(option);
		            $('#alert').show();
		            $('#alert').html('Successfully saved!');
		        }//Success
		    });// ajax
		}
        	return false;
      }// End save

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
			url: "<?php echo base_url();?>options_permission/GetPermissionRecord",
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
		    url: "<?php echo base_url();?>options_permission/GetPermissionRecord",
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
