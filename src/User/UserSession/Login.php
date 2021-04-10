<?php declare(strict_types=1);
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

namespace RFHAPI\User\UserSession;

use EmmetBlue\Core\Factory\DatabaseConnectionFactory as DBConnectionFactory;
use EmmetBlue\Core\Factory\DatabaseQueryFactory as DBQueryFactory;
use EmmetBlue\Core\Exception\SQLException as SQLException;
use EmmetBlue\Core\Builder\QueryBuilder\QueryBuilder as QB;


/**
 * class Login.
 *
 * Session Controller
 *
 * @author Samuel Adeshina <samueladeshina73@gmail.com>
 * @since v0.0.1 17/01/2021 19:35
 */
class Login
{
    /**
     * Determines if a login data is valid
     *
     * @param string $username
     * @param string $password
     */
    public static function isLoginDataValid($username, $password)
    {
        $query = "SELECT PasswordHash FROM Users_Account WHERE UserEmail='$username'";

        try
        {
            $result = DBConnectionFactory::getConnection()->query($query)->fetchAll(\PDO::FETCH_ASSOC);

            if (count($result) == 1){
                $passwordHash = $result[0]["PasswordHash"];

                if (!is_null($passwordHash) && password_verify($password, $passwordHash)) {
                    return true;
                }
            }
        }
        catch (\PDOException $e)
        {
            
            throw new SQLException(sprintf(
                "Unable to validate login data. Error returned by server: %s",
                $e->getMessage()
            ), \EmmetBlue\Core\Constant::UNDEFINED);
        }

        return false;
    }

    /**
     * Returns the login status of the current user
     */
    public static function isUserLoggedIn(int $userid)
    {
        $sessions = Session::getActiveSessions($userId);

        return count($sessions) >= 1;
    }
}