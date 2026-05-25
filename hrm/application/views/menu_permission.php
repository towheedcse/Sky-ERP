<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::Menu Permission</title>
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
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("menu")." ".$this->lang->line("permission");?></h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("menu")." ".$this->lang->line("permission");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("menu");?></h3>
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
		                                <select name="company_id" id="company_id" class="chosen-select" required="">
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
                                        <select name="module_id" id="module_id" class="chosen-select" required="">
                                            <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("module_name");?></option>
                                            <?php foreach($mquery->result() as $row){
                                                echo '<option value="'.$row->module_id.'">'.$row->module_name.'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <div class="form-group required">
                                    <label class="control-label" for="menu_status"><?php echo $this->lang->line("menu")." ".$this->lang->line("status");?>:</label>
                                    <div id="status_id">
                                        <select name="menu_status" id="menu_status" class="chosen-select" required="">
                                            <option value="0"><?php echo $this->lang->line("inactive");?></option>
                                            <option value="1"><?php echo $this->lang->line("active");?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <br>
                                <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>
                            </div>
		                </div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
		    <?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("menu")." ".$this->lang->line("permission")." ".$this->lang->line("list");?></h3>
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
			url: "<?php echo base_url();?>menu_permission/GetPermissionRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid
	});
    $('.search').click(function(){
		var company_id       = $('#company_id').val();
		if(isNullAndUndefined(company_id)){ company_id=0;}
		var module_id       = $('#module_id').val();
		if(isNullAndUndefined(module_id)){ module_id=0;}
		var menu_status       = $('#menu_status').val();
		if(isNullAndUndefined(menu_status)){ menu_status=1;}

		if(company_id >0 && module_id >=0){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>menu_permission/GetPermissionRecord",
			data: "company_id="+company_id+"&module_id="+module_id+"&status="+menu_status,
            		success: function(option){
                		$('#dataGrid').html(option);
            		}//Success
        	});// ajax
		}else{
		  $('#alert').hide();
		  $('#alert').html('');
		  $('#alert-delete').show();
		  $('#alert-delete').html('Record did not fount! Please fill data in required fields');		
	        }
                return false;
    });// End search
    function savePermission(module_id, menu_id, role_id, isChecked){
        if($(isChecked).prop('checked')==true){
            setPermission(module_id, menu_id, role_id, "insert");
            //alert('checked'+module_id+" "+role_id);
        }else{
            setPermission(module_id, menu_id, role_id, "delete");
        }
    }

    function setPermission(module_id, menu_id, role_id, action_type){
        var company_id   = $('#company_id').val();
        if(isNullAndUndefined(company_id)){
            company_id=0;
        }
    //alert(categoryName+categorySlug);
        if(module_id>0 && menu_id>0&& role_id>0){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>menu_permission/SaveRecord",
                data: "company_id="+company_id+"&module_id="+module_id+"&menu_id="+menu_id+"&role_id="+role_id+"&action_type="+action_type,
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

	/* Pagination Next Page */
	function nextPage(frm, to, pno) {
	//alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>menu_permission/GetPermissionRecord",
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
