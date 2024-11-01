<?php /** @version $Id: view-default.php 913 2011-08-10 08:00:57Z xagero $ */?>
<div id="thethefly">
  <div class="wrap">
    <h2 id="thethefly-panel-title"> <span id="thethefly-panel-icon" class="icon48">&nbsp;</span><?php print $this->_config['meta']['Name'];?></h2>
    <div id="thethefly-panel-frame">
      <div id="menu-management-liquid">
        <div id="menu-management"> 
          <!-- tabs -->
          <div class="nav-tabs-wrapper">
            <div class="nav-tabs">
<?php
$view = $this->getCurrentViewIndex();	
if ($view == 'overview') echo "<span class='nav-tab nav-tab-active'>Overview</span>"; else echo "<a href='".$this->getTabURL('overview')."' class='nav-tab hide-if-no-js'>Overview</a>"; 
if ($view == 'settings') echo "<span class='nav-tab nav-tab-active'>Settings</span>"; else echo "<a href='".$this->getTabURL('settings')."' class='nav-tab hide-if-no-js'>Settings</a>"; 
if ($view == 'style') echo "<span class='nav-tab nav-tab-active'>Style</span>"; else echo "<a href='".$this->getTabURL('style')."' class='nav-tab hide-if-no-js'>Style</a>"; 
?>
            </div>
          </div>
          <!-- /tabs -->
          <div class='menu-edit tab-overview'>
            <div id='nav-menu-header'>
              <div class='major-publishing-actions'> <span><?php print $this->viewIndexAll[$view]['title']; ?></span>
                <div class="sep">&nbsp;</div>
              </div>
              <!-- END .major-publishing-actions --> 
            </div>
            <!-- END #nav-menu-header -->
            <div id='post-body'>
              <div id='post-body-content'>
                <?php
					if(in_array($view, array('overview'))){
						include 'view-tab-'.$view.'.php';
					} else {
					?>
                <form method="post" action="">
                  <?php
					include 'inc.submit-buttons.php';				  
					include 'view-tab-'.$view.'.php';
					include 'inc.submit-buttons.php';
				  ?>
                </form>
                <?php } ?>
              </div>
              <!-- /#post-body-content --> 
            </div>
            <!-- /#post-body -->
            <div id="nav-menu-footer">
              <div class="major-publishing-actions">&nbsp;</div>
            </div>             
          </div>
          <!-- /.menu-edit --> 
        </div>
      </div>
      <!-- sidebar -->
      <div id="thethefly-admin-sidebar" class="metabox-holder">
        <div id="side-sortables" class="meta-box-sortables">
          <?php include 'inc.sidebar.donate.php';?>
          <?php include 'inc.sidebar.newsletter.php';?>
          <?php include 'inc.sidebar.themes.php';?>
          <?php include 'inc.sidebar.plugins.php';?>
          <?php include 'inc.sidebar.help.php';?>
        </div>
      </div>
      <!-- /sidebar -->
      <div class="clear"></div>
    </div>
  </div>
</div> 