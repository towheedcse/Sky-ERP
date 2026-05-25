<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("journal_voucher");?> </title>
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
       
    <!--Delete Modal-->
	<div class="modal fade" id="deleteModal" role="dialog" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("delete_modal_header");?></h4>
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
	<!--Delete Item Modal-->
	<div class="modal fade" id="deleteItemModal" role="dialog" tabindex="-1" aria-labelledby="confirmIDLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line("delete_modal_header");?></h4>
				</div>
				<div class="modal-body">
					<h5><?php echo $this->lang->line("delete_message");?></h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
					<button type="button" id="btnItemDelete" class="btn btn-danger confirm-item"><i class="fa fa-trash-o"></i> <?php echo $this->lang->line("delete");?></button>
				</div>
			</div>
		</div>
	</div>
	<!--Dishonored Modal-->
	<div class="modal fade" id="DishonoredModal" role="dialog" tabindex="-1" aria-labelledby="confirmDishonoredLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="color: orange"><i class="fa fa-exclamation-triangle"></i> Dishonored Cheque</h4>
				</div>
				<div class="modal-body">
					<h5>Are you sure you want to dishonored this cheque?</h5>
					<form>
					  <div class="form-group">
						<label for="recipient-name" class="col-form-label">Reason:</label>
						<input type="text" class="form-control" id="dishonor-remarks">
					  </div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-warning" data-dismiss="modal"><?php echo $this->lang->line("cancel");?></button>
					<button type="button" id="btnDishonored" class="btn btn-danger confirm-dishonored"><i class="fa fa-ban-o"></i> Dishonored</button>
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
				   <?php echo $this->lang->line("journal_voucher");?>				   
					</h1>
                  </div><!-- /.col -->
                  <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                      <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                      <li class="breadcrumb-item active"><?php echo $this->lang->line("journal_voucher");?></li>
                    </ol>
                  </div><!-- /.col -->
                </div><!-- /.row -->
				<input type="hidden" name="voucher-id" id="voucher-id" value="<?php echo $voucher_id;?>">
				<?php if($hasPrintOption){?> 			
				<div class="pull-right">
				<button type="button" onclick="PrintElem('#dataGrid')" style="margin-top:5px" class="btn btn-sm btn-success print"><span class="glyphicon glyphicon-print"> Print</span></button>
				</div>
				<?php }?>
				<div class="clearfix"></div>
				<div id="dataGrid" class="print-font"></div>	
				
              </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
           
            </div><!-- End Content -->
        </div><!-- /.content-wrapper --> 
      <?php require('copyright.php');?>
    </div><!-- /.wrapper --> 
    <?php require('jslinks4admin.php');?>
    
<script type="text/javascript">    
    $(function() {
        $('.chosen-select').chosen();
        $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
    });
    $(document).ready(function(){
        resizeChosen();
        jQuery(window).on('resize', resizeChosen);
	$('#alert-success').hide();
	$('#danger-alert').hide();
	
	var voucher_id=<?php echo $voucher_id;?>;
	//Load dataGrid
	loadReportForm();
	
    });
	
    function resizeChosen() {
        $(".chosen-container").each(function() {
            $(this).attr('style', 'width: 100%');
        });
    }
    $('.form_date').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0,
        pickerPosition: 'bottom-left'
    });

</script>

<script>
        
    function loadReportForm(){
	var voucher_id 	= $('#voucher-id').val();
	if(voucher_id >0){
	$.ajax({
		type: 'POST',
		url: "<?php echo base_url();?>voucher/GetVoucherInfo",
		data: "voucher-id="+voucher_id,
		success: function(option){
			$('#dataGrid').html(option);
			$('#danger-alert').hide();
		}//Success
	});// End datagrid
	}
    }	
    		
</script>
</body>
</html>
