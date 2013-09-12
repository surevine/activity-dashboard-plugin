<?php
/**
 * Dashboard database 
 *
 * @author Lloyd Watkin <lloyd.watkin@surevine.com>
 * @author Jonny Heavey <jonny.heavey@surevine.com>
 * @since  2012/11/02
 */
class DashboardDatabase implements DashboardDatabaseInterface
{
	/**
	 * Store database connection
	 * 
	 * @var WPDB 
	 */
	protected $_database;
  
  /**
   * Store database table name for activities
   * 
   * @var string
   */
  protected $_activityTableName;
	
	/**
	 * Constructor
	 * 
	 * @param WPDB $db
	 */
	public function __construct(wpdb $database, $activityTableName)
	{
		$this->_database = $database;
    $this->_activityTableName = $activityTableName;
	}
	
	/**
	 * Get activities
	 *
	 * @param  integer $maxItems
	 * @param  integer $offset
	 * @return stdClass
	 */
	public function getActivities($maxItems = 30, $offset = 0)
	{
		    $maxItems = (int) $maxItems;
        $offset = (int) $offset;

    		return $this->_database->get_results(
    		    "SELECT * FROM `{$this->_activityTableName}` ORDER BY `created` DESC LIMIT {$maxItems} OFFSET {$offset} ;"
    		);   
	}
  
	/**
	 * Get activity by id
	 *
	 * @param  integer $id ID of activity to retrieve
	 * @return stdClass
	 */
	public function getActivityById($id)
	{
        $id = (int) $id;
    
    		return $this->_database->get_row(
    		    "SELECT * FROM `{$this->_activityTableName}` WHERE `activityId`='{$id}' ;"
    		);   
	}
  
	/**
	 * Get activities
	 *
	 * @param  string $created Threshold unix timestamp to retrieve older activities than
	 * @param  integer $maxItems
	 * @return stdClass
	 */
	public function getActivitiesBefore($created, $maxItems = 30)
	{
		    $maxItems = (int) $maxItems;

    		return $this->_database->get_results(
    		    "SELECT * FROM `{$this->_activityTableName}` WHERE `created` < '{$created}' ORDER BY `created` DESC LIMIT {$maxItems} ;"
    		);   
	}
  
}