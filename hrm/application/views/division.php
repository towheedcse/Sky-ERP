<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("division")." ".$this->lang->line("setup");?></title>
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
			<?php echo $this->lang->line("division");?>
			<?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
			<?php }?>
		    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("division");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("division");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                            <div class="card-body">
                                <div id="alert" class="alert alert-success"></div>
                                <form>
		                <div class="row">
		                    <div class="col-sm-6 col-md-6 col-lg-6">
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
				    <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="division_name"><?php echo $this->lang->line("division")." ".$this->lang->line("of")." ".$this->lang->line("name");?>:</label>
		                            <input type="text" required="" placeholder="<?php echo $this->lang->line("division")." ".$this->lang->line("of")." ".$this->lang->line("name");?>" name="division_name" class="form-control text-capitalize" id="division_name">
		                        </div>
		                    </div>
		                     
		                </div>
		                <div class="row">
		                	<div class="col-sm-6 col-md-6 col-lg-6">
		                		<div class="form-group required">
                                    <label class="control-label" for="division_status"><?php echo $this->lang->line("status");?>:</label>
                                    <div id="division_status_id">
                                        <select name="division_status" id="division_status" class="chosen-select" required="" placeholder="<?php echo $this->lang->line("status");?>">
                                            <option value="0"><?php echo $this->lang->line("inactive");?></option>
                                            <option value="1"><?php echo $this->lang->line("active");?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <br>
                                <button type="button" id="btnSave" style="margin-top: 7px;" class="btn btn-block btn-success save"><i class="fas fa-check-circle"></i> <?php echo $this->lang->line("save");?></button>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <br>
                                <button type="reset" id="reset" style="margin-top: 7px;" class="btn btn-block btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
                            </div>
                            <input type="hidden" name="division_id" class="form-control" id="division_id">   
		                </div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
		    <?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("division")." ".$this->lang->line("of")." ".$this->lang->line("list");?></h3>
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
    
        $('#addnew').click(function() {
	 	$(".show-create").show();
        });
   
	/* Start Delete Data*/
	function deleteRecord(id){
	    $('#division_id').val(id);
	    return false;
	}

	$('.confirm').click(function(){
	    	var delId = $('#division_id').val();
	   	$('#division_id').val("");
		if(delId!=""){
		    $.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>division/DelRecord",
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
            url: "<?php echo base_url();?>division/GetRecord",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(5000);
            }//Success
          });// End datagrid
	}

	function editRecord(id){
		$.ajax({
		type: 'POST',
		    url: "<?php echo base_url();?>division/FillRecord",
		    data: "id="+id,
		    success: function(option){
		    //alert(option);
		    rsStr = option.split("##&##");
		    //alert(rsStr[1]);
		    $('#company_id').val(rsStr[1]);
		    $('#company_id').trigger("chosen:updated");
		    $('#division_name').val(rsStr[2]);
		    $('#division_status').val(rsStr[3]);
		    $('#division_status').trigger("chosen:updated");
		    $('#division_id').val(rsStr[0]);
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
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>division/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid 

		$('#reset').click(function(){
			$('#alert').hide();			
			$('#company_id').val("");
			$('#company_id').trigger("chosen:updated");
			$('#division_name').val("");
			$('#division_status').val("1");
			$('#division_status').trigger("chosen:updated");
			$('#division_id').val("");
		});// End reset
		$('#division_status').val("1");
	});

	$('.save').click(function(){
		var company_id     	 = $('#company_id').val();
		var division_name    = $('#division_name').val();
		var division_status  = $('#division_status').val();
		var division_id      = $('#division_id').val();
		//alert(categoryName+categorySlug);
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>division/AddRecord",
		    data: "company_id="+company_id+"&division_name="+division_name+"&division_status="+division_status+"&division_id="+division_id,
		    success: function(option){
		    //alert(option);
		    $('#company_id').val("");
		    $('#company_id').trigger("chosen:updated");
		    $('#division_name').val("");
		    $('#division_status').val("1");
		    $('#division_status').trigger("chosen:updated");
		    $('#division_id').val("");
		    $('#dataGrid').html(option);
		    $('.show-create').hide();
		    $('#alert').show();
		    $('#alert').html('Successfully saved!');
		    }//Success
		});// ajax
	    	return false;
	});// End save

	/* Pagination Next Page */
	function nextPage(frm, to, pno){
	    	//alert("f-"+frm+",t-"+to+"p-"+pno);
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>division/GetRecord",
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
