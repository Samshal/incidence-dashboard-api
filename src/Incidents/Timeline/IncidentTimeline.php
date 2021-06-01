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

        $query = "SELECT CONVERT(a.IncidentDate, DATE) as IncidentDate, COUNT(*) as TotalIncidences FROM incidents_incidents a";

        if ($sDate !== "" && $eDate !== ""){
            $sDate = $sDate." 00:00:00";
            $eDate = $eDate." 23:59:59";

            $query .= " WHERE (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
        }

        $query .= "  GROUP BY CONVERT(a.IncidentDate, Date) ORDER BY DATE(a.IncidentDate) ASC";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }


    public static function getIncidentsGeojson(array $data){        
        $sDate = $data["startDate"] ?? "";
        $eDate = $data["endDate"] ?? "";

        $incidents = \IncidenceDashboardApi\Incidents\Incident\Incident::viewIncidents([
            "length"=>-1,
            "startDate"=>$sDate,
            "endDate"=>$eDate
        ]);

        $geojson = array(
           'type'      => 'FeatureCollection',
           'features'  => array()
        );

        foreach ($incidents["data"] as $key => $value) {
            $point = $value["IncidentPointOfInterest"];
            $point = explode(" ", str_replace(")", "", str_replace("POINT(", "", $point)));
            $feature = array(
                'id' => $value["IncidentId"],
                'type' => 'Feature', 
                'geometry' => array(
                    'type' => 'Point',
                    # Pass Longitude and Latitude Columns here
                    'coordinates' => array($point[1], $point[0])
                ),
                # Pass other attribute columns here
                'properties' => array(
                    'category' => $value['IncidentCategoryName'],
                    'type' => $value['IncidentTypeName'],
                    'description' => $value['IncidentDescription'],
                    'title' => $value['IncidentName'],
                    'lga' => $value['LGA'],
                    'state' => $value['State'],
                    'region' => $value['Region'],
                    'locality' => $value['Locality']
                    )
                );
            # Add feature arrays to feature collection array
            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }

}