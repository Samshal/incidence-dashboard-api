<?php declare (strict_types=1);
/**
 * Visitor Class.
 *
 * This file is part of IncidenceDashboardAPI, please read the documentation
 * available in the root level of this project
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents;

/**
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 25/05/2021 21:17
 */
class Timeline {
	public static function getDates(array $data){
		$return_result = Timeline\IncidentTimeline::getDates($data);

		return $return_result;
	}
}