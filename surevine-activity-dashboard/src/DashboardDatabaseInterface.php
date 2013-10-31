<?php
/**
 * Dashboard database interface
 *
 * @author Lloyd Watkin <lloyd.watkin@surevine.com>
 * @author Jonny Heavey <jonny.heavey@surevine.com>
 * @since  2012/11/02
 */
interface DashboardDatabaseInterface
{
	/**
	 * Get activities 
	 * 
	 * @param  integer $maxItems
	 * @param  integer $offset
	 * @return stdClass
	 */
	public function getActivities($maxItems = 30, $offset = NULL);
  
	/**
	 * Get specific activity by id
	 *
	 * @param  integer $id
	 * @return stdClass
	 */
    public function getActivityById($id);
  
	/**
	 * Get activities older than a certain threshold
	 *
	 * @param  string $created
	 * @param  integer $maxItems
	 * @return stdClass
	 */
	public function getActivitiesBefore($created, $maxItems = 30);  
  
}
