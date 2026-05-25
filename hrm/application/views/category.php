<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Car Expense</title>
<?php require('csslinks4admin.php');?>
   <!-- <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css"/>-->
</head>
<body>
    <!--Add New Modal-->
    <div class="modal fade" data-refresh="true" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">

            <!-- Add New Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <p class="modal-title" style="font-size: x-large"><i class="fa fa-edit"></i> Add New Category</p>
                </div>
                <div class="modal-body">
                    <div id="alert" class="alert alert-success"></div>
                    <form id="categoryForm">
                        <div class="form-group required">
                            <label class="control-label" for="category-name">Category Name:</label>
                            <input type="text" required="" placeholder="Category Name" name="category_name" class="form-control text-capitalize" id="category_name">
                        </div>
                        <div class="form-group required">
                            <label class="control-label" for="category-title">Category Details:</label>
                            <input type="text" required="" placeholder="Category Details" name="category-title" class="form-control text-capitalize" id="category-title">
                        </div>
                        <input type="hidden" name="category-id" class="form-control" id="category-id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnSave" class="btn btn-success save"><span class="glyphicon glyphicon-saved"></span> Save </button>
                    <button type="reset" id="reset" class="btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-refresh"></span> Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!--Delete Modal-->
    <div class="modal fade" id="deleteModal" role="dialog" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> Delete Parmanently</h4>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btnDelete" class="btn btn-danger confirm"><i class="fa fa-trash-o"></i> Delete</button>
                </div>
            </div>
        </div>
    </div>
    <div id="container">
    <?php require('adminheader.php');?>
    <?php require('leftmenu.php');?>
        <div id="content"> <!-- Start content -->
        <div class="container-fluid"><!-- Start container-fluid -->
        <br>
        <div id="alert-delete" class="alert alert-danger"></div>
        <div class="clearfix"></div>
            <ol class="breadcrumb">
                <li><a class="bc-font" href="<?php echo SERVER?>/dashboard/Userhome">Home</a></li>
                <li class="active"><a class="bc-font" href="#">Category</a></li>
            </ol>
        <div class="pull-right">
        <!--span class="input-group-addon"-->
        <span data-toggle="tooltip" data-original-title="Add Category"><a class="btn btn-primary" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i></a></span>
        <!--/span-->
        </div>
        </div>
        <br>
        <div class="container-fluid"> <!-- Start container-fluid -->
        <div class="panel panel-primary">
        <div class="panel-heading"><i class="fa fa-list"></i> View Categories</div>
        <div class="panel-body">
            <?php if($this->session->flashdata('msg')!=""){?>
            <div align="center" class="alert fade in alert-danger">
            <button data-dismiss="alert" class="close" type="button"> ×</button>
        <?php  echo $this->session->flashdata('msg');?> </div> <?php } ?>
        <div id="dataGrid"></div>
        </div> <!-- End Panel Body-->
        </div> <!-- End Panel -->
        </div><!-- End container-fluid -->
        </div><!-- End content -->
        <footer id="footer"><a href="#"><span class="text-info">ATN News Software Solutions</span></a> &copy; 2015 All Rights Reserved.<br />Version Beta 1.0</footer>
    </div>
    <?php require('jslinks4admin.php');?>
    <script>
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#category_id').val(id);
        return false;
    }

    $('.confirm').click(function(){
        var delId = $('#category_id').val();
        $('#category_id').val("");
        if(delId!=""){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>category/DelRecord",
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
            url: "<?php echo base_url();?>category/GetRecord",
            success: function(option){
                $('#dataGrid').html(option);
                $('#alert-delete').hide(5000);
            }//Success
        });// End datagrid
    }

    function myFunction(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>category/FillRecord",
            data: "id="+id,
            success: function(option){
                //alert(option);
                rsStr = option.split("##&##");
                //alert(rsStr[1]);
                $('#category_name').val(rsStr[1]);
                $('#category_id').val(rsStr[0]);
                $('#alert').show();
                $('#alert').html('Ready to Edit!');
            }//Success

        });// ajax
        return false;
    }

    $(document).ready(function(){

	$('#btnSave').click(function() {
		setTimeout(function() {$('#addModal').modal('hide');}, 600);
	});

	$('#btnDelete').click(function() {
		$('#deleteModal').modal('hide');
	});

	//Load dataGrid
	$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>category/GetRecord",
			success: function(option){
				$('#dataGrid').html(option);
			}//Success			
		});// End datagrid 
		
		$('#reset').click(function(){
			$('#alert').hide();
			$('#category_id').val("");
		});// End reset
	});

	$('#alert-delete').hide();
	$('#alert').hide();
	$('.save').click(function(){
	   var categoryName= $('#category_name').val();
	   var categoryId= $('#category_id').val();
	   //alert(categoryName+categorySlug);
	   $.ajax({
    		type: 'POST',
    		url: "<?php echo base_url();?>category/AddRecord",
    		data: "category_name="+categoryName+"&category_id="+categoryId,
    		success: function(option){
    			//alert(option);
    			$('#category_name').val("");
    			$('#category_id').val("");
    			$('#dataGrid').html(option);
    			$('#alert').show();
    			$('#alert').html('Successfully saved!');
		    }//Success
	   });// ajax
	   return false;
    });// End save
    </script>
</body>
</html>