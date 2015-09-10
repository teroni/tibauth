<?php namespace Tib\Models;

use \SimpleXMLElement;
use \Carbon as Carbon;
use \Cache;

class EVE
{
	/*
	 * 	Attempt a curl request.
	 * 	Variables:
	 * 	URI: EVE-Api endpoint (ex: eve/CharacterID.xml.aspx
	 * 	Parameters: array of parameters built with multiple arrays of 'keys' and 'values.
	 * 	ex: $parameters = array(array('key' => 'keyID', 'value' => '12345678'), array('key' => 'vCode', 'value' => 'asdghj12345');
	 */
	public static function request($uri, $parameters, $cachable = false)
	{
		if($cachable == true)
		{
			if (Cache::has(serialize($uri).serialize($parameters)))
			{
			    $return = Cache::get(serialize($uri).serialize($parameters));
			    $returndata = @new SimpleXMLElement($return);
			    return $returndata;
			}
		}
		// Variable setup.
		// Base URL:
		$url = 'https://api.eveonline.com/';
		
		// Let's add the parameters to the curl request.
		$requesturl = $url . $uri;
		if(count($parameters) > 0)
		{
			$pn = 0;
			foreach($parameters as $par)
			{
				if($pn == 0)
				{
					// First parameter.
					$requesturl .= '?'. $par['key']. '=' . $par['value'];
					$pn = $pn + 1;
				} else {
					$requesturl .= '&'. $par['key']. '=' . $par['value'];
					$pn = $pn + 1;
				}
				
				 
			}
		}
		// Initialize a new request for this URL
		$ch = curl_init($requesturl);
		
		// Set the options for this request
		curl_setopt_array($ch, array(
		CURLOPT_FOLLOWLOCATION => true, // Yes, we want to follow a redirect
		CURLOPT_RETURNTRANSFER => true, // Yes, we want that curl_exec returns the fetched data
		CURLOPT_SSL_VERIFYPEER => false, // Do not verify the SSL certificate
		));
		
		// Fetch the data from the URL
		$request = curl_exec($ch);
		
		// Close the connection
		curl_close($ch);
		
		// Return a new SimpleXMLElement based upon the received data
		try {
			$returndata = @new SimpleXMLElement($request);
			$cachetime = 30;
			Cache::put(serialize($uri).serialize($parameters), $request, $cachetime);
			return $returndata;
		}
		catch (Exception $e) {
			// SimpleXMLElement::__construct produces an E_WARNING error message for
			// each error found in the XML data and throws an exception if errors
			// were detected. Catch any exception and return failure (NULL).
			return null;
		}
	}
	
	/*
	 * 	General Common API Requests
	 * 	Variables:
	 * 	Parameters: array of parameters built with multiple arrays of 'keys' and 'values.
	 * 	ex: $parameters = array(array('key' => 'keyID', 'value' => '12345678'), array('key' => 'vCode', 'value' => 'asdghj12345');
	 */
	public static function AccessMask($parameters)
	{
		$uri = '/account/APIKeyInfo.xml.aspx';
		$api = EVE::request($uri, $parameters);
		if(!is_null($api))
		{
			// Let's check the access mask.
			if(isset($api->result->key))
			{
				return intval($api->result->key['accessMask']);
			} else {
				return $api;
			}
		} else {
			return $api;
		}
	}
	public static function CharacterList($parameters)
	{
		$uri = '/account/Characters.xml.aspx';
		$api = EVE::request($uri, $parameters);
		if(!is_null($api))
		{
			// Let's check the access mask.
			return $api;
		} else {
			return null;
		}
	}
	public static function CorpInfo($parameters)
	{
		$uri = '/corp/CorporationSheet.xml.aspx';
		$api = EVE::request($uri, $parameters);
		if(!is_null($api))
		{
			// Let's check the access mask.
			return $api;
		} else {
			return null;
		}
	}
	public static function CharacterWallet($parameters)
	{
		$uri = 'char/AccountBalance.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(isset($api->result->rowset))
		{
			foreach($api->result->rowset->row as $account)
			{
				$balance = $balance + intval($account['balance']);
			}
			
			// Return the balance.
			return $balance;
			
		} elseif(is_null($api))
		{
			// Let's check the access mask.
			return null;
		} elseif(isset($api->error)) {
			// Maybe there's an error. Let's check.
			return array('error' => (string)$api->error['code']);
		}
		
	}
	public static function CorporationWallet($parameters)
	{
		$uri = 'corp/AccountBalance.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(isset($api->result->rowset))
		{
			foreach($api->result->rowset->row as $account)
			{
				$balance = $balance + intval($account['balance']);
			}
				
			// Return the balance.
			return $balance;
				
		} elseif(is_null($api))
		{
			// Let's check the access mask.
			return null;
		} elseif(isset($api->error)) {
			// Maybe there's an error. Let's check.
			return array('error' => (string)$api->error['code']);
		}
	
	}

