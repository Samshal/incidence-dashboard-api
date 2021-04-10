use RFHApiDB;

INSERT INTO Users_AccountTypes (TypeName) VALUES 
('Field Agent'), 
('Extension Agent'), 
('Supervisor'), 
('Sub Admin'), 
('Super Admin'), 
('Farmer'), 
('Merchant');

INSERT INTO Users_KycTypeGroups (GroupName, Priority) VALUES ('level_0', 0), ('level_1', 1), ('level_2', 2);

INSERT INTO Users_KycTypes (GroupId, TypeName) VALUES (2, 'email'), (3, 'phone_number');

INSERT INTO Users_KycStatusTypes (TypeName) VALUES ('unverified'), ('in_progress'), ('verified');

INSERT INTO Users_AclEndPointRules (EndPoint) VALUES 
('user'),
('spatial_entity');

INSERT INTO SpatialEntities_EntityTypes (SpatialEntityTypeName, AdminLevel) VALUES
('Country', 0),
('State', 1),
('City', 2),
('Village', 3),
('Farm', 4);

INSERT INTO SpatialEntities_MetadataFieldTypes (TypeName) VALUES
('number'),
('text'),
('currency'),
('date');

INSERT INTO SpatialEntities_MetadataFields (FieldName, FieldType) VALUES
('country_capital', 'text'),
('country_phone_code', 'text'),
('country_currency', 'currency'),
('country_currency_symbol', 'text');