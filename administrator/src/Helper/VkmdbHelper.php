<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_vkmdb
 *
 * @copyright   Copyright (C) 2025 Mario Hewera. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace VkmdbNamespace\Component\Vkmdb\Administrator\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * Vkmdb component helper.
 *
 * @since  1.0.0
 */
class VkmdbHelper extends ContentHelper
{
    /**
     * Get Data from the Ninox API
     *
     * @param string $table // contacts, companies, jobs, relations
     * @param string $method // GET, POST, DELETE, PUT
     * @param array $data //Data for saving in Ninox
     *
     * @return object $results
     */

    public static function ninoxApi($table, $method, $data = null){
        $params = ComponentHelper::getParams('com_vkmdb');
		$apiKey = $params->get('ninox_api_key');
		$apiUrl = $params->get('ninox_api_url');
		$teamId = $params->get('team_id');
		$databaseId = $params->get('database_id');
		$tableContacts = $params->get('table_contacts_id');
		$tableContactdata = $params->get('table_contactdata_id');
  		$tableJobs = $params->get('table_jobs_id');
  		$tableRelations = $params->get('table_relations_id');
  		$tableRoles = $params->get('table_roles_id');
  		$tableCounterroles = $params->get('table_counterroles_id');

		$curl = curl_init();
        $url = $apiUrl;
        switch ([$method,$table]) {
            case ['GET','contacts']:
	           // https://api.ninox.com/v1/teams/{{teamId}}/databases/{{databaseId}}/tables/{{tableId}}/records/
                $url .= "{$teamId}/databases/{$databaseId}/tables/{$tableContacts}/records/";
                break;
	        case ['GET','contact']:
		        // https://api.ninox.com/v1/teams/{{teamId}}/databases/{{databaseId}}/tables/{{tableId}}/records/
		        $url .= "{$teamId}/databases/{$databaseId}/tables/{$tableContacts}/records/{$data}";
		        break;
            default:
                break;
        }

	    $curl = curl_init();
	    curl_setopt_array($curl, array(
		    CURLOPT_URL            => $url,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING       => '',
		    CURLOPT_MAXREDIRS      => 10,
		    CURLOPT_TIMEOUT        => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST  => 'GET',
		    CURLOPT_HTTPHEADER     => array(
			    'Content-Type: application/json',
			    'Authorization: Bearer 53322cb0-0a1a-11f0-a3bd-79a3c9ca6870'
		    ),
	    ));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);

        if($info['http_code'] !== 200 && $info['http_code'] !== 201 && $info['http_code'] !== 409)
        {


            $message = json_decode($response);
            //Joomlafehlermeldung einbauen enqueerrorblabla
            $error  = '<ul>';
            $error .= '<li><strong>HTTP Status Code: </strong>' . $info['http_code'] . '</li>';
            $error .= '<li><strong>Message: </strong>' . $message->message . '</li>';
            $error .= '</ul>';

            return $error;
        }
        else{
            return json_decode($response);
        }
    }

	public static function prepareNinoxData($data){
		$item = $data->fields;
		$item->id = $data->id;
		
		$arrayEntries = ['Kontaktdaten2', 'Jobs', 'Beziehungen', 'Beziehungen2', 'Vorname', 'Nachname', 'Name', 'Kontakttyp'];

		foreach ($arrayEntries as $entry) {
			if (property_exists($item, $entry)) {
				if(is_array($item->$entry)){
					$item->$entry = implode(',', $item->$entry);
				}
			}
			else{
				$item->$entry = '';
			}
		}

		if($item->Kontakttyp == "Firma"){
			$item->title = $item->Name;
		}
		else{
			$item->title = $item->Nachname . ', ' . $item->Vorname;
		}
		$item->alias = ApplicationHelper::stringURLSafe($item->title . '-' . $item->id);
		return $item;
	}
}
