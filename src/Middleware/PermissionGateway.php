<?php declare(strict_types=1);

namespace RFHApi\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use EmmetBlue\Core\Constant;

class PermissionGateway implements \EmmetBlueMiddleware\MiddlewareInterface
{
	protected static function isPublicEndpoint($endpoint){
		$endpoints = [];

		try {
			$endpointsFile = Constant::getGlobals()["config-dir"]["public-endpoints"];
			if (file_exists($endpointsFile)){
				$endpoints = json_decode(file_get_contents($endpointsFile));
			}
		}
		catch (\TypeError $e) {
		}

		$endpoint = implode("__", $endpoint);

		return in_array($endpoint, $endpoints);
	}

	protected static function isUserLoggedIn($userId, $sessionId, $userToken)
	{
		$isTokenValid = \RFHApi\User\UserSession\Session::isTokenValid($userId, ["sessionId"=>$sessionId, "token"=>$userToken]);

		return $isTokenValid["status"];
	}

	protected static function isUserPermitted($userId, $sessionId, $endpoint)
	{
		$session = \RFHApi\User\UserSession\Session::load((int)$userId, (int)$sessionId);
		$accountType = $session[0]["AccountTypeName"];

		if (is_null($accountType)){
			return false;
		}

		$requestData = [
			"accountType"=>$accountType,
			"endpoint"=>$endpoint
		];

		$isUserPermitted = \RFHApi\User\UserPermission\Acl::checkPermission($userId, $requestData);

		return $isUserPermitted["status"];
	}

	public function getStandardResponse()
	{
		return function(RequestInterface $request, ResponseInterface $response, callable $next)
		{
			$args = $request->getAttribute('routeInfo')[2];

			if (!isset($args["resourceId"]) && (!is_null($request->getServerParams()["QUERY_STRING"]))){
				$qs = explode("&",$request->getServerParams()["QUERY_STRING"]);
				foreach($qs as $string){
					$explode = explode("=",$string);
					if ("resourceId" == $explode[0]){
						$args["resourceId"] = $explode[1];
						break;
					}
					continue;
				}
			}

			$module = $args['module'];
			$resource = $args['resource'];
			$action = $args['action'];

			$token_string = (isset($request->getHeaders()["HTTP_AUTHORIZATION"][0])) ? $request->getHeaders()["HTTP_AUTHORIZATION"][0] : "";

			$token = explode(",", $token_string); //token_string format: token,session_id,user_id

			$sessionId = $token[1] ?? 0;
			$userId = (int)$token[2] ?? 0;
			$token = $token[0] ?? 0;
			
			$endpoint = [$module, $resource, $action];

			if (!self::isPublicEndpoint($endpoint)){
				if (!self::isUserLoggedIn($userId, $sessionId, $token))
				{
					$globalResponse = [];

					$globalResponse["status"] = 401;
					$globalResponse["body"]["errorStatus"] = true;
					$globalResponse["body"]["errorMessage"] = "You havent been logged in or your supplied login token is invalid.";

					return $response->withJson($globalResponse["body"], $globalResponse["status"]);
				}

				try
				{
					if (!self::isUserPermitted($userId, $sessionId, $endpoint) || (isset($args["resourceId"]) && $userId != $args["resourceId"]))
					{
						$globalResponse = [];

						$globalResponse["status"] = 401;
						$globalResponse["body"]["errorStatus"] = true;
						$globalResponse["body"]["errorMessage"] = "You do not have the appropriate permissions to perform the requested operation";

						return $response->withJson($globalResponse["body"], $globalResponse["status"]);
					}
				}
				catch(\Exception $e)
				{
					$globalResponse = [];

					$globalResponse["status"] = 403;
					$globalResponse["body"]["errorStatus"] = true;
					$globalResponse["body"]["errorMessage"] = "Authentication failed, please resend a valid request. ".$e->getMessage();

					return $response->withJson($globalResponse["body"], $globalResponse["status"]);
				}
			}

			return $next($request, $response);
		};
	}
}