<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
    <?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("project")." ".$this->lang->line("setup");?>
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
			       <?php echo $this->lang->line("project");?>
			       <?php if($hasCreateOption){?>
			       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
			       <?php }?>
			    </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                                <li class="breadcrumb-item active"><?php echo $this->lang->line("project");?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("project");?></h3>
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
                                                <label for="company_name" class="control-label"><?php echo $this->lang->line("company_name");?></label>
                                                <div id="com_id">
                                                    <select name="company_name" id="company_name" required="" class="form-control chosen-select">
                                                        <option value=""><?php echo $this->lang->line("select");?> <?php echo $this->lang->line("company");?></option>
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
                                                <label for="branch_name" class="control-label"><?php echo $this->lang->line("branch_name");?></label>
                                                <div id="branch_id">
                                                    <select name="branch_name" id="branch_name" class="form-control chosen-select" style="width: 100%;" required="">
                                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("branch_name");?></option>
                                                        <?php foreach($bquery->result() as $row){
                                                            echo '<option value="'.$row->branch_id.'">'.$row->branch_name.'</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                            <div class="form-group required">
                                                <label class="control-label" for="project_name"><?php echo $this->lang->line("project_name");?></label>
                                                <input type="text" required="" placeholder="<?php echo $this->lang->line("project_name");?>" name="project_name" class="form-control text-capitalize" id="project_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="project_description"><?php echo $this->lang->line("project")." ".$this->lang->line("description");?></label>
                                                <input type="text" placeholder="<?php echo $this->lang->line("project")." ".$this->lang->line("description");?>" name="project_description" class="form-control" id="project_description">
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="project_price"><?php echo $this->lang->line("project_price");?></label>
                                                <input type="text" placeholder="<?php echo $this->lang->line("project_price");?>" name="project_price" class="form-control" id="project_price">
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4">
                                            <div class="form-group">
                                                <label for="project_budget"><?php echo $this->lang->line("project_budget");?></label>
                                                <input type="text" placeholder="<?php echo $this->lang->line("project_budget");?>" name="project_budget" class="form-control" id="project_budget">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="project_id" class="form-control" id="project_id">
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success save"><i class="fas fa-save"></i> <?php echo $this->lang->line("save");?></button>
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("project");?> <?php echo $this->lang->line("list");?></h3>
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
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#project_id').val(id);
        return false;
    }

    $('.confirm').click(function(){
        var delId = $('#project_id').val();
        $('#project_id').val("");
        if(delId!=""){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>project/DelRecord",
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
                url: "<?php echo base_url();?>project/GetRecord",
                success: function(option){
                    $('#dataGrid').html(option);
                    $('#alert-delete').hide(5000);
                }//Success
            });// End datagrid
        }

        function editRecord(id){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>project/FillRecord",
                data: "id="+id,
                success: function(option){
                    //alert(option);
                    rsStr = option.split("##&##");
                    //alert(rsStr[1]);
                    $('#company_name').val(rsStr[1]);
                    $('#branch_name').val(rsStr[2]);
                    $('#project_name').val(rsStr[3]);
                    $('#project_description').val(rsStr[4]);
                    $('#project_price').val(rsStr[5]);
                    $('#project_budget').val(rsStr[6]);
                    $('#project_id').val(rsStr[0]);
	    	    $('.show-create').show();
                    $('#alert').show();
                    $('#alert').html('Ready to Edit!');
                }//Success

            });// ajax
            return false;
        }

    $(document).ready(function(){
        $('.select2').select2();
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
    	$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>project/GetRecord",
		success: function(option){
			$('#dataGrid').html(option);
		}//Success			
	});// End datagrid 
	
	$('#reset').click(function(){
		$('#alert').hide();
		$('#project_id').val("");
	});// End reset
    });

    
	$('.save').click(function(){
	    	var companyName         = $('#company_name').val();
		var branchName          = $('#branch_name').val();
		var projectName         = $('#project_name').val();
		var projectDescription  = $('#project_description').val();
		var projectPrice        = $('#project_price').val();
		var projectBudget       = $('#project_budget').val();
		var projectId           = $('#project_id').val();
		    //alert(categoryName+categorySlug);
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>project/AddRecord",
		    data: "company_name="+companyName+"&branch_name="+branchName+"&project_name="+projectName+"&project_description="+projectDescription+"&project_price="+projectPrice+"&project_budget="+projectBudget+"&project_id="+projectId,
		    success: function(option){
		        //alert(option);
		        $('#company_name').val("");
		        $('#branch_name').val("");
		        $('#project_name').val("");
		        $('#project_description').val("");
		        $('#project_price').val("");
		        $('#project_budget').val("");
		        $('#project_id').val("");
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
            url: "<?php echo base_url();?>project/GetRecord",
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
