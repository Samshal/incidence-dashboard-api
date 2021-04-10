use IncidenceDashboard;

INSERT INTO Users_AclEndPointRules (EndPoint) VALUES 
('user'),
('spatial_entity');

INSERT INTO SpatialEntities_EntityTypes (SpatialEntityTypeName, AdminLevel) VALUES
('Country', 0),
('Region', 1),
('State', 2),
('LGA', 3),
('Locality', 4);

INSERT INTO SpatialEntities_MetadataFieldTypes (TypeName) VALUES
('number'),
('text'),
('currency'),
('date');

INSERT INTO Incidents_MetadataFieldTypes (TypeName) VALUES
('number'),
('text'),
('currency'),
('date');

INSERT INTO Incidents_MetadataFields (FieldName, FieldType) VALUES
('faction', 'text'),
('friendly_forces', 'text'),
('terrain', 'text'),
('number_killed_in_action', 'number'),
('number_missing_in_action', 'number'),
('number_wounded_in_action', 'number'),
('number_of_civillians_killed', 'number'),
('number_of_civillians_abducted', 'number'),
('number_of_criminals_killed', 'number'),
('number_of_suspect_arrested', 'number'),
('associated_feature', 'text');