	public static function CharacterTransactions($parameters)
	{
		$uri = '/char/WalletTransactions.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(!is_null($api))
		{
			return $api;
		} elseif(is_null($api))
		{
			// Let's check the access mask.
			return null;
		} else {
			return false;
		}
	
	}
	public static function CharacterJournal($parameters)
	{
		$uri = '/char/WalletJournal.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(!is_null($api))
		{
			return $api;
		} elseif(is_null($api))
		{
			// Let's check the access mask.
			return null;
		} else {
			return false;
		}
	
	}
	public static function CharacterOrders($parameters)
	{
		$uri = '/char/MarketOrders.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(!is_null($api))
		{
			return $api;
		} elseif(is_null($api))
		{
			// Let's check the access mask.
			return null;
		} else {
			return false;
		}
	
	}
	public static function CorporationTransactions($parameters)
	{
		$uri = '/corp/WalletTransactions.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(!is_null($api))
		{
			return $api;
		} else {
			return null;
		}
	
	}
	public static function CorporationJournal($parameters)
	{
		$uri = '/corp/WalletJournal.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(!is_null($api))
		{
			return $api;
		} else {
			return null;
		}
	
	}
	public static function CharNamebyID($id = 0)
	{
		$parameters[] = array('key' => 'ids', 'value' => $id);
		$uri = '/eve/CharacterName.xml.aspx';
		$api = EVE::request($uri, $parameters, true);
		if(!is_null($api))
		{
			return $api->result->rowset->row['name'];
		} else {
			return null;
		}
	
	}
	public static function TypeNamebyID($id = 0)
	{
		$parameters[] = array('key' => 'ids', 'value' => $id);
		$uri = '/eve/TypeName.xml.aspx';
		$api = EVE::request($uri, $parameters, true);
		if(!is_null($api))
		{
			if($api->result->rowset->row['name'] != "Unknown")
			{
				return $api->result->rowset->row['name'];
			} else {
				if($item = Item::where('typeID', '=', $id)->first())
				{
					return $item->typeName;
				} else {
					return "Unknown";
				}
			}
		} else {
			return null;
		}
	
	}
	public static function CorporationOrders($parameters)
	{
		$uri = '/corp/MarketOrders.xml.aspx';
		$api = EVE::request($uri, $parameters);
		$balance = 0;
		if(!is_null($api))
		{
			return $api;
		} else {
			return null;
		}
	
	}
	
	public static function getCharacterOrder($order) // Requires order eloquent model with character relationship included.
	{
		if($order)
		{
			$parameters = @array(array('key' => 'keyID', 'value' => $order->character->apiid), array('key' => 'vCode', 'value' => $order->character->vcode), array('key' => 'characterID', 'value' => $order->character->charid),array('key' => 'orderID', 'value' => $order->orderID));
			$uri = '/char/MarketOrders.xml.aspx';
			$api = EVE::request($uri, $parameters);
			$balance = 0;
			if(!is_null($api))
			{
				return $api;
			} else {
				return null;
			}
		}
	
	}
	public static function getCorporationOrder($order) // Requires order eloquent model with character relationship included.
	{
		if($order)
		{
			$parameters = @array(
					array('key' => 'keyID', 'value' => $order->corporation->apiid),
					array('key' => 'vCode', 'value' => $order->corporation->vcode),
					array('key' => 'accountkey', 'value' => $order->corporation->division),
					array('key' => 'orderID', 'value' => $order->orderID));
			$uri = '/corp/MarketOrders.xml.aspx';
			$api = EVE::request($uri, $parameters);
			$balance = 0;
			if(!is_null($api))
			{
				return $api;
			} else {
				return null;
			}
		}
	
	}

}