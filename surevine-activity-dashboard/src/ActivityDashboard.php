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
     * Database wrapper
     * 
     * @var DashboardDatabaseInterface
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
    protected $initialActivityCount = 20;
    
    /**
    * Number of activities to fetch per 'page' of dashboard
    *
    * @var int
    */  
    protected $activitiesPerPage = 10;

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
      $activities = $this->_database->getActivities($this->initialActivityCount);      
      
      $this->render('/views/header.phtml');
      $this->render('/views/activity-wall.phtml', array('activities' => $activities));
      $this->render('/views/footer.phtml'); 
    }
    
    /**
     * Load required scripts / stylesheets
     */
    protected function loadScripts()
    {
      wp_enqueue_style('bootstrap', plugins_url('../assets/css/bootstrap.min.css', __FILE__));
      wp_enqueue_style('bootstrap-responsive', plugins_url('../assets/css/bootstrap-responsive.min.css', __FILE__));
      wp_enqueue_style('activity-dashboard-stylesheet', plugins_url('../assets/css/activity-dashboard.css', __FILE__));
      
      wp_enqueue_script('bootstrap', plugins_url('../assets/js/bootstrap.min.js', __FILE__), array('jquery'), false, false);
      wp_enqueue_script('bootstrap-button', plugins_url('../assets/js/bootstrap-button.min.js', __FILE__), array('jquery'), false, false);
      wp_enqueue_script('masonry', plugins_url('../assets/js/masonry.pkgd.min.js', __FILE__), array('jquery'), false, false);
      wp_enqueue_script('timeago', plugins_url('../assets/js/jquery.timeago.min.js', __FILE__), array('jquery'), false, false);
    }
    
    public function activate()
    {
        add_option("surevine-technical-dashboard-settings", '[]', '', 'yes');
        $this->setup_database(); 
    }
    
    /**
     * Create or update database schema
     */
    protected function setup_database()
    {
        $table_name = $this->_database->_activityTableName;
        $create_table_sql = "CREATE TABLE $table_name (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `activityId` varchar(100) NOT NULL,
              `data` text NOT NULL,
              `type` varchar(255) NOT NULL,
              `created` datetime NOT NULL,
              PRIMARY KEY (`id`)
            );";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $create_table_sql );        
    }        
    
    /**
    * Compile and render template script
    *
    * @param string $template path to template to render
    * @param array $params    
    */
    protected function render($template, $params = array())
    {
        $templateEngine = new Mustache_Engine;
        include $this->_filePath . $template;
    }
    
    /**
    * Retrieve a batch of activities older than provided activity ID.
    */
    public function ajax_load_activities()
    {
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
    
    /**
     * Detects whether page contains shortcode (indicating that we need to include resources in head)
     */
    public function isDashboardPage($posts) {
        
        if(empty($posts)) {
            return $posts;
        }
        
        foreach ($posts as $post) {
            if(strpos($post->post_content, '[surevine-activity-dashboard]') !== false) {
                $this->loadScripts();
                break;
            }
        }
        
        return $posts;
    }
  
} 
?>