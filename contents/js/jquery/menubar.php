<div id="menubar" align="center">
						
							<div id="menubar_container">
							
									<div id="tabs1">
												<ul>
												<!-- CSS Tabs -->
													
														<li <?php if($_GET['page']==base64_encode('home')) echo 'id="current"'; ?>><a href="index.php?<?php echo 'page='.base64_encode('home');  ?>"><span><?=HOME?></span></a></li>
														<?php if(!empty($_SESSION['username'])){ ?>
														<li <?php if($_GET['page']==base64_encode('myprofile')) echo 'id="current"'; ?>><a href="myprofile.php?<?php echo 'page='.base64_encode('myprofile');  ?>"><span><?=PROFILE?></span></a></li>
														
																												
														
														<li <?php if($_GET['page']==base64_encode('postarticle')) echo 'id="current"'; ?>><a href="postarticles.php?<?php echo 'page='.base64_encode('postarticle');  ?>"><span><?=POSTARTICLE_AND_OTHERS?></span></a></li>
														
														<li <?php if($_GET['page']==base64_encode('research')) echo 'id="current"'; ?>><a href="researchontopic.php?<?php echo 'page='.base64_encode('research');  ?>"><span><?=RESEARCH?></span></a></li>
																										
														<li <?php if($_GET['page']==base64_encode('mygroup')) echo 'id="current"'; ?>><a href="mygroup.php?<?php echo 'page='.base64_encode('mygroup');  ?>"><span><?=GROUP?></span></a></li>
														<?php } ?>

														<li <?php if($_GET['page']==base64_encode('jobs')) echo 'id="current"'; ?>><a href="jobs.php?<?php echo 'page='.base64_encode('jobs');  ?>"><span><?=JOB?></span></a></li>
												
												</ul>
							</div>					
									</div>
						</div>