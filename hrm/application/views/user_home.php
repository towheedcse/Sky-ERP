<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $this->session->userdata('short_name');?>::Dashboard</title>
    <?php require('csslinks4admin.php');?>
	<?php				
	$institute_id 	=$this->session->userdata('company_id');
	$attendance_date= date("Y-m-d");
	?>
	<style>.small-box h3 {font-size: 24px;}</style>
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
				<h1 class="m-0 text-dark">Dashboard</h1>
			  </div><!-- /.col -->
			  <div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				  <li class="breadcrumb-item"><a href="#">Home</a></li>
				  <li class="breadcrumb-item active">Dashboard</li>
				</ol>
			  </div><!-- /.col -->
			</div><!-- /.row -->
		  </div><!-- /.container-fluid -->
		</div>
		<!-- /.content-header -->
		
	<!-- Main content -->
	<?php if($this->session->userdata('user_role') < 4){?>
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-4 col-4">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
				<?php
				  if(empty($institute_id)){ $institute_id = $this->session->userdata('company_id'); }
				  if(empty($institute_id)){ $institute_id = 1; } // bridged default company (P0005)
				  $CSQL = "SELECT * FROM ".EMPLOYEE_TBL." WHERE company_id='".$institute_id."' AND status=1 ORDER BY `employee_id` ASC";
				  $cquery =$this->db->query($CSQL);
				  if($cquery->num_rows() >=0){ 
				?>
                <h3><?php echo $cquery->num_rows();?></h3>
				<?php }?>
                <p>Staff & Employee</p>
              </div>
              <div class="icon">
				<i class="far fa-address-card"></i>
              </div>
              <a href="<?php echo SERVER?>/employee" class="small-box-footer">Details <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div> 
          <div class="col-lg-4 col-md-4">
            <div class="info-box bg-success-gradient">
              <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
              <?php
				  $PRESQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE institute_id=$institute_id AND attendance_date='$attendance_date' AND day_type=1 AND present=1";
				  $Prequery =$this->db->query($PRESQL);
				  $TotalEmp=0; $TotalPresent=0;
			  ?>
              <div class="info-box-content">
                <span class="info-box-text">Present</span>
                <span class="info-box-number">
                  <?php 
                  $TotalEmp = $cquery->num_rows();
				  if($Prequery->num_rows() >=0){ 
				  $TotalPresent = $Prequery->num_rows();   
				  echo $Prequery->num_rows();
				  }else{ echo "0"; $TotalPresent =0;}
				  ?>
				  
                </span>

                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  Today's Present
                </span>
                <br>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>  
          <div class="col-lg-4 col-md-4">
           <div class="info-box bg-danger-gradient">
              <span class="info-box-icon"><i class="fas fa-user-times"></i></span>
              <?php
                  $TotalLeave=0;
				  $LESQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE institute_id=$institute_id AND attendance_date='$attendance_date' AND day_type=4 AND present=0";
				  $lequery =$this->db->query($LESQL);
			      if($lequery->num_rows() >=0){ $TotalLeave=$lequery->num_rows();}
                  $TotalOD=0;
				  $ODSQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE institute_id=$institute_id AND attendance_date='$attendance_date' AND day_type=6 AND present=1";
				  $odquery =$this->db->query($ODSQL);
				  if($odquery->num_rows() >=0){ $TotalOD=$odquery->num_rows();}
				?>
              <div class="info-box-content">
                <span class="info-box-text">Absent</span>
                <span class="info-box-number">
                <?php
				  if($TotalPresent >=0){  
				  echo ($TotalEmp-($TotalPresent+$TotalOD+$TotalLeave));
				  }
				  ?>
                </span>

                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  Today's Absent
                </span>
                <br>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div> 
        </div> 
        <!-- /.card --> 
        
        
        <!-- 2nd Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-4 col-4">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
				<?php
				  $ODSQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE institute_id=$institute_id AND attendance_date='$attendance_date' AND day_type=6 AND present=1";
				  $odquery =$this->db->query($ODSQL);
				  if($odquery->num_rows() >=0){ 
				?>
                <h3><?php echo $odquery->num_rows();?></h3>
				<?php }?>
                <p>Today's OD</p>
              </div>
              <div class="icon">
				<i class="far fa-address-card"></i>
              </div>
              <a href="<?php echo SERVER?>/outstationduty" class="small-box-footer">Details <i class="fa fa-arrow-circle-right"></i></a>
            </div>
          </div> 
          <div class="col-lg-4 col-md-4">
            <div class="info-box bg-success-gradient">
              <span class="info-box-icon"><i class="fas fa-user-check"></i></span>
              <?php
				  $LATSQL = "SELECT * FROM ".ATTENDANCE_TBL." WHERE institute_id=$institute_id AND attendance_date='$attendance_date' AND day_type=1 AND late=1 AND present=1";
				  $ltquery =$this->db->query($LATSQL);
			  ?>
                
              <div class="info-box-content">
                <span class="info-box-text">Late</span>
                <span class="info-box-number">
                  <?php 
				  if($ltquery->num_rows() >=0){ 
				  echo $ltquery->num_rows();
				  }else{ echo "0";}
				  ?>
                </span>

                <div class="progress">
                  <div class="progress-bar" style="width: 100%"></div>
                </div>
                <span class="progress-description">
                  Today's Late
                </span>
                <br>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>  
          <div class="col-lg-4 col-md-4">
           <div class="info-box bg-danger-gradient">
              <span class="info-box-icon"><i class="fas fa-user-times"></i></span>
              
              <div class="info-box-content">
                <span class="info-box-text">Leave</span>
                <span class="info-box-number">
                   
                  <?php 
				  echo $TotalLeave;
				  ?> 
                </span>

                <div class="progress">
                  <div class="progress-bar" style="width: 70%"></div>
                </div>
                <span class="progress-description">
                  Today's Leave
                </span>
                <br>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div> 
        </div> 
        <!-- /.card --> 
      </div><!-- /.container-fluid -->
    </section>
	<?php }else{?>
	<section class="content">
      <div class="container-fluid">
        
	  </div>
					
    </section>
	<?php }?>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php require('jslinks4admin.php');?>
	
