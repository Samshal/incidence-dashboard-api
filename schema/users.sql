USE RFHApiDB;


CREATE TABLE Users_AccountTypes (
	TypeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	TypeName VARCHAR(50) NOT NULL UNIQUE,
	TypeDescription VARCHAR(500)
);


CREATE TABLE Users_KycTypeGroups (
	GroupId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	GroupName VARCHAR(50) NOT NULL UNIQUE,
	Priority INT NOT NULL UNIQUE,
	GroupDescription VARCHAR(500)
);


CREATE TABLE Users_KycTypes (
	TypeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	GroupId INT NOT NULL,
	TypeName VARCHAR(50) NOT NULL,
	TypeDescription VARCHAR(500),

	CONSTRAINT fk_KycTypes_GroupId_KycTypeGroups_GroupId 
		FOREIGN KEY (GroupId) REFERENCES Users_KycTypeGroups (GroupId) ON UPDATE CASCADE ON DELETE CASCADE 
);


CREATE TABLE Users_KycStatusTypes (
	TypeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	TypeName VARCHAR(50) NOT NULL UNIQUE,
	TypeDescription VARCHAR(500)
);


CREATE TABLE Users_Account (
	UserId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	UserEmail VARCHAR(50) NOT NULL UNIQUE,
	PasswordHash LONGTEXT,
	KycGroupId INT,
	AccountName VARCHAR(256),
	LastModified DATETIME,
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT fk_Account_KycGroupId_KycTypeGroups_GroupId 
		FOREIGN KEY (KycGroupId) REFERENCES Users_KycTypeGroups (GroupId) ON UPDATE CASCADE ON DELETE CASCADE 
);


CREATE TABLE Users_UserInfo (
    UserInfoId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	UserId INT NOT NULL,
    FirstName VARCHAR(50),
	LastName VARCHAR(50),
	MiddleName VARCHAR(50),
	Gender VARCHAR(50),
	PreferredTitle VARCHAR(50),
	FullHomeAddress NVARCHAR(100),
	CountryOfResidence VARCHAR(50),
	StateOfResidence VARCHAR(50),
	CityOfResidence VARCHAR(50),
	DateOfBirth DATETIME,
	EmailAddress VARCHAR(100),
	PhoneNumber VARCHAR(50),
	ProfilePhotoUrl LONGTEXT,
    LastModified DATETIME,

	CONSTRAINT fk_UserInfo_UserId_Account_UserId 
		FOREIGN KEY (UserId) REFERENCES Users_Account (UserId) ON UPDATE CASCADE ON DELETE CASCADE 
);


CREATE TABLE Users_UserAccountType (
	AccountTypeId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	UserId INT NOT NULL,
	AccountType INT NOT NULL,
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT u_UserAccountType_UserId_AccountType 
		UNIQUE(UserId, AccountType),
	CONSTRAINT fk_UserAccountType_UserId_Account_UserId 
		FOREIGN KEY (UserId) REFERENCES Users_Account (UserId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_UserAccountType_AccountType_AccountTypes_TypeId 
		FOREIGN KEY (AccountType) REFERENCES Users_AccountTypes (TypeId) ON UPDATE CASCADE ON DELETE CASCADE		 
);

-- For hierarchical linking of accounts.
-- Admin -> Supervisors -> Farmers, etc.
CREATE TABLE Users_LinkedAccounts (
	LinkedAccountId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	UserId INT NOT NULL,
	LinkedAccount INT NOT NULL,
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

	CONSTRAINT u_LinkedAccounts_UserId_LinkedAccount
		UNIQUE(UserId, LinkedAccount),
	CONSTRAINT u_LinkedAccounts_LinkedAccount_UserId
		UNIQUE(LinkedAccount, UserId),
	CONSTRAINT fk_LinkedAccounts_UserId_Account_UserId 
		FOREIGN KEY (UserId) REFERENCES Users_Account (UserId) ON UPDATE CASCADE ON DELETE CASCADE
);
    

CREATE TABLE Users_UserKycStatus (
	UserKycId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	UserId INT NOT NULL,
	KycType INT NOT NULL,
	KycStatus INT NOT NULL,
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	LastModified DATETIME,


	CONSTRAINT u_UserKycStatus_UserId_KycType
		UNIQUE(UserId, KycType),
	CONSTRAINT fk_UserKycStatus_UserId_Account_UserId 
		FOREIGN KEY (UserId) REFERENCES Users_Account (UserId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_UserKycStatus_KycStatus_KycStatusTypes_TypeId 
		FOREIGN KEY (KycStatus) REFERENCES Users_KycStatusTypes (TypeId) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Users_UserSession (
	SessionId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	UserId INT NOT NULL,
	Session LONGTEXT NOT NULL,
	AccountTypeId INT, 
	Status BIT NOT NULL DEFAULT 1,
	DateCreated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	LastModified DATETIME,

	CONSTRAINT fk_UserSession_UserId_Account_UserId
		FOREIGN KEY (UserId) REFERENCES Users_Account (UserId) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_UserSession_AccountTypeId_AccountTypes_TypeId 
		FOREIGN KEY (AccountTypeId) REFERENCES Users_AccountTypes (TypeId) ON UPDATE CASCADE ON DELETE CASCADE	
);


CREATE TABLE Users_AclEndPointRules (
	RuleId INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	AccountType VARCHAR(50),
	KycGroup VARCHAR(50),
	EndPoint VARCHAR(500) NOT NULL,
	EndPointRule BIT NOT NULL DEFAULT 1,

	CONSTRAINT u_AclEndPointRules_AccountType_KycGroup_EndPoint
		UNIQUE(AccountType, EndPoint),
	CONSTRAINT fk_AclEndPointRules_AccountType_AccountTypes_TypeName
		FOREIGN KEY (AccountType) REFERENCES Users_AccountTypes (TypeName) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT fk_AclEndPointRules_KycGroup_KycTypeGroups_GroupName
		FOREIGN KEY (KycGroup) REFERENCES Users_KycTypeGroups (GroupName) ON UPDATE CASCADE ON DELETE CASCADE
);
