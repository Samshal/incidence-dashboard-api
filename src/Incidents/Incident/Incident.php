<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace IncidenceDashboardApi\Incidents\Incident;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class IncidenceDashboardApi\Incidents\Incident\Incident.
 *
 * Incident Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 04/15/2021 11:59
 */
class Incident {
    public static function newIncident(array $data){
        $name = $data["name"];
        $type = $data["type"];
        $location = $data["location"];
        $date = date("Y-m-d H:i:s", strtotime($data["date"]));

        $pointOfInterest = $data["pointOfInterest"] ?? 'POINT(0 0)';
        $description = $data["description"] ?? "";

        $metadata = $data["metadata"] ?? [];

        $query = "INSERT INTO Incidents_Incidents(IncidentName, IncidentType, IncidentLocation, IncidentDate, IncidentPointOfInterest, IncidentDescription) VALUES ('$name', $type, $location, '$date', ST_GeomFromText('$pointOfInterest'), '$description');";

        $connection = DBConnectionFactory::getConnection();
        $result = $connection->exec($query);

        $incidentId = $connection->lastInsertId();
        if (count($metadata) > 0){
            $meta = [];
            foreach($metadata as $value){
                $key = array_keys($value)[0];
                $value = $value[$key];
                $meta[] = "($incidentId, '$key', '$value')";
            }

            $query = "INSERT INTO Incidents_IncidentMetadata (IncidentId, Field, FieldValue) VALUES ".implode(",", $meta);
            $result = $connection->exec($query);
        }
        
        return ["lastInsertId"=>$incidentId];
    }

    public static function viewIncidents(array $data){
    	$limit = $data["length"] ?? 10;
    	$offset = $data["start"] ?? 0;
    	$search = $data["search"] ?? "";
    	$sDate = $data["startDate"] ?? "";
    	$eDate = $data["endDate"] ?? "";

        $query = "SELECT a.IncidentId, a.IncidentName, a.IncidentDate, ST_asText(a.IncidentPointOfInterest) as IncidentPointOfInterest, a.IncidentDescription, b.IncidentTypeName, d.IncidentCategoryName, c.EntityName as Locality, e.EntityName as LGA, f.EntityName as State, g.EntityName as Region FROM Incidents_Incidents a INNER JOIN Incidents_IncidentTypes b ON a.IncidentType = b.IncidentTypeId INNER JOIN SpatialEntities_Entities c ON a.IncidentLocation = c.EntityId INNER JOIN Incidents_IncidentCategories d ON b.IncidentCategoryId = d.IncidentCategoryId INNER JOIN SpatialEntities_Entities e ON c.EntityParent = e.EntityId INNER JOIN SpatialEntities_Entities f ON e.EntityParent = f.EntityId INNER JOIN SpatialEntities_Entities g ON f.EntityParent = g.EntityId";

        if ($search != ""){
        	$keywords = explode(" ", $search);
        	$squery = [];
        	foreach ($keywords as $keyword){
	        	$squery[] = " (
	        		a.IncidentName LIKE '%$keyword%' OR
	        		a.IncidentDescription LIKE '%$keyword%' OR
	        		b.IncidentTypeName LIKE '%$keyword%' OR
	        		d.IncidentCategoryName LIKE '%$keyword%' OR
	        		c.EntityName LIKE '%$keyword%' OR
	        		e.EntityName LIKE '%$keyword%' OR
	        		f.EntityName LIKE '%$keyword%' OR
	        		g.EntityName LIKE '%$keyword%'
	        	)";
        	}

        	$query .= " WHERE (".implode(" AND ", $squery).")";
        }

        if ($sDate !== "" && $eDate !== ""){
        	$sDate = $sDate." 00:00:00";
        	$eDate = $eDate." 23:59:59";

    		$query .= " AND (a.IncidentDate BETWEEN '$sDate' AND '$eDate')";
    	}

        $cquery = $query;
        $query .= " ORDER BY a.DateCreated DESC";

        if ($limit != -1){
            $query .= " LIMIT $limit OFFSET $offset";
        }

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($result as $key=>$data){
            $query = "SELECT * FROM Incidents_IncidentMetadata a WHERE a.IncidentId = ".$data['IncidentId'];
            $_result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
            $result[$key]["metadata"] = $_result;
        }

        $cresult = DBConnectionFactory::getConnection()->query($cquery)->fetchAll(\PDO::FETCH_ASSOC);
        $total = count($cresult);

        $result = [
        	"data"=>$result,
        	"recordsFiltered"=>$total,
        	"recordsTotal"=>$limit
        ];

        return $result;
    }

    public static function viewIncident(int $resourceId){
        $query = "SELECT a.IncidentId, a.IncidentName, a.IncidentDate, ST_asText(a.IncidentPointOfInterest) as IncidentPointOfInterest, a.IncidentDescription, b.IncidentTypeName, d.IncidentCategoryName, c.EntityName as Locality, e.EntityName as LGA, f.EntityName as State, g.EntityName as Region FROM Incidents_Incidents a INNER JOIN Incidents_IncidentTypes b ON a.IncidentType = b.IncidentTypeId INNER JOIN SpatialEntities_Entities c ON a.IncidentLocation = c.EntityId INNER JOIN Incidents_IncidentCategories d ON b.IncidentCategoryId = d.IncidentCategoryId INNER JOIN SpatialEntities_Entities e ON c.EntityParent = e.EntityId INNER JOIN SpatialEntities_Entities f ON e.EntityParent = f.EntityId INNER JOIN SpatialEntities_Entities g ON f.EntityParent = g.EntityId WHERE a.IncidentId = $resourceId";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($result as $key=>$data){
            $query = "SELECT * FROM Incidents_IncidentMetadata a WHERE a.IncidentId = ".$data['IncidentId'];
            $_result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
            $result[$key]["metadata"] = $_result;
        }
        return $result;
    }
}