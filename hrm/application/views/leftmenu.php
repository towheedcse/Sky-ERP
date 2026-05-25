<?php if($this->session->userdata('user_role') >0){?>
<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="<?php echo base_url().ASSETS?>/img/company/<?php echo $this->session->userdata('sm_logo');?>" alt="Brand Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> 
      <!-- 128X128 Logo -->
      <span class="brand-text font-weight-light"><?php echo $this->session->userdata('short_name');?></span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo base_url().ASSETS?>/img/no-avatar.png" class="img-circle elevation-2" alt="User Image"/>
        </div>
        <div class="info">
            <a href="#" class="d-block">
                <h4><?php $this->session->userdata('employee_name'); ?></h4>
                <h6><?php if($this->session->userdata('user_role')=='1'){ echo "Administrator";} ?></h6>
            </a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview menu-open">
            <a href="<?php echo base_url()?>dashboard/Userhome" class="nav-link active">
              <i class="nav-icon fa fa-dashboard"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
	  <?php
		$company_id = $this->session->userdata('company_id');
		$role_id    = $this->session->userdata('user_role');
		$MSQL="SELECT m.module_id,m.module_name,m.module_icon FROM ".MODULE_TBL." as m,".MODULE_PERMISSION_TBL." as mp WHERE m.module_id=mp.module_id AND mp.company_id =$company_id AND mp.role_id=$role_id AND m.module_status =1 GROUP BY m.module_id ORDER BY m.order_no ASC";
	  	$mquery = $this->db->query($MSQL);
		$mresult = $mquery->result();
		if($mquery->num_rows() >0){
			foreach($mresult as $mrow)
			{
			$module_id  = $mrow->module_id;				
			?>
			<li class="nav-item has-treeview">
			    <a href="#" class="nav-link">
			      <i class="nav-icon <?php echo $mrow->module_icon;?>"></i>
			      <p>
				<?php echo $mrow->module_name;?>
				<i class="right fas fa-angle-left"></i>
			      </p>
			    </a>
			    <?php			    
			    $MESQL="SELECT me.menu_id,me.menu_slug,me.menu_name FROM ".MENU_TBL." as me,".MENU_PERMISSION_TBL." as mep WHERE me.module_id=mep.module_id AND me.menu_id=mep.menu_id AND mep.company_id =$company_id AND mep.module_id=$module_id AND mep.role_id=$role_id AND me.menu_status =1 GROUP BY me.menu_id ORDER BY me.order_no ASC";
		  	    $mequery  = $this->db->query($MESQL);
			    $meresult  = $mequery->result();
			    if($mequery->num_rows() >0){
			    ?>
			    <ul class="nav nav-treeview">
			    <?php			
			     foreach( $meresult as $merow)
			     {
			    ?>			    
			      <li class="nav-item">
				<a href="<?php echo SERVER?>/<?php echo $merow->menu_slug;?>/" class="nav-link">
				  <i class="fa fa-circle-o nav-icon"></i>
				  <p><?php echo $merow->menu_name;?></p>
				</a>
			      </li>
			    <?php
			     } // end menu foreach
			    ?>
			    </ul>
			    <?php					
			    } // end menu if num row
			    ?>          
			  </li>
			<?php				
			} // end foreach
		} // end if num row
	  ?>

          <li class="nav-item" style="margin-top:10px; border-top:1px solid rgba(255,255,255,0.15); padding-top:10px;">
            <a href="<?php echo str_replace(ROOT_DIR, '', SERVER); ?>" class="nav-link" style="color:#f8d7a0;">
              <i class="nav-icon fas fa-arrow-left"></i>
              <p>Back to ERP</p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside> 
<?php }?>
