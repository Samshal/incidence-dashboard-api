<?php declare (strict_types=1);
/**
 * Controller Class.
 *
 * This file is part of RFHubAPI, please read the documentation
 * available in the root level of this project
 *
 * @license MIT
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 *
 */

namespace RFHAPI\User\UserAccount;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;

/**
 * class RFHAPI\User\UserAccount\UserKyc.
 *
 * User KYC Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 17/01/2021 02:37
 */
class UserKyc {

	protected const KYC_VERIFY_STRING = "verified";
	protected const KYC_UNVERIFIED_STRING = "unverified";
	protected const KYC_INPROGRESS_STRING = "in_progress";

	public static function getKycStatusTypeName(int $statusTypeId){
		$query = "SELECT TypeName FROM Users_KycStatusTypes WHERE TypeId = $statusTypeId";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		if (count($result) == 1){
			return $result[0]["TypeName"];
		}

		return 0;
	}

	public static function getKycStatusTypeId(string $statusTypeName){
		$query = "SELECT TypeId FROM Users_KycStatusTypes WHERE TypeName = '$statusTypeName'";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		if (count($result) == 1){
			return $result[0]["TypeId"];
		}

		return 0;
	}

	public static function viewStatus(int $userId, array $data = ["kycType"=>0]){
		$kycType = (int)$data["kycType"];

		$query = "SELECT a.*, b.TypeName as KycStatusName, c.TypeName as KycTypeName FROM Users_UserKycStatus a INNER JOIN Users_KycStatusTypes b ON a.KycStatus = b.TypeId INNER JOIN Users_KycTypes c ON a.KycType = c.TypeId WHERE a.UserId=$userId";
		$query = $kycType != 0 ? $query . " AND a.KycType = $kycType" : $query;

		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return $result;
	}

	public static function addKycStatus(int $userId, array $data){
		$kycType = (int) $data["kycType"];
		$kycStatus = (int) $data["kycStatus"];

		$result = DBQueryFactory::insert("Users_UserKycStatus", [
			"UserId"=>$userId,
			"KycType"=>$kycType,
			"KycStatus"=>$kycStatus
		], false);

		return ["UserKycId"=>$result["lastInsertId"]];
	}

	public static function updateKycStatus(int $userId, array $data){
		$kycId = $data["kycStatusId"];
		$kycValue = $data["status"];

		$query = "UPDATE Users_UserKycStatus SET KycStatus = $kycValue, LastModified=CURRENT_TIMESTAMP WHERE UserKycId = $kycId AND UserId=$userId;";

		$result = ["status"=>DBConnectionFactory::getConnection()->exec($query)];

		return $result;
	}

	public static function getLowestPriorityGroup(){
		$query = "SELECT * FROM Users_KycTypeGroups where Priority = 0;";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		return $result[0] ?? [];
	}

	public static function determineKycGroup(int $userId){
		$query = "SELECT * FROM Users_KycTypeGroups a INNER JOIN Users_KycTypes b ON a.GroupId = b.GroupId ORDER BY a.Priority DESC";
		$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

		$groups = [];

		foreach ($result as $key=>$value){
			$groups[$value["GroupId"]][] = $value["TypeId"];
		}
		
		$userGroup = 0;
		foreach ($groups as $key=>$group){
			$group = implode(", ", $group);
			$query = "SELECT COUNT(*) as Total FROM Users_UserKycStatus a INNER JOIN Users_KycStatusTypes b ON a.KycStatus = b.TypeId WHERE a.KycType IN ($group) AND a.UserId = $userId AND b.Typename = ".QB::wrapString(self::KYC_VERIFY_STRING, "'");
			
			$result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

			$kycComplete = count($groups[$key]) == $result[0]["Total"];

			if ($kycComplete){
				$userGroup = $key;
				break;
			}
 		}

		return ["GroupId"=>$userGroup];
	}
}