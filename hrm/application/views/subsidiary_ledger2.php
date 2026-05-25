<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("sl_level")."-".$this->lang->line("2");?></title>
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
				<?php echo $this->lang->line("subsidiary_ledger")." (".$this->lang->line("s.l").")"." ".$this->lang->line("level")."-".$this->lang->line("2");?>
			    <?php if($hasCreateOption){?>
		       	    <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		       	    <?php }?>
			    </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                                <li class="breadcrumb-item active"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("2");?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->
            
            <div id="content"> <!-- Start content -->
                <div class="container-fluid"> <!-- Start container-fluid -->
                    <div class="card card-primary show-create">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new")." ".$this->lang->line("sl_level")."-".$this->lang->line("2");?></h3>
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
                                                <label for="company-name" class="control-label"><?php echo $this->lang->line("company_name");?></label>
                                                <div id="com_id">
                                                    <select name="company-name" id="company-name" required="" class="form-control chosen-select">
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
                                                <label class="control-label" for="Parents Head"><?php echo $this->lang->line("group_ledger")." ".$this->lang->line("name");?></label>
                                                <div id="group-head">
                                                    <select name="parents-head" id="parents-head" class="chosen-select" onChange="getSubAccountList(this.value,'subsidiary-id')">
								<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("group_ledger");?></option>
								<?php foreach($gquery->result() as $row){
								echo '<option value="'.$row->group_id.'">'.$row->group_name.'</option>';
								}
								?>	
						    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="SL Level-1"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("1")." ".$this->lang->line("name");?></label>
                                                <div id="sh-level1-id">
                                                    <select name="subsidiary-id" id="subsidiary-id" class="chosen-select">
							  <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("1");?></option>
									
						    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md-6 col-lg-6">
                                            <div class="form-group required">
                                                <label class="control-label" for="SL Level-1 Name"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("2")." ".$this->lang->line("name");?></label>
                                                <input type="text" required="" placeholder="<?php echo $this->lang->line("sl_level")."-".$this->lang->line("2")." ".$this->lang->line("name");?>" name="subsidiary-name2" id="subsidiary-name2" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="sub2-id" class="form-control" id="sub2-id">
                                    <div class="row">
                                        <div class="col-sm-2 col-md-2 col-lg-2">
                                            <button type="button" style="margin-top: 7px;" class="btn btn-block btn-success save-record"><i class="fas fa-save"></i> <?php echo $this->lang->line("save");?></button>
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
			<h3 class="card-title"><i class="fa fa-filter"></i><?php echo $this->lang->line("search")." ".$this->lang->line("sl_level")."-".$this->lang->line("2");?></h3>
			<div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
			</div>
			<div class="card-body">
				<form action="" method="post" class="hidden-print" role="form">
					<div class="row">						
						<div class="col-sm-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="control-label" for="src-sales-person"><?php echo $this->lang->line("group_ledger")." ".$this->lang->line("name");?></label>
								<select name="src-top-head" id="src-top-head" class="form-control chosen-select" onChange="getSubAccountList(this.value,'src-sub-head')">
								<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("group_ledger");?></option>
								<?php foreach($gquery->result() as $row){
								echo '<option value="'.$row->group_id.'">'.$row->group_name.'</option>';
								}
								?>
								</select>
							</div>
						</div>
						<div class="col-sm-4 col-md-4 col-lg-4">
							<div class="form-group">
								<label class="control-label" for="src-sales-person"><?php echo $this->lang->line("sl_level")."-".$this->lang->line("1")." ".$this->lang->line("name");?></label>
								<select name="src-sub-head" id="src-sub-head" class="form-control chosen-select">
								<option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("sl_level")."-".$this->lang->line("1");?></option>
					
								</select>
							</div>
						</div>
						<div class="col-sm-4 col-md-4 col-lg-4">
							<div class="form-group text-right">
								<br>
								<button type="button" class="btn btn-md btn-info search" style="margin-top:5px; width:105px"><i class="fa fa-search"></i> <?php echo $this->lang->line("search");?> </button>
								<button type="button" onclick="PrintElem('#dataGrid')" class="btn btn-md btn-success print" style="margin-top:5px;width:105px"><span class="glyphicon glyphicon-print"> <?php echo $this->lang->line("print");?></span></button>
							</div>
						</div>	
					</div>
				</form>
			</div>	
		 </div>
		 <div class="clearfix"></div>

                 <div class="card box-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("sl_level")."-".$this->lang->line("2")." ".$this->lang->line("list");?></h3>
                            <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div> <!-- /.card-header -->  
                        <div class="card-body">			    
                            <div id="danger-alert" class="alert alert-danger"></div>
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
    </script>
    
<script type="text/javascript">
	$(document).ready(function () {
		resizeChosen();
		$('#btnSave').click(function() {
			setTimeout(function() {$('#addModal').modal('hide');}, 600);
		});
		$('#reset').click(function() {
			$(this).find('form').trigger('reset');
			$('#sub2-id').val("");	
			$('#company-name').val("0");	
			$('#company-name').trigger('chosen:updated');	
			$('#parents-head').val("0");	
			$('#parents-head').trigger('chosen:updated');
			$('#subsidiary-id').val("0");	
			$('#subsidiary-id').trigger('chosen:updated');
			$('#subsidiary-name2').val("");
		});
		$('.close').click(function() {
			$(this).find('form').trigger('reset');
			$('#sub2-id').val("");	
			$('#company-name').val("0");	
			$('#company-name').trigger('chosen:updated');	
			$('#parents-head').val("0");	
			$('#parents-head').trigger('chosen:updated');
			$('#subsidiary-id').val("0");	
			$('#subsidiary-id').trigger('chosen:updated');
			$('#subsidiary-name2').val("");
		});
		$('#btnDelete').click(function() {
			$('#deleteModal').modal('hide');
		});
		$('#addModal').on('hidden.bs.modal', function () {
			$('#alert').hide();
			$(this).find('form').trigger('reset');
		});
		$('.show-create').hide();
		$('#alert').hide();
		$('#danger-alert').hide();
		//Load dataGrid
		reloadDataGrid();
		$('#reset').click(function(){
			$(this).find('form').trigger('reset');
			$('#sub2-id').val("");	
			$('#company-name').val("0");	
			$('#company-name').trigger('chosen:updated');	
			$('#parents-head').val("0");	
			$('#parents-head').trigger('chosen:updated');
			$('#subsidiary-id').val("0");	
			$('#subsidiary-id').trigger('chosen:updated');
			$('#subsidiary-name2').val("");
		});// End reset
	});
