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
                    <h1 class="m-0 text-dark"><?php echo $this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("role");?></h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("role");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->

            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("role");?></h3>
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
                                            <label class="control-label" for="role_name"><?php echo $this->lang->line("user_role").$this->lang->line("r")." ".$this->lang->line("name");?>:</label>
                                            <input type="text" required="" placeholder="<?php echo $this->lang->line("user_role").$this->lang->line("r")." ".$this->lang->line("name");?>" name="role_name" class="form-control text-capitalize" id="role_name">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group required">
                                            <label class="control-label" for="role_description"><?php echo $this->lang->line("role").$this->lang->line("r")." ".$this->lang->line("description");?>:</label>
                                            <input type="text" required="" placeholder="<?php echo $this->lang->line("role").$this->lang->line("r")." ".$this->lang->line("description");?>" name="role_description" class="form-control" id="role_description">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group required">
                                            <label class="control-label" for="role_status"><?php echo $this->lang->line("status");?>:</label>
                                            <div id="role_status_id">
                                                <select name="role_status" id="role_status" class="chosen-select" required="">
                                                    <option value="0"><?php echo $this->lang->line("inactive");?></option>
                                                    <option value="1"><?php echo $this->lang->line("active");?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" required="" placeholder="Role ID" name="role_id" class="form-control" id="role_id">
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
                    <br>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("user").$this->lang->line("r")." ".$this->lang->line("role").$this->lang->line("r")." ".$this->lang->line("list");?></h3>
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

	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#role_id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
        var delId = $('#role_id').val();
        $('#role_id').val("");
        if(delId!=""){
            $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>user_role/DelRecord",
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
            url: "<?php echo base_url();?>user_role/GetRecord",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(5000);
            }//Success
        });// End datagrid
	}

	function myFunction(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>user_role/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#role_name').val(rsStr[1]);
            $('#role_description').val(rsStr[2]);
            $('#role_status').val(rsStr[3]);
            $('#role_status').trigger("chosen:updated");
            $('#role_id').val(rsStr[0]);
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

        $('#alert-delete').hide();
        $('#alert').hide();

        //Load dataGrid
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>user_role/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success
		});// End datagrid

		$('#reset').click(function(){
			$('#alert').hide();
			$('#role_id').val("");
		});// End reset
	});

	$('.save').click(function(){
        var role_name           = $('#role_name').val();
        var role_description    = $('#role_description').val();
        var role_status         = $('#role_status').val();
        var role_id             = $('#role_id').val();
	    //alert(categoryName+categorySlug);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>user_role/AddRecord",
            data: "role_name="+role_name+"&role_description="+role_description+"&role_status="+role_status+"&role_id="+role_id,
            success: function(option){
                //alert(option);
                $('#role_name').val("");
                $('#role_description').val("");
                $('#role_status').val("");
                $('#role_status').trigger("chosen:updated");
                $('#role_id').val("");
                $('#dataGrid').html(option);
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
            url: "<?php echo base_url();?>user_role/GetRecord",
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
