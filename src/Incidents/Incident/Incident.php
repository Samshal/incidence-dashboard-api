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
            foreach($metadata as $key=>$value){
                $meta[] = "($incidentId, $key, '$value')";
            }

            $query = "INSERT INTO Incidents_IncidentMetadata (IncidentId, FieldId, FieldValue) VALUES ".implode(",", $meta);
            $result = $connection->exec($query);
        }
        
        return ["lastInsertId"=>$incidentId];
    }

    public static function viewIncidents(){
        $query = "SELECT a.IncidentId, a.IncidentName, a.IncidentDate, ST_asText(a.IncidentPointOfInterest) as IncidentPointOfInterest, b.*, c.* FROM Incidents_Incidents a INNER JOIN Incidents_IncidentTypes b ON a.IncidentType = b.IncidentTypeId INNER JOIN SpatialEntities_Entities c ON a.IncidentLocation = c.EntityId";

        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($result as $key=>$data){
            $query = "SELECT * FROM Incidents_IncidentMetadata a INNER JOIN Incidents_MetadataFields b ON a.FieldId = b.FieldId WHERE a.IncidentId = ".$data['IncidentId'];
            $_result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
            $result[$key]["metadata"] = $_result;
        }

        return $result;
    }

    public static function viewIncident(int $resourceId){
        $query = "SELECT a.IncidentId, a.IncidentName, a.IncidentDate, ST_asText(a.IncidentPointOfInterest) as IncidentPointOfInterest, b.*, c.* FROM Incidents_Incidents a INNER JOIN Incidents_IncidentTypes b ON a.IncidentType = b.IncidentTypeId INNER JOIN SpatialEntities_Entities c ON a.IncidentLocation = c.EntityId WHERE a.IncidentId = $resourceId";
        $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($result as $key=>$data){
            $query = "SELECT * FROM Incidents_IncidentMetadata a INNER JOIN Incidents_MetadataFields b ON a.FieldId = b.FieldId WHERE a.IncidentId = ".$data['IncidentId'];
            $_result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);
            $result[$key]["metadata"] = $_result;
        }
        return $result;
    }
}