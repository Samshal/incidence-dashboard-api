<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents\Stats;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\Incidents\Stat\IncidentStat.
 *
 * IncidentStats Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 05/16/2021 14:06
 */
class IncidentStats {
    public static function loadByRegion(array $data){        
        $sDate = $data["startDate"] ?? "";
        $eDate = $data["endDate"] ?? "";

        $query = "SELECT e.EntityName as name, COUNT(*) as value FROM incidents_incidents a 
                 INNER JOIN spatialentities_entities b ON a.IncidentLocation = b.EntityId 
                 INNER JOIN spatialentities_entities c ON b.EntityParent=c.EntityId 
                 INNER JOIN spatialentities_entities d ON c.EntityParent = d.EntityId 
                 INNER JOIN spatialentities_entities e ON d.EntityParent=e.EntityId";

        if ($sDate !== "" && $eDate !== ""){
            $sDate = $sDate." 00:00:00";
            $eDate = $eDate." 23:59:59";

            $query .= " WHERE (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
        }

        $query .= " GROUP BY e.EntityName;";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        $keys = [];
        foreach($result as $values){
            $keys[] = $values["name"];
        }

        return [
            "legends"=>$keys,
            "data"=>$result
        ];
    }

    public static function loadByState(array $data){        
        $sDate = $data["startDate"] ?? "";
        $eDate = $data["endDate"] ?? "";

        $query = "SELECT d.EntityName as name, COUNT(*) as value FROM incidents_incidents a 
                 INNER JOIN spatialentities_entities b ON a.IncidentLocation = b.EntityId 
                 INNER JOIN spatialentities_entities c ON b.EntityParent=c.EntityId 
                 INNER JOIN spatialentities_entities d ON c.EntityParent = d.EntityId";

        if ($sDate !== "" && $eDate !== ""){
            $sDate = $sDate." 00:00:00";
            $eDate = $eDate." 23:59:59";

            $query .= " WHERE (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
        }

        $query .= " GROUP BY d.EntityName;";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        $keys = [];
        $values = [];
        foreach($result as $val){
            $keys[] = $val["name"];
            $values[] = $val["value"];
        }

        return [
            "legends"=>$keys,
            "data"=>$result,
            "values"=>$values
        ];
    }

    public static function loadTypesByRegion(array $data){        
        $sDate = $data["startDate"] ?? "";
        $eDate = $data["endDate"] ?? "";
        $region = $data["region"] ?? "";

        $query = "SELECT e.EntityName as Region, d.EntityName as State, z.IncidentTypeName as IncidentTypeName, COUNT(*) as total 
                FROM incidents_incidents a
                INNER JOIN incidents_incidenttypes z ON a.IncidentType = z.IncidentTypeId
                INNER JOIN incidents_incidentcategories y ON z.IncidentCategoryId = y.IncidentCategoryId
                INNER JOIN spatialentities_entities b ON a.IncidentLocation = b.EntityId 
                INNER JOIN spatialentities_entities c ON b.EntityParent=c.EntityId 
                INNER JOIN spatialentities_entities d ON c.EntityParent = d.EntityId 
                INNER JOIN spatialentities_entities e ON d.EntityParent=e.EntityId";

        if ($sDate !== "" && $eDate !== ""){
            $sDate = $sDate." 00:00:00";
            $eDate = $eDate." 23:59:59";

            $query .= " WHERE (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
        }

        if ($region !== ""){
            $query .= " AND e.EntityName = '$region'";
        }

        $query .= " GROUP BY e.EntityName, d.EntityName, z.IncidentTypeName;";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        $regions = [];

        foreach($result as $val){
            if (!array_key_exists($val["Region"], $regions)){
                $regions[$val["Region"]] = ["legends"=>[], "data"=>[], "values"=>[]];
            }

            if (!in_array($val["IncidentTypeName"], $regions[$val["Region"]]["legends"])){
                $regions[$val["Region"]]["legends"][] = $val["IncidentTypeName"];
            }
            
            $regions[$val["Region"]]["data"][$val["IncidentTypeName"]] = [];

            if (!in_array($val["State"], $regions[$val["Region"]]["values"])){
                $regions[$val["Region"]]["values"][] = $val["State"];
            }

        }

        foreach ($regions as $key=>$region) {
            foreach ($region["data"] as $type=>$value){
                foreach($region["values"] as $state){
                    $regions[$key]["data"][$type][$state] = 0;
                }
            }
        }

        foreach($result as $val){
            $regions[$val["Region"]]["data"][$val["IncidentTypeName"]][$val["State"]] = $val["total"];
        }

        return [
            "categories"=>$regions
        ];
    }

    public static function loadTrendsByRegion(array $data){        
        $sDate = $data["startDate"] ?? "";
        $eDate = $data["endDate"] ?? "";
        $region = $data["region"] ?? "";

        $query = "SELECT e.EntityName as Region, d.EntityName as State, z.IncidentTypeName as IncidentTypeName, 
                DATE_FORMAT(a.IncidentDate, '%Y-%m-%d') as IncidentDate, COUNT(*) as total FROM incidents_incidents a
                INNER JOIN incidents_incidenttypes z ON a.IncidentType = z.IncidentTypeId
                INNER JOIN incidents_incidentcategories y ON z.IncidentCategoryId = y.IncidentCategoryId
                INNER JOIN spatialentities_entities b ON a.IncidentLocation = b.EntityId 
                INNER JOIN spatialentities_entities c ON b.EntityParent=c.EntityId 
                INNER JOIN spatialentities_entities d ON c.EntityParent = d.EntityId 
                INNER JOIN spatialentities_entities e ON d.EntityParent=e.EntityId";

        if ($sDate !== "" && $eDate !== ""){
            $sDate = $sDate." 00:00:00";
            $eDate = $eDate." 23:59:59";

            $query .= " WHERE (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
        }

        if ($region !== ""){
            $query .= " AND e.EntityName = '$region'";
        }

        $query .= " GROUP BY e.EntityName, d.EntityName, z.IncidentTypeName, DATE_FORMAT(a.IncidentDate, '%Y-%m-%d') ORDER BY DATE_FORMAT(a.IncidentDate, '%Y-%m-%d') ASC;";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

        $regions = [];

        foreach($result as $val){
            if (!array_key_exists($val["Region"], $regions)){
                $regions[$val["Region"]] = ["legends"=>[], "data"=>[], "values"=>[]];
            }

            if (!in_array($val["IncidentTypeName"], $regions[$val["Region"]]["legends"])){
                $regions[$val["Region"]]["legends"][] = $val["IncidentTypeName"];
            }
            
            $regions[$val["Region"]]["data"][$val["IncidentTypeName"]] = [];

            if (!in_array($val["IncidentDate"], $regions[$val["Region"]]["values"])){
                $regions[$val["Region"]]["values"][] = $val["IncidentDate"];
            }

        }

        foreach ($regions as $key=>$region) {
            foreach ($region["data"] as $type=>$value){
                foreach($region["values"] as $date){
                    $regions[$key]["data"][$type][$date] = 0;
                }
            }
        }

        foreach($result as $val){
            $regions[$val["Region"]]["data"][$val["IncidentTypeName"]][$val["IncidentDate"]] = $val["total"];
        }

        return [
            "categories"=>$regions
        ];
    }


}