<script>
	 // THE SCRIPT PART
	var createResponseData ="";
    var scriptLink = "https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js";
	$.getScript(scriptLink);
	let paymentID;

	let createCheckoutUrl = 'https://merchantserver.sandbox.bka.sh/api/checkout/v1.2.0-beta/payment/create';
	let executeCheckoutUrl = 'https://merchantserver.sandbox.bka.sh/api/checkout/v1.2.0-beta/payment/execute';

	$(document).ready(function () {
		//initBkash();
	});

	function initBkash() {
		bKash.init({
		  paymentMode: 'checkout', // Performs a single checkout.
		  paymentRequest: {"amount": '85.50', "intent": 'sale'},

		  createRequest: function (request) {
			$.ajax({
			  url: createCheckoutUrl,
			  type: 'POST',
			  contentType: 'application/json',
			  data: JSON.stringify(request),
			  success: function (data) {
				  
				if (data && data.paymentID != null) {
				  paymentID = data.paymentID;
				  bKash.create().onSuccess(data);
				} 
				else {
				  bKash.create().onError(); // Run clean up code
				  alert(data.errorMessage + " Tag should be 2 digit, Length should be 2 digit, Value should be number of character mention in Length, ex. MI041234 , supported tags are MI, MW, RF");
				}

			  },
			  error: function () {
				bKash.create().onError(); // Run clean up code
				alert(data.errorMessage);
			  }
			});
		  },
		  executeRequestOnAuthorization: function () {
			$.ajax({
			  url: executeCheckoutUrl,
			  type: 'POST',
			  contentType: 'application/json',
			  data: JSON.stringify({"paymentID": paymentID}),
			  success: function (data) {

				if (data && data.paymentID != null) {
				  // On success, perform your desired action
				  alert('[SUCCESS] data : ' + JSON.stringify(data));
				  window.location.href = "/mevsc/billing/ViewBillForm/7/7/"+data.paymentID+"/TrxID/"+data.trxID;

				} else {
				  alert('[ERROR] data : ' + JSON.stringify(data));
				  bKash.execute().onError();//run clean up code
				}

			  },
			  error: function () {
				alert('An alert has occurred during execute');
				bKash.execute().onError(); // Run clean up code
			  }
			});
		  },
		  onClose: function () {
			alert('User has clicked the close button');
		  }
		});

		$('#bKash_button').removeAttr('disabled');

	}
	</script>
</body>
</html>
