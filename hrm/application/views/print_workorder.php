<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("print")." ".$this->lang->line("workorder");?> </title>
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
            width: 100%;
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
               <?php echo $this->lang->line("customer")." ".$this->lang->line("workorder");?>
               <?php if($hasPrintOption){?>
               <div class="pull-right">
				<button type="button" onclick="PrintElem('#dataGrid')" style="margin-top:3px" class="btn btn-sm btn-success print"><span class="glyphicon glyphicon-print"> Print</span></button>
				<!--button type="button" onclick="DownloadPDF()" style="margin-top:3px" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-download"> Download</span></button-->							
				</div>
               <?php }?>
            </h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("customer")." ".$this->lang->line("workorder");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->                          
                <input type="hidden" name="workorder-id" id="workorder-id" value="<?php echo $workorder_id;?>">
				<input type="hidden" name="customer-id" id="customer-id" value="<?php echo $customer_id;?>">           
				<?php if($hasPrintOption){?>
                    <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("workorder")." ".$this->lang->line("details");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">						    							
							<div class="clearfix"></div>
                            <div id="dataGrid" class="print-font Printable"></div>
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
	
	$(function() {
        $('.chosen-select').chosen();
        $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
    });
    $(document).ready(function(){
        resizeChosen();
        jQuery(window).on('resize', resizeChosen);
		$('#alert-success').hide();
		$('#danger-alert').hide();
	
		var workorder_id=<?php echo $workorder_id;?>;
		//Load dataGrid
		loadReportForm();
	
    });
	
        
	function loadReportForm(){
		var workorder_id 	= $('#workorder-id').val();
		var customer_id     = $('#customer-id').val();
		if(customer_id >0){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>workorder/GetWorkorder",
			data: "workorder-id="+workorder_id+"&customer-id="+customer_id,
			success: function(option){
				$('#dataGrid').html(option);
				$('#danger-alert').hide();
			}//Success
		});// End datagrid
		}
    }	
    function DownloadPDF(){
		var workorder_id 	= $('#workorder-id').val();
		var url ="<?php echo base_url();?>workorder/downloadWOPDFForm/"+workorder_id;	
		window.location.href = url;	
	
    }
    </script>
</body>
</html>
