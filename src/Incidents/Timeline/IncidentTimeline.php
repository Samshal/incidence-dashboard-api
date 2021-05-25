<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents\Timeline;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\Incidents\Timeline\IncidentTimeline.
 *
 * IncidentTimeline Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 05/25/2021 21:17
 */
class IncidentTimeline {
    public static function getDates(array $data){        
        $sDate = $data["startDate"] ?? "";
        $eDate = $data["endDate"] ?? "";

        $query = "SELECT DISTINCT a.IncidentDate FROM incidents_incidents a";

        if ($sDate !== "" && $eDate !== ""){
            $sDate = $sDate." 00:00:00";
            $eDate = $eDate." 23:59:59";

            $query .= " WHERE (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
        }

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

}