</script>
<script>
	/* Start Delete Data*/
	function deleteRecord(id){
		$('#sub2-id').val(id);
		return false;
	}
	$('.confirm').click(function(){
		var delid = $('#sub2-id').val();
		$('#sub2-id').val("");
		if(delid!=""){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>subsidiary_ledgertwo/DeleteRecord",
				data: "id="+delid,
				success: function(option){
					$('#danger-alert').show(1000);
					$('#danger-alert').html('Record deleted successfully!!!');
					reloadDataGrid();
				}//Success
			});// ajax
			return false;
		}
	});// End reset
	/* End Delete Data*/

	function reloadDataGrid(){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>subsidiary_ledgertwo/GetRecords",
			success: function(option){
				$('#dataGrid').html(option);
				$('#danger-alert').hide();
			}//Success
		});// End datagrid
	}
	$('.search').click(function(){
        	var srcTopHead      = $("#src-top-head").val();
		var srcSubHead      = $("#src-sub-head").val();
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>subsidiary_ledgertwo/GetRecords",
			data: "src-top-head="+srcTopHead+"&src-sub-head="+srcSubHead,
            		success: function(option){
                		$('#dataGrid').html(option);
            		}//Success
        	});// ajax
                return false;
        });// End search
	function editRecord(id){
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>subsidiary_ledgertwo/FillRecord",
			data: "id="+id,
			success: function(option){
				//alert(option);
				ArrStr = option.split("@@##@@");
				rsStr  = ArrStr[0].split("##&##");				
				//alert(rsStr[1]);
				getSubAccountList(rsStr[2],"subsidiary-id",rsStr[3]);
				
				$('#sub2-id').val(rsStr[0]);
				$('#company-name').val(rsStr[1]);	
				$('#company-name').trigger('chosen:updated');				
				$('#parents-head').val(rsStr[2]);
				$('#parents-head').trigger('chosen:updated');	
				$('#subsidiary-id').val(rsStr[3]);
				$('#subsidiary-id').trigger('chosen:updated');
				$('#subsidiary-name2').val(rsStr[4]);
	    			$('.show-create').show();
				$('#alert').show();
				$('#alert').html('Ready to Edit!');
			}//Success

		});// ajax
		return false;
	 }

	$('.save-record').click(function(){
		var companyName		= $('#company-name').val();
		var GroupHead		= $('#parents-head').val();
		var SubsidiaryId	= $('#subsidiary-id').val();
		var SubsidiaryName	= $('#subsidiary-name2').val().replace(/&/g,'U+0026'); 
		var Subsidiary2Id	= $('#sub2-id').val();
			
		//alert(companyName+" C:"+companyName+" G:"+GroupHead+" S:"+SubsidiaryId);
		if((SubsidiaryName !="") && (parseInt(companyName) >0) && (parseInt(GroupHead) >0) && (parseInt(SubsidiaryId) >0)){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>subsidiary_ledgertwo/AddRecord",
				data: "company-name="+companyName+"&subsidiary-name2="+SubsidiaryName+"&group-head="+GroupHead+"&subsidiary-id="+SubsidiaryId+"&sub2-id="+Subsidiary2Id,
				success: function(option){
					//alert(option);
					$(this).find('form').trigger('reset');
					$('#sub2-id').val("");	
					$('#company-name').val("0");	
					$('#company-name').trigger('chosen:updated');	
					$('#parents-head').val("0");	
					$('#parents-head').trigger('chosen:updated');
					$('#subsidiary-id').val("0");	
					$('#subsidiary-id').trigger('chosen:updated');
					$('#subsidiary-name2').val("");
					$('#dataGrid').html(option);
					$('.show-create').hide();
					$('#alert').show();
					$('#alert').html('Record has been saved successfully!!!');
				}//Success
			});// ajax
		}else{			
			$('#danger-alert').show();
			$('#danger-alert').html('Record did not saved! Please fill data in required fields');
			return false;
		}
		return false;
	});// End save
	
	/* Pagination Next Page */
	function nextPage(frm, to, pno) {
		var srcTopHead      = $("#src-top-head").val();
		var srcSubHead      = $("#src-sub-head").val();
		$.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>subsidiary_ledgertwo/GetRecords",
			data: "src-top-head="+srcTopHead+"&src-sub-head="+srcSubHead+"&from=" + frm + "&to=" + to + "&page_no=" + pno,
			success: function (option) {
				$('#dataGrid').html(option);
			}//Success
		});// End datagrid
		return false;

	}
	function getSubAccountList(group_id,placement,sub_head=0){
	  var company_id = $("#company-name").val();
          $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>chartofaccounts/GetAjaxSubAccountList",
            data: "company-id="+company_id+"&group-id="+group_id+"&sub-head="+sub_head,
            success: function(option){
                //alert(option);
                $('#'+placement).html(option);
                $('#'+placement).trigger('chosen:updated');
            }//Success

          });// ajax
          return false;
    	}
</script>
</body>
</html>
