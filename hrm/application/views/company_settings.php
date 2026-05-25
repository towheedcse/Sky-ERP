<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title><?php echo $this->session->userdata('short_name');?>::<?php echo $this->lang->line("company")." ".$this->lang->line("setup");?></title>
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
	 	<div class="container-fluid"> <!-- Start container-fluid -->
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark"><?php echo $this->lang->line("company");?> 
		    <?php if($hasCreateOption){?>
		    <button type="button" class="btn btn-tool" id="addnew"><i class="fa fa-plus"></i></button>
		    <?php }?>
		</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="<?php echo SERVER?>/dashboard/Userhome"><?php echo $this->lang->line("home");?></a></li>
                  <li class="breadcrumb-item active"><?php echo $this->lang->line("company");?></li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div><!-- /.content-header -->
    
	<div id="content"> <!-- Start content -->
        <div class="container-fluid"> <!-- Start container-fluid -->
            <div class="card card-primary show-create">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa fa-pen-square"></i> <?php echo $this->lang->line("new");?> <?php echo $this->lang->line("company");?></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div> <!-- /.card-header -->
            <div class="card-body">
                <div id="alert" class="alert alert-success"></div>
                <form method="POST" id="#company" action="<?php echo SERVER?>/company_settings/saveRecord" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="company_name" class="control-label"><?php echo $this->lang->line("company_name");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("company_name");?>" name="company_name" class="form-control" id="company_name">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="address" class="control-label"><?php echo $this->lang->line("address");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("address");?>" name="address" class="form-control" id="address">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="phone" class="control-label"><?php echo $this->lang->line("phone");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("phone");?>" name="phone" class="form-control" id="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="mobile" class="control-label"><?php echo $this->lang->line("mobile");?></label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("mobile");?>" name="mobile" class="form-control" id="mobile">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="email" class="control-label"><?php echo $this->lang->line("email");?>:</label>
                                <input type="email" placeholder="<?php echo $this->lang->line("email");?>" name="email" class="form-control" id="email">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="site_url" class="control-label"><?php echo $this->lang->line("site_url");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("site_url");?>" name="site_url" class="form-control" id="site_url">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="ssl_url" class="control-label"><?php echo $this->lang->line("ssl_url");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("ssl_url");?>" name="ssl_url" class="form-control" id="ssl_url">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="backend_title" class="control-label"><?php echo $this->lang->line("backend_title");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("backend_title");?>" name="backend_title" class="form-control" id="backend_title">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="frontend_title" class="control-label"><?php echo $this->lang->line("frontend_title");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("frontend_title");?>" name="frontend_title" class="form-control" id="frontend_title">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                                <label for="short_title" class="control-label"><?php echo $this->lang->line("short_title");?>:</label>
                                <input type="text" required="" placeholder="<?php echo $this->lang->line("short_title");?>" name="short_title" class="form-control" id="short_title">
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">							
                            <div class="form-group required">
                            <label class="control-label" for="sm_logo"><?php echo $this->lang->line("small_logo");?>:</label>
                            <input type="file" placeholder="<?php echo $this->lang->line("small_logo");?>" name="sm_logo" class="form-control" id="sm_logo">
							</div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group required">
                            <label class="control-label" for="md_logo"><?php echo $this->lang->line("large_logo");?>:</label>
                            <input type="file" placeholder="<?php echo $this->lang->line("large_logo");?>" name="md_logo" class="form-control" id="md_logo">
							</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="copyright" class="control-label"><?php echo $this->lang->line("copyright");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("copyright");?>" name="copyright" class="form-control" id="copyright">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="keywords" class="control-label"><?php echo $this->lang->line("keywords");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("keywords");?>" name="keywords" class="form-control" id="keywords">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="meta_description" class="control-label"><?php echo $this->lang->line("meta_description");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("meta_description");?>" name="meta_description" class="form-control" id="meta_description">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="currency_sign" class="control-label"><?php echo $this->lang->line("currency_sign");?>:</label>
                            <input type="text" required="" placeholder="<?php echo $this->lang->line("currency_sign");?>" name="currency_sign" class="form-control" id="currency_sign">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="currency_code" class="control-label"><?php echo $this->lang->line("currency_code");?>:</label>
                            <input type="text" required="" placeholder="<?php echo $this->lang->line("currency_code");?>" name="currency_code" class="form-control" id="currency_code">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="default_language" class="control-label"><?php echo $this->lang->line("default_language");?>:</label>
                            <input type="text" required="" placeholder="<?php echo $this->lang->line("default_language");?>" name="default_language" class="form-control" id="default_language">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="license_key" class="control-label"><?php echo $this->lang->line("license_key");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("license_key");?>" name="license_key" class="form-control" id="license_key">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="secret_key" class="control-label"><?php echo $this->lang->line("secret_key");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("secret_key");?>" name="secret_key" class="form-control" id="secret_key">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="site_offline" class="control-label"><?php echo $this->lang->line("site_offline");?>:</label>
                            <select name="site_offline" id="site_offline" class="form-control chosen-select"  placeholder="<?php echo $this->lang->line("site_offline");?>">
                                <option value=""><?php echo $this->lang->line("select");?></option>
                                <option value="0"><?php echo $this->lang->line("no");?></option>
                                <option value="1"><?php echo $this->lang->line("yes");?></option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="offline_msg" class="control-label"><?php echo $this->lang->line("offline_message");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("offline_message");?>" name="offline_msg" class="form-control" id="offline_msg">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="allow_registration" class="control-label"><?php echo $this->lang->line("allow_registration");?>:</label>
                            <select name="allow_registration" id="allow_registration" class="form-control chosen-select"  placeholder="<?php echo $this->lang->line("allow_registration");?>">
                                <option value=""><?php echo $this->lang->line("select");?></option>
                                <option value="0"><?php echo $this->lang->line("no");?></option>
                                <option value="1"><?php echo $this->lang->line("yes");?></option>
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="booking_cancellation" class="control-label"><?php echo $this->lang->line("booking_cancellation");?>:</label>
                            <select name="booking_cancellation" id="booking_cancellation" class="form-control chosen-select" placeholder="<?php echo $this->lang->line("booking_cancellation");?>">
                                <option value=""><?php echo $this->lang->line("select");?></option>
                                <option value="0"><?php echo $this->lang->line("no");?></option>
                                <option value="1"><?php echo $this->lang->line("yes");?></option>
                            </select>
                            <input type="hidden" name="admission_head" id="admission_head" value="0">
                            <input type="hidden" name="fullscholarship_head" id="fullscholarship_head" value="0">
                            <input type="hidden" name="partialscholarship_head" id="partialscholarship_head" value="0">
                            <input type="hidden" name="discount_head" id="discount_head" value="0">
                            <input type="hidden" name="concession_hreads" id="concession_hreads" value="0">
                            <input type="hidden" name="absent_head" id="absent_head" value="0">
                            <input type="hidden" name="late_payment_head" id="late_payment_head" value="0">
                            <input type="hidden" name="due_payment_head" id="due_payment_head" value="0">
                            <input type="hidden" name="defaulter_head" id="defaulter_head" value="0">
                        </div>
                    </div>
                    <div class="row">
			            <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="default_shift" class="control-label"><?php echo $this->lang->line("default")." ".$this->lang->line("shift_name");?>:</label>
                            <select name="default_shift" id="default_shift" class="form-control chosen-select" placeholder="<?php echo $this->lang->line("shift_name");?>">
                                <option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("shift_name");?></option>
                                <?php foreach($shquery->result() as $row){
									echo '<option value="'.$row->shift_id.'">'.$row->shift_name.'</option>';
								}
								?>
                            </select>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
							<div class="form-group required">
								<label for="weekend" class="control-label"><?php echo $this->lang->line("weekend");?>:</label>
								<select name="weekend[]" id="weekend" required="" class="form-control chosen-select" multiple placeholder="<?php echo $this->lang->line("weekend");?>">
									<option value=""><?php echo $this->lang->line("select")." ".$this->lang->line("weekend");?></option>
									<option value="Friday">Friday</option>
									<option value="Saturday">Saturday</option>
									<option value="Sunday">Sunday</option>
									<option value="Monday">Monday</option>
									<option value="Tuesday">Tuesday</option>
									<option value="Wednesday">Wednesday</option>
									<option value="Thursday">Thursday</option>
								</select>
							</div>
						</div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <label for="one_day_deduction" class="control-label"><?php echo $this->lang->line("one_day_deduction");?>:</label>
                            <input type="text" placeholder="<?php echo $this->lang->line("one_day_deduction");?>" name="one_day_deduction" class="form-control" id="one_day_deduction">
                        </div>
                    </div>
                    <div class="row">
        		    	<div class="col-sm-2 col-md-2 col-lg-2">
                            <br>
                            <button type="submit" style="margin-top:2px" class="btn btn-md btn-block btn-success" name="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("save");?></button>
                        </div>
                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <br>
                            <button type="reset" style="margin-top:2px" id="reset" class="btn btn-md btn-block btn-warning"><i class="fas fa-sync-alt"></i> <?php echo $this->lang->line("clear");?></button>
                        </div>
                        <input type="hidden" name="company_id" class="form-control" id="company_id">
                    </div>
		    
                    <!--button type="button" class="btn btn-sm btn-success save"><span class="glyphicon glyphicon-saved"></span> Save </button-->
                </form>
    	    </div> <!-- End Card Body-->
        </div> <!-- End Card -->
	
	<?php if($hasViewOption){?>
        <div class="card box-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-list"></i> <?php echo $this->lang->line("company");?> <?php echo $this->lang->line("list");?></h3>
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
    <script type="text/javascript">
        $(function() {
            $('.chosen-select').chosen();
            $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
        });

        function resizeChosen() {
            $(".chosen-container").each(function() {
            $(this).attr('style', 'width: 100%');
            });
        }
	$('#addnew').click(function() {
	   $(".show-create").show();
        });
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

    $(document).ready(function(){
        //select2 Call//
        //$('#site_offline').select2();
        //$('#allow_registration').select2();
        //$('#booking_cancellation').select2();
	
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
            url: "<?php echo base_url();?>company_settings/GetRecord",
            success: function(option){
                $('#dataGrid').html(option);
            }//Success
        });// End datagrid

        $('#reset').click(function(){
            $('#alert').hide();
            $('#company_id').val("");
        });// End reset
    });

    /* Start Delete Data*/
    function deleteRecord(id){
        $('#company_id').val(id);
        return false;
    }

    $('.confirm').click(function(){
        var delId = $('#company_id').val();
        $('#company_id').val("");
        if(delId!=""){
            $.ajax({
                type: 'POST',
                url: "<?php echo base_url();?>company_settings/DelRecord",
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
            url: "<?php echo base_url();?>company_settings/GetRecord",
            success: function(option){
                $('#dataGrid').html(option);
                $('#alert-delete').hide(5000);
            }//Success
        });// End datagrid
    }

    function myFunction(id){
	   $.ajax({
		  type: 'POST',
		  url: "<?php echo base_url();?>company_settings/FillRecord",
		  data: "id="+id,
		  success: function(option){
			 //alert(option);
			 rsStr = option.split("##&##");
			 //alert(rsStr[1]);
			 $('#company_name').val(rsStr[1]);
			 $('#address').val(rsStr[2]);
			 $('#phone').val(rsStr[3]);
			 $('#mobile').val(rsStr[4]);
			 $('#email').val(rsStr[5]);
			 $('#site_url').val(rsStr[6]);
			 $('#ssl_url').val(rsStr[7]);
			 $('#backend_title').val(rsStr[8]);
			 $('#frontend_title').val(rsStr[9]);
			 $('#short_title').val(rsStr[10]);
			 $('#copyright').val(rsStr[11]);
			 $('#keywords').val(rsStr[12]);
			 $('#meta_description').val(rsStr[13]);
			 $('#currency_sign').val(rsStr[14]);
			 $('#currency_code').val(rsStr[15]);
			 $('#default_language').val(rsStr[16]);
			 $('#license_key').val(rsStr[17]);
			 $('#secret_key').val(rsStr[18]);
			 $('#site_offline').val(rsStr[19]).trigger("chosen:updated");
			 $('#offline_message').val(rsStr[20]);
			 $('#allow_registration').val(rsStr[21]);
			 $('#allow_registration').trigger("chosen:updated");
			 $('#booking_cancellation').val(rsStr[22]).trigger("chosen:updated");
			 $('#default_shift').val(rsStr[32]);
             $('#default_shift').trigger("chosen:updated");
			 
			 var weekend =rsStr[33];
			 var weekend = weekend.split(',');
			 $('#weekend').val(weekend).trigger("chosen:updated");
			 $('#one_day_deduction').val(rsStr[34]);
			 /**/
			 $('#company_id').val(rsStr[0]); //alert(rsStr[0]);
			 $('.show-create').show();
			 $('#alert').show();
			 $('#alert').html('Ready to Edit!');
		  }//Success

	   });// ajax
	   return false;
    }
    
    function nextPage(frm,to,pno){
        //alert("f-"+frm+",t-"+to+"p-"+pno);
        $.ajax({
            type: 'POST',
            url: "<?php echo base_url();?>company_settings/GetRecord",
            data: "from="+frm+"&to="+to+"&page_no="+pno,
            success: function(option){
                $('#dataGrid').html(option);
            }//Success
        });// End datagrid
        return false;
    }
</script>
</body>
</html>