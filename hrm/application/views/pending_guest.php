<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("case");?> </title>
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
            width: 8%;
        }    
    </style>
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
                    <h1 class="m-0 text-dark">
                    <?php echo $this->lang->line("pending")." ".$this->lang->line("card")." ".$this->lang->line("guest");?>
                    </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("guest");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->    
                
				<div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("search")." ".$this->lang->line("guest");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">
                            <form>
    		                <div class="row">
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <div class="form-group required">
                                        <label class="control-label" for="src-guest-name"><?php echo $this->lang->line("name_of_parties");?>:</label>
                                        <select name="src-guest-name" id="src-guest-name" class="chosen-select">
                                                <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("name_of_parties");?></option>
                                                
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-md-4 col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label" for="src-mobile"><?php echo $this->lang->line("mobile");?>:</label>
                                        <input type="text" placeholder="<?php echo $this->lang->line("mobile");?>" name="src-mobile" class="form-control" id="src-mobile">
                                    </div>
                                </div>
    		                </div>
		                    
    						<div class="row">
    							<div class="col-sm-1 col-md-2 col-lg-2">
    							 <button type="button" class="btn btn-block btn-md btn-success search"><i class="fas fa-search"></i> <?php echo $this->lang->line("search");?></button>							 
    							</div>	
    							<?php if($hasPrintOption){?> 
    							<div class="col-sm-1 col-md-2 col-lg-2">
                				<button type="button" onclick="PrintElem('#dataGrid')" class="btn btn-block btn-md btn-primary print"><i class="fas fa-print"></i> Print</button>
                				</div>
                				<?php }?>
    							<div class="col-sm-1 col-md-2 col-lg-2">
    							 <button type="reset" id="reset" class="btn btn-block btn-md btn-warning clear"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
    							</div>
    							<div class="col-sm-4 col-md-4 col-lg-4"></div>
    						</div>
		            </form>
                    </div> <!-- End Card Body-->
                    </div> <!-- End Card -->
					
				<?php if($hasViewOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("pending")." ".$this->lang->line("card")." ".$this->lang->line("guest")." ".$this->lang->line("list");?></h3>
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
    
    jQuery('.datepicker_mask').datetimepicker({
         timepicker:false,
         mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
         format:'d/m/Y'
    });
	//jQuery('#datetimepicker').datetimepicker();	
	
    
    $(document).ready(function(){
        //Start Chosen Responsive//
        resizeChosen();	
	    $('#alert-delete').hide();
        jQuery(window).on('resize', resizeChosen);
        $(".chosen-select").val('').trigger("chosen:updated");
        //End Chosen Responsive//
		//==== Start Temp Set =====		
	    $('#institute_id').val("1");
	    $('#branch_id').val("1");
        getAjaxGuestList();
        //Load dataGrid
        reloadDataGrid();
    }); 
    function getAjaxGuestList(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>registration/GetAjaxGuestList",
            success: function(option){
             $('#src-guest-name').html(option);
			 $('#src-guest-name').trigger("chosen:updated");
            }//Success
        });// End datagrid
    }
    function reloadDataGrid(){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>registration/GetGuestRecord",
            data: "status=0",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
	
 	$('.search').click(function(){
		var name_of_guest   = $('#src-guest-name').val();
		var srcmobile       = $('#src-mobile').val();
		var status          = 0;
		if(isNullAndUndefined(status)){ status=0;}
		
		if(name_of_guest !="" || srcmobile !=""){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>registration/GetGuestRecord",
			data: "name_of_guest="+name_of_guest+"&mobile="+srcmobile+"&status="+status,
            		success: function(option){
                		$('#dataGrid').html(option);
						$('#alert-delete').hide();
						$('.print').show();
            		}//Success
        	});// ajax
		}else{
		  $('#alert-delete').show();
		  $('#alert-delete').html('Please fill data in required fields');		
	    }
        return false;
    });// End search
	
    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
		var name_of_guest   = $('#src-guest-name').val();
		var srcmobile       = $('#src-mobile').val();
		var status          = 0;
		if(isNullAndUndefined(status)){ status=0;}
		
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>registration/GetGuestRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno+"&name_of_guest="+name_of_guest+"&mobile="+srcmobile+"&status="+status,
            success: function (option) {
            $('#dataGrid').html(option);
            }//Success
        });// End datagrid
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
