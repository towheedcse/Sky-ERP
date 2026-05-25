<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
    <?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("building")." ".$this->lang->line("setup");?>
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
                        <div id="alert" class="alert alert-success"></div>
			<div id="alert-delete" class="alert alert-danger"></div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                        <h1 class="m-0 text-dark"><?php echo $this->lang->line("building");?>
		       <?php if($hasCreateOption){?>
		       <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       <?php }?></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                                <li class="breadcrumb-item active"><?php echo $this->lang->line("building");?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("building");?></h3>
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
                                                <label for="company_name" class="control-label"><?php echo $this->lang->line("company_name");?></label>
                                                <div id="com_id">
                                                    <select name="company_name" id="company_name" required="" class="form-control chosen-select" onChange="getBranchList(this.value,'branch_name')">
                                                        <option value=""><?php echo $this->lang->line("select");?> <?php echo $this->lang->line("company");?></option>
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
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="building_name"><?php echo $this->lang->line("building_name");?></label>
                                                <input type="text" required="" placeholder="<?php echo $this->lang->line("building_name");?>" name="building_name" class="form-control text-capitalize" id="building_name">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group">
                                                <label for="total_floor"><?php echo $this->lang->line("total_floor");?></label>
                                                <input type="text" placeholder="<?php echo $this->lang->line("total_floor");?>" name="total_floor" class="form-control" id="total_floor">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="form-group">
                                                <label for="building_description"><?php echo $this->lang->line("building")." ".$this->lang->line("of")." ".$this->lang->line("description");?></label>
                                                <input type="text" placeholder="<?php echo $this->lang->line("building")." ".$this->lang->line("of")." ".$this->lang->line("description");?>" name="building_description" class="form-control" id="building_description">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success save"><i class="fas fa-save"></i> <?php echo $this->lang->line("save");?></button>
                                        </div>
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <button type="reset" id="reset" style="margin-top: 7px;" class="btn btn-block btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
                                        </div>
					<input type="hidden" name="building_id" class="form-control" id="building_id">
                                    </div>

                                </form>
                            </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
                    
		    <?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("building");?> <?php echo $this->lang->line("list");?></h3>
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
        $('#building_id').val(id);
        return false;
    }

    $('.confirm').click(function(){
        var delId = $('#building_id').val();
        $('#building_id').val("");
        if(delId!=""){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>building/DelRecord",
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
                url: "<?php echo base_url();?>building/GetRecord",
                success: function(option){
                    $('#dataGrid').html(option);
                    $('#alert-delete').hide(5000);
                }//Success
            });// End datagrid
        }

        function myFunction(id){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>building/FillRecord",
                data: "id="+id,
                success: function(option){
                    //alert(option);
		    ArrStr = option.split("@@##@@");
		    rsStr  = ArrStr[0].split("##&##");
                    //alert(rsStr[1]);
		    getBranchList(rsStr[1],'branch_name',rsStr[2]);
                    $('#company_name').val(rsStr[1]);	
		    $('#company_name').trigger('chosen:updated');
                    $('#branch_name').val(rsStr[2]);	
		    $('#branch_name').trigger('chosen:updated');
                    $('#building_name').val(rsStr[3]);
                    $('#building_description').val(rsStr[4]);
                    $('#total_floor').val(rsStr[5]);
                    $('#building_id').val(rsStr[0]);
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
		url: "<?php echo base_url();?>building/GetRecord",
		success: function(option){
			$('#dataGrid').html(option);
		}//Success			
	});// End datagrid
    }); 
		
	$('#reset').click(function(){
		$('#alert').hide();
		$('#building_id').val("");
	});// End reset

    	
	$('.save').click(function(){
    	   var companyName         = $('#company_name').val();
	   var branchName          = $('#branch_name').val();
	   var buildingName        = $('#building_name').val();
	   var buildingDescription = $('#building_description').val();
	   var totalFloor          = $('#total_floor').val();
    	   var buildingId          = $('#building_id').val();
	   if(isNullAndUndefined(companyName)){ companyName=0;}
	   if(isNullAndUndefined(branchName)){ branchName=0;}
	   if(companyName >0 && branchName >0 && buildingName!=""){
		$.ajax({
		    type: 'POST',
		    url: "<?php echo base_url();?>building/AddRecord",
		    data: "company_name="+companyName+"&branch_name="+branchName+"&building_name="+buildingName+"&building_description="+buildingDescription+"&total_floor="+totalFloor+"&building_id="+buildingId,
		    success: function(option){
		        //alert(option);
		        $('#company_name').val("");
		        $('#branch_name').val("");
		        $('#building_name').val("");
		        $('#building_description').val("");
		        $('#total_floor').val("");
		        $('#building_id').val("");
		        $('#dataGrid').html(option);
			$('.show-create').hide();
		        $('#alert').show();
		        $('#alert').html('Successfully saved!');
		    }//Success
		});// ajax
	   }else{
		$('#alert').hide();
		$('#alert').html('');
		$('#alert-delete').show();
		$('#alert-delete').html('Record did not saved! Please fill data in required fields');		
	   }
	   return false;
	});// End save

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>building/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
            success: function (option) {
                $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
    function getBranchList(company_id,placement,branch_id=0){
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>building/GetAjaxBranchList",
            data: "company-id="+company_id+"&branch-id="+branch_id,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          return false;
    }

    function isNullAndUndefined(variable){
	if(variable == null || variable == undefined || variable == ""){
	    return true;
	}else if(isNaN(variable)){
	    return true;
	}
    }
    </script>
</body>
</html>
