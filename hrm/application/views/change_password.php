<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Change Password</title>
<?php require('csslinks4admin.php');?>
</head>
<body>

<div id="container">
	<?php require('adminheader.php');?>
	<?php require('leftmenu.php');?>
	<div id="content"><!-- Start content -->
	    <br>
	    <div class="container-fluid">
            <div class="panel panel-primary">
                <div class="panel-heading"><i class="fa fa-key"></i> Change Password</div>
                    <div class="panel-body">
                        <div id="alertDanger" class="alert alert-danger"></div>
                        <div id="alert" class="alert alert-success"></div>
                        <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-2 col-md-2 col-lg-2" for="user-name">User Name:</label>
                            <div class="col-sm-10 col-md-10 col-lg-10">
                                <input type="text" required="" placeholder="User Name" name="user-name" class="form-control" value="<?php echo $user_id;?>" readonly>
                            </div>
                        </div>
						<div class="clearfix"></div>
                        <div class="form-group required">
                            <label class="col-sm-2 col-md-2 col-lg-2 control-label" for="old-password">Old Password:</label>
                            <div class="col-sm-10 col-md-10 col-lg-10">
                                <input type="password" required="" placeholder="Old Password" name="old-password" class="form-control" id="old-password">
                            </div>
                        </div>
						<div class="clearfix"></div>
                        <div class="form-group required">
                            <label class="col-sm-2 col-md-2 col-lg-2 control-label" for="user-password">New Password:</label>
                            <div class="col-sm-10 col-md-10 col-lg-10">
                                <input type="password" required="" placeholder="New Password" name="user-password" class="form-control" id="user-password">
                            </div>
                        </div>
						<div class="clearfix"></div>
                        <div class="form-group required">
                            <label class="col-sm-2 col-md-2 col-lg-2 control-label" for="conf-password">Confirm Password:</label>
                            <div class="col-sm-10 col-md-10 col-lg-10">
                                <input type="password" required="" placeholder="Confirm Password" name="conf-password" class="form-control" onKeyUp="chkConfPass()" id="conf-password">
                            </div>
                        </div>
						<div class="clearfix"></div>
                        <div class="form-group required">
                            <label class="col-sm-2 col-md-2 col-lg-2"></label>
                            <div class="col-sm-10 col-md-10 col-lg-10">
                                <input type="hidden" name="u-id" class="form-control" id="u-id" value="<?php echo $id;?>">
                                <input type="hidden" name="isvalid" class="form-control" id="isvalid">
                                <button type="button" class="btn btn-sm btn-success save"><i class="fa fa-save"> Save</i></button>
                                <button type="reset" id="reset" class="btn btn-sm btn-warning"><i class="fa fa-refresh"> Clear</i></button>
                            </div>
                        </div>
                    </form>
                    </div>
            </div>
	    </div><!-- End container-fluid -->
	</div><!--End Content-->
</div>
<?php require('adminfooter.php');?>
<?php require('jslinks4admin.php');?>

 <script>
 function chkConfPass(){
	 if($('#u-id').val()!=""){
		 var new_password	= $('#user-password').val(); 
		 var conf_password  = $('#conf-password').val();
		 if(conf_password.length >= new_password.length){
			 if(new_password==conf_password){
				$('#alertDanger').hide();
				$('#alert').show();
				$('#alert').html("Your new password & confirm password is equal");
				$('#isvalid').val("1");
			}else{
				$('#alert').hide();
				$('#alertDanger').show();
				$('#alertDanger').html("Your new password & confirm password is not equal");
				$('#isvalid').val("0");
			}
		 }
	 }
	return false;
 }
 
    $(document).ready(function(){
			
		$('#reset').click(function(){
			$('#alert').hide(); $('#alertDanger').hide();
		});// End reset	
				
		$('#alert').hide(); $('#alertDanger').hide();
		$('.save').click(function(){
		var old_password= $('#old-password').val(); var usserPassword= $('#user-password').val(); var isvalid= $('#isvalid').val(); 
		var uId= $('#u-id').val();
		//alert(employeeName+employeeSlug);
		if(usserPassword!="" && uId !="" && isvalid==1){
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url();?>users/ChangePassword",
				data: "old-password="+old_password+"&user-password="+usserPassword+"&u-id="+uId,
				success: function(option){
					//alert(option);
					if(option=="The old password is not valid"){
						$('#alertDanger').show();
						$('#alertDanger').html('The old password is not valid. Please try again!!!');
					}else{
						$('#old-password').val("");
						$('#user-password').val("");
						$('#conf-password').val("");
						$('#isvalid').val("0");
						$('#alertDanger').hide();
						$('#alert').show();
						$('#alert').html('Successfully changed your password');
					}
				}//Success
				
			});// ajax
		}// End if
		return false;		
		});// End save	
		
	});
</script>
</body>
</html>