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
    <!--Add New Party Modal--> 
    <div class="modal fade" data-refresh="true" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <!-- Add New Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" style="font-size: x-large"><i class="fa fa-edit"></i> Add New Parties</p>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
		    <div id="st-danger-alert" class="alert alert-danger"></div>
                    <form>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
								<div class="form-group required">
									<label for="prt-party-name" class="control-label"><?php echo $this->lang->line("party_name");?> :</label>
									<input type="text" required="" placeholder="<?php echo $this->lang->line("party_name");?>" name="prt-party-name" class="form-control" id="prt-party-name">
								</div>
							</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
								<div class="form-group required">
									<label class="control-label" for="prt-father-name"><?php echo $this->lang->line("father_name");?> :</label>
									<input type="text" required="" placeholder="<?php echo $this->lang->line("father_name");?>" name="prt-father-name" class="form-control" id="prt-father-name">
								</div>
							</div>
                        </div>  
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group required">
									<label class="control-label" for="prt-gender"><?php echo $this->lang->line("gender");?> :</label>
                                    <select name="prt-gender" id="prt-gender" class="chosen-select" required="">    
                                        <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("gender");?></option>
                                        <option value="Male"><?php echo $this->lang->line("male");?></option>
                                        <option value="Female"><?php echo $this->lang->line("female");?></option>
                                        <option value="Others"><?php echo $this->lang->line("others");?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group required">
									<label class="control-label" for="prt-mobile"><?php echo $this->lang->line("mobile");?> :</label>
                                    <input type="text" placeholder="<?php echo $this->lang->line("mobile");?>" name="prt-mobile" class="form-control" id="prt-mobile">
                                </div>
                            </div>
                        </div> 
                        <input type="hidden" name="account-id" class="form-control" id="account-id" value=""> 
                        <input type="hidden" name="head-type" class="form-control" id="head-type" value="1">
                        
                        <input type="hidden" name="head-id" class="form-control" id="head-id" value="">
                        <input type="hidden" name="slgroup_id" class="form-control" id="slgroup_id" value="1">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="reset" class="btn btn-danger" data-dismiss="modal"> Cancel</button>
                    <button type="button" id="btnSave" class="btn btn-success save-party"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>

        </div>
    </div>
    
    
    <!--Add New Reference Modal--> 
    <div class="modal fade" data-refresh="true" id="addRefModal" tabindex="-1" role="dialog" aria-labelledby="addRefModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <!-- Add New Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" style="font-size: x-large"><i class="fa fa-edit"></i> Add New Reference</p>
                    <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
		    <div id="ref-danger-alert" class="alert alert-danger"></div>
                    <form>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
								<div class="form-group required">
									<label for="ref-reference-name" class="control-label"><?php echo $this->lang->line("reference")." ".$this->lang->line("name");?> :</label>
									<input type="text" required=""  placeholder="<?php echo $this->lang->line("reference");?>" name="ref-reference_name" class="form-control" id="ref-reference-name">
								</div>
							</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12">
								<div class="form-group required">
									<label class="control-label" for="ref-father-name"><?php echo $this->lang->line("father_name");?> :</label>
									<input type="text" required="" placeholder="<?php echo $this->lang->line("father_name");?>" name="ref-father-name" class="form-control" id="ref-father-name">
								</div>
							</div>
                        </div>  
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group required">
									<label class="control-label" for="ref-gender"><?php echo $this->lang->line("gender");?> :</label>
                                    <select name="ref-gender" id="ref-gender" class="chosen-select" required="">    
                                        <option value="0"><?php echo $this->lang->line("select")." ".$this->lang->line("gender");?></option>
                                        <option value="Male"><?php echo $this->lang->line("male");?></option>
                                        <option value="Female"><?php echo $this->lang->line("female");?></option>
                                        <option value="Others"><?php echo $this->lang->line("others");?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group required">
									<label class="control-label" for="ref-mobile"><?php echo $this->lang->line("mobile");?> :</label>
                                    <input type="text" placeholder="<?php echo $this->lang->line("mobile");?>" name="ref-mobile" class="form-control" id="ref-mobile">
                                </div>
                            </div>
                        </div> 
                        <input type="hidden" name="reference-id" class="form-control" id="reference-id" value=""> 
                        <input type="hidden" name="ref-head-type" class="form-control" id="ref-head-type" value="2">
                        
                        <input type="hidden" name="ref-head-id" class="form-control" id="ref-head-id" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="reset" class="btn btn-danger" data-dismiss="modal"> Cancel</button>
                    <button type="button" id="btnRefSave" class="btn btn-success save-reference"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>

        </div>
    </div>
    
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
               <?php echo $this->lang->line("guest");?>
               <?php if($hasCreateOption){?>
               <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
               <?php }?>
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
                    <div class="card card-primary show-create">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("guest_reg");?></h3>
						<div class="card-tools">
						  <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
						</div>
					</div> <!-- /.card-header -->  
                    <div class="card-body">
                        <div id="alert" class="alert alert-success"></div>
                        <div class="container-fluid">
							<form method="post" id="InputForm" enctype="multipart/form-data" action="<?php echo base_url();?>registration/saveData">
							<div class="card card-info card-outline">							
								<div class="bg-default">
									<h3 class="container-fluid card-title"><?php echo $this->lang->line("guest_details");?></h3><hr>								
								</div><!-- /.card-header -->
								<div class="container-fluid">
									<div class="row">
										<div class="col-sm-8 col-md-8 col-lg-8">
											<div class="form-group required">
												<label class="control-label" for="name_of_guest"><?php echo $this->lang->line("name_of_parties");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("name_of_parties");?>" name="name_of_guest" class="form-control" id="name_of_guest">
											</div>
										</div>
										
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group required">	
												<label class="control-label" for="reception_date"><?php echo $this->lang->line("reception_date");?></label>
												<div class="input-group">
												<input type="text" class="form-control datepicker_mask" name="reception_date" id="reception_date" required="">
												<div class="input-group-prepend">
												  <span class="input-group-text"><i class="fa fa-calendar"></i></span>
												</div>
											</div>
											</div>									
										</div>
										
									</div>
									<div class="row">
										<div class="col-sm-8 col-md-8 col-lg-8">
											<div class="form-group">
												<label class="control-label" for="organization"><?php echo $this->lang->line("organization");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("organization");?>" name="organization" class="form-control" id="organization">
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4 img-container" rowspan="4">
											<div class="form-group">
												<label class="control-label" for="designation"><?php echo $this->lang->line("designation");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("designation");?>" name="designation" class="form-control" id="designation">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-8 col-md-8 col-lg-8">
											<div class="form-group required">
												<label class="control-label" for="mailing_address"><?php echo $this->lang->line("mailing_address");?></label>
												<input type="text" placeholder="<?php echo $this->lang->line("mailing_address");?>" name="mailing_address" class="form-control" id="mailing_address">
											</div>
										</div>
										<div class="col-sm-4 col-md-4 col-lg-4 img-container" rowspan="4">
											<div class="form-group required">
												<label class="control-label" for="mobile"><?php echo $this->lang->line("mobile");?></label>
												<input type="text" required="" placeholder="<?php echo $this->lang->line("mobile");?>" name="mobile" class="form-control" id="mobile">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-8 col-md-8 col-lg-8">
										 <div class="form-group">
											<label class="control-label" for="division"><?php echo $this->lang->line("division");?></label>
											<select name="division" id="division" class="chosen-select">
												<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("division");?></option>
										<?php foreach($dvquery->result() as $row){
											echo '<option value="'.$row->division_id.'">'.$row->division_name.'</option>';
										}
										?>
											</select>
										 </div>
										</div>
										
										<div class="col-sm-4 col-md-4 col-lg-4">
											<div class="form-group">
											<label class="control-label" for="district"><?php echo $this->lang->line("district");?></label>
											<select name="district" id="district" class="chosen-select">
    										<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("district")." ".$this->lang->line("name");?></option>
    										<?php foreach($dquery->result() as $row){
    											echo '<option value="'.$row->district_id.'">'.$row->district_name.'</option>';
    										}
    										?>
    										</select>
											</div>
										</div>
										
									</div>
									<div class="row">
										<div class="col-sm-8 col-md-8 col-lg-8">
										    
                                        <input type="hidden" name="institute_id" id="institute_id" value="1"> 
                                        <input type="hidden" name="branch_id" id="branch_id" value="1"> 
                                        <input type="hidden" name="guest_id" id="guest_id" value="">  
										</div>
									    <div class="col-sm-6 col-md-6 col-lg-6">
											
    										<div class="form-group">
    											<button type="submit" class="btn btn-success save-record"><i class="fa fa-save"></i> Save</button>
    										</div>
								        </div>
								    </div>
								</div> <!-- End Card Body-->
							</div> <!-- End Card -->
							
						
						</div> <!-- End Card -->			
					</div> <!-- End Card -->                     
					</div> <!-- End Card --> 

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
		                    <div class="col-sm-6 col-md-6 col-lg-6">
		                        <div class="form-group required">
		                            <label class="control-label" for="src-guest-name"><?php echo $this->lang->line("name_of_parties");?>:</label>
		                            <input type="text" placeholder="<?php echo $this->lang->line("name_of_parties");?>" name="src-guest-name" class="form-control" id="src-guest-name">
		                        </div>
		                    </div>
		                    <div class="col-sm-6 col-md-6 col-lg-6">
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
                            <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("case")." ".$this->lang->line("list");?></h3>
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
    
    $('#addnew').click(function() {
       $(".show-create").show();
    });
    
    jQuery('.datepicker_mask').datetimepicker({
         timepicker:false,
         mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
         format:'d/m/Y'
    });
	//jQuery('#datetimepicker').datetimepicker();	
	
    $(document).ready(function(){
        //Start Chosen Responsive//
        resizeChosen();	
	    $('#st-danger-alert').hide();	
	    $('#ref-danger-alert').hide();
        jQuery(window).on('resize', resizeChosen);
        $(".chosen-select").val('').trigger("chosen:updated");
        //End Chosen Responsive//
		//==== Start Temp Set =====		
	    $('#institute_id').val("1");
	    $('#branch_id').val("1");
		
        $('#btnDelete').click(function() {
            $('#deleteModal').modal('hide');
        });
        $('.show-create').hide();
        $('#alert-delete').hide();
        $('#alert').hide();
		
        //Load dataGrid
        reloadDataGrid();
    }); 
    
    /* Start Delete Data*/
    function deleteRecord(id){
        $('#guest_id').val(id);
        return false;
    }
	
    $('.confirm').click(function(){
    var delId = $('#guest_id').val();
    $('#guest_id').val("");
    if(delId!=""){
        $.ajax({
        type: 'POST',
        url: "<?php echo base_url();?>registration/DelRecord",
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
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>registration/GetRecord",
            success: function(option){
            $('#dataGrid').html(option);
            $('#alert-delete').hide(500);
            }//Success
        });// End datagrid
    }
	
 	$('.search').click(function(){
		var name_of_guest   = $('#src-guest-name').val();
		var srcmobile       = $('#src-mobile').val();
		if(name_of_guest !="" || srcmobile !=""){
		   $.ajax({
			type: 'POST',
			url: "<?php echo base_url();?>registration/GetRecord",
			data: "name_of_guest="+name_of_guest+"&mobile="+srcmobile,
            		success: function(option){
                		$('#dataGrid').html(option);
						$('#alert-delete').hide();
						$('.print').show();
            		}//Success
        	});// ajax
		}else{
		  $('#alert').hide(); 
		  $('#alert').html(''); $('.print').hide();
		  $('#alert-delete').show();
		  $('#alert-delete').html('Please fill data in required fields');		
	    }
        return false;
    });// End search
	
    function editRecord(id){
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>registration/FillRecord",
            data: "id="+id,
            success: function(option){
            //alert(option);
            rsStr = option.split("##&##");
            //alert(rsStr[1]);
            $('#guest_id').val(rsStr[0]);
            $('#institute_id').val(rsStr[1]);
            $('#branch_id').val(rsStr[2]);
            $('#name_of_guest').val(rsStr[3]);
            $('#reception_date').val(rsStr[4]);
            $('#organization').val(rsStr[5]);
            $('#designation').val(rsStr[6]);
            $('#mailing_address').val(rsStr[7]);
            $('#mobile').val(rsStr[8]);
            $('#division').val(rsStr[9]);
            $('#division').trigger("chosen:updated");
            $('#district').val(rsStr[10]);
            $('#district').trigger("chosen:updated");		
			
            $('.show-create').show();
            $('#alert').show();
            $('#alert').html('Ready to Edit!');
            }//Success
        });// ajax
        return false;
    }
    $('.addParty').click(function() {              
	    $('#prt-gender').val("Male").trigger("chosen:updated");
    });
    $('.addRef').click(function() {              
	    $('#ref-gender').val("Male").trigger("chosen:updated");
    });
    $('#reset').click(function(){
          $('#alert').hide();
          $('#guest_id').val("");
    });// End reset

    /* Pagination Next Page */
    function nextPage(frm, to, pno) {
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>registration/GetRecord",
            data: "from=" + frm + "&to=" + to + "&page_no=" + pno,
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
