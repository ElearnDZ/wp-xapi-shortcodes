<?php

	/**
	 * Xapi.
	 */
	class Xapi {

		/**
		 * Constructor.
		 */
		public function __construct($endpoint, $username, $password) {
			$this->endpoint=$endpoint;
			$this->username=$username;
			$this->password=$password;
		}

		/**
		 * Get statements.
		 */
		public function getStatements($params) {
			$url=get_option("h5pxapi_endpoint_url");
			if (substr($url,-1)!="/")
				$url.="/";
			$url.="statements";

			if ($params["agentEmail"]) {
				$params["agent"]=json_encode(array(
					"mbox"=>"mailto:".$params["agentEmail"]
				));

				unset($params["agentEmail"]);
			}

			$query=http_build_query($params);

			//print_r($query);

			$url.="?".$query;

			$headers=array(
				"Content-Type: application/json",
				"X-Experience-API-Version: 1.0.1",
			);

			$userpwd=get_option("h5pxapi_username").":".get_option("h5pxapi_password");

			$curl=curl_init();
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
			curl_setopt($curl,CURLOPT_URL,$url);
			curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
			curl_setopt($curl,CURLOPT_USERPWD,$userpwd);
			$res=curl_exec($curl);

			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if ($code!=200) {
				echo $res;
				throw new Exception("Unable to connect to xapi",$code);
			}

			$decoded=json_decode($res,TRUE);

			if (!$decoded || !array_key_exists("statements",$decoded))
				throw new Exception("Bad response from xapi endpoint.");

			return $decoded["statements"];
		}
	}