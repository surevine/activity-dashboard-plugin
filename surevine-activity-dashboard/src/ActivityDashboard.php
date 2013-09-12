<?php
/**
* Activity Dashboard Plugin 
*
* @author Lloyd Watkin <lloyd.watkin@surevine.com>
* @author Jonny Heavey <jonny.heavey@surevine.com>
* @since  2013/09/01
*/
class ActivityDashboard
{
	/**
	 * Database connection
	 * 
	 * @var WPDB
	 */
	protected $_database;
	
	/**
	 * File path
	 * 
	 * @var string
	 */
	protected $_filePath;
  
  /**
   * Number of activities to display on dashboard initially
   *
   * @var int
   */
  // TODO make this configurable
  protected $initialActivityCount = 20;
  
  /**
   * Number of activities to fetch per 'page' of dashboard
   *
   * @var int
   */
  // TODO make this configurable  
  protected $activitiesPerPage = 10;

  /**
   * Instantiate dashboard
   */
  public function __construct(DashboardDatabaseInterface $database, $filePath)
  {
      $this->_database = $database;
      $this->_filePath = $filePath;
      
      // Setup template engine
      Mustache_Autoloader::register();
  }
  
  /**
   * Setup admin options page
   */
  public function adminMenu()
  {
      add_options_page(
          'Activity Dashboard Setup',
          'Activity Dashboard Setup',
          'administrator',
          'surevine-activity-dashboard',
          array($this, 'optionsPage')
      );
  }
  
  /**
   * Render admin options page
   */
  public function optionsPage()
  {
  	$this->render('/views/options-page.phtml');
  }
  
  /**
   * Render dashboard
   */
  public function display()
  {  
      wp_register_style('activity-dashboard-stylesheet', plugins_url('assets/css/activity-dashboard.css', __FILE__));
      wp_enqueue_style('activity-dashboard-stylesheet');
      
      wp_enqueue_script('bootstrap-button', plugins_url('assets/js/bootstrap-button.min.js', __FILE__), false, true);
      wp_enqueue_script('masonry', plugins_url('assets/js/masonry.pkgd.min.js', __FILE__), false, true);
      
      // TODO package timeago with plugin
      wp_enqueue_script('timeago');
      
      $activities = $this->_database->getActivities($this->initialActivityCount);      
      
      $this->render('/views/header.phtml');
      $this->render('/views/activity-wall.phtml', array('activities' => $activities));
      $this->render('/views/footer.phtml'); 
  }
  
  /**
   * Activate plugin
   */
  public function activate()
  {
      add_option("surevine-technical-dashboard-settings", '[]', '', 'yes');
  }
  
  /**
   * Compile and render template script
   */
  protected function render($script, $params = array())
  {
    $templateEngine = new Mustache_Engine;
  	include $this->_filePath . $script;
  }
  
  /**
   * Retrieve batch of activities older than provided activity ID.
   */
  function ajax_load_activities() {
      
    if(!isset($_GET['oldest_activity']) || $_GET['oldest_activity'] == '') {
        // Return 400 BAD REQUEST due to missing parameter
        header('HTTP', true, 400);
        die();
    }
    
    $oldestActivity = $this->_database->getActivityById(mysql_real_escape_string($_GET['oldest_activity']));
    $activities = $this->_database->getActivitiesBefore($oldestActivity->created, $this->activitiesPerPage);

    if(count($activities) == 0) {
        // Return 404 for no more activities
        header('HTTP', true, 404);
        die();
    }
    
    $this->render('/views/activitystream.phtml', array('activities' => $activities));
    die();
  
  }
  
} 
?>