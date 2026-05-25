    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">
            <span class="text-info">HRM Management System</span>
          </a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">      
        <li class="nav-item">
          <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
              class="fa fa-th-large"></i>
          </a>
        </li>
	
        <!-- Language Dropdown Menu -->
        <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fa fa-language"></i>
          <span class="badge badge-warning navbar-badge">
		<?php 
		if($this->session->userdata('language')){
		echo $this->session->userdata('language');
		}else{
		echo "en";
		}
		?>
	  </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          
          <a href="<?php echo base_url()?>langswitch/switchLanguage/en" class="dropdown-item">
            <i class="fa fa-users mr-2"></i> Englih
          </a>
          <div class="dropdown-divider"></div>
          <a href="<?php echo base_url()?>langswitch/switchLanguage/bn" class="dropdown-item">
            <i class="fa fa-file mr-2"></i> Bangla
          </a>
        </div>
        </li>

        <li class="nav-item">
           <a class="nav-link" data-slide="true" href="<?php echo base_url()?>dashboard/Logout">
              <span class="hidden-xs hidden-sm hidden-md">Logout</span>
              <i class="fas fa-sign-out-alt fa-lg"></i>
            </a>
        </li>
      </ul>
    </nav>
    <!-- /.navbar -->

<!-- Fixed navbar -->
<!--header id="header" class="navbar navbar-static-top">
    <div class="navbar-header">
        <a id="button-menu" class="pull-left" type="button">
            <i class="fa fa-outdent fa-lg"></i>
        </a>
        <a href="#" class="navbar-brand"><img src="<?php echo base_url().ASSETS?>/img/logo100x53.png" height="29" alt="Demo" title="Demo" />
            <span class="text-info"> Demo</span>
        </a>
    </div>
    <ul class="nav pull-right">
        <li>
            <a href="<?=SERVER?>/dashboard/Logout">
                <span class="hidden-xs hidden-sm hidden-md">Logout</span>
                <i class="fas fa-sign-out-alt fa-lg"></i>
            </a>
        </li>
    </ul>
</header>
<!--Page Loader->
<div id="preloader">
    <div id="preloader_status"><i class="fas fa-spinner fa-3x fa-pulse"></i></div>
</div>
<!--Page Loader-->

<!--header class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <div><a class="navbar-brand" href="#"><img src="../assets/img/logo100x53.png" height="45" /></a></div> 
	</div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
      <?php $user_role	=$this->session->userdata('user_role');?>
       <?php if(!$this->session->userdata('validate')){?>
        <li class="active">
        <a href="<?=SERVER?>/dashboard/Userhome"><span class="glyphicon glyphicon-home"></span> Home</a></li>
       <?php }else{?>
        <li class="active">
        <?php if($user_role==101){?>
        <a href="<?=SERVER?>/dashboard/Userhome"><span class="glyphicon glyphicon-home"></span> Home</a></li>
        <?php }elseif($user_role==102){?>
        <a href="<?=SERVER?>/dashboard/SalesHome"><span class="glyphicon glyphicon-home"></span> Home</a></li>
        <?php }?>
       <? }?>       
        
        
        <?php if($this->session->userdata('validate')){?>
        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-paperclip"></span> Quick Actions <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
           <? if($this->session->userdata('user_role')=='101'){?>
            <li><a href="<?php echo SERVER?>/employee">Employee</a></li>
            <li><a href="<?php echo SERVER?>/brand">Brand</a></li>  
            <li><a href="<?php echo SERVER?>/category">Catagory</a></li>
            <li><a href="<?php echo SERVER?>/subcategory">Sub Catagory</a></li>
            <li><a href="<?php echo SERVER?>/product/">Product</a></li>            
            <li><a href="<?php echo SERVER?>/sales/AddSales">Existing Customer</a></li>             
			<? }elseif($this->session->userdata('user_role')=='102'){?>                           
            <li><a href="<?php echo SERVER?>/complain/WarrantyStatus">Warranty Status</a></li>
            <? }?>
            <li><a href="<?php echo SERVER?>/complain/ServiceStatus">Service Status</a></li>
            
          </ul>
        </li>
       <? }?>
      </ul>
      <ul class="nav navbar-nav navbar-right">
      <?php if(!$this->session->userdata('validate')){?>
        <li class="active"><a href="<?=SERVER?>/dashboard/Login"><span class="glyphicon glyphicon-user"></span> Sign in</a></li>
       <?php }else{?>
        <li class="active"><a href="<?=SERVER?>/dashboard/Logout"><span class="glyphicon glyphicon-user"></span> Sign out</a></li>
       <? }?>
      </ul>
    </div>
    <!--/.nav-collapse --> 
  <!--/div>
</header-->
