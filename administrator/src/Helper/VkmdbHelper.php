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

use Joomla\CMS\Helper\ContentHelper;

/**
 * Vkmdb component helper.
 *
 * @since  1.0.0
 */
class VkmdbHelper extends ContentHelper
{
    protected $apiKey;//TODO:Componentenparameter einsetzen
    protected $prescreenBaseURL = 'https://api.prescreenapp.io/v1/';  //TODO:Componentenparameter einsetzen
    public function __construct(){
        $this->apiKey = esc_html( get_option( 'apiKey' ) ); //TODO:Componentenparameter einsetzen esc_html( get_option( 'apiKey' ) ) umbenennen durch Componentenparameter
    }

    /**
     * Get Data from the Ninox API
     *
     * @param string $type // contacts, companies, jobs, relations
     * @param string $method // GET, POST, DELETE, PUT
     * @param array $parameters
     *
     * @return object $results
     */

    public function ninoxApi($type, $method, $parameters){
        $curl = curl_init();

        $url = ""; // TODO:Componentenparameter einsetzen

        $curlOptions =  [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,s
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                'apiKey: ' . $this->apiKey,
                'Content-Type: text/plain'
            ),
            CURLINFO_HEADER_OUT => true,

        ];

        switch ([$method,$type]) {
            // Write Candidate
            case ['POST','candidate']:
                $curlOptions[10015] = $parameters;
                break;
            // PAtch application
            case ['PATCH','application']:
                $curlOptions[10015] = $parameters;
                break;
            // Post application
            case ['POST','application']:
                $curlOptions = [
                    CURLOPT_URL => 'https://api.prescreenapp.io/v1/application',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>   $parameters,
                    CURLOPT_HTTPHEADER => array(
                        'apikey: ' . $this->apiKey,
                        'Content-Type: text/plain'
                    ),
                ];
                break;
            // Get candidate
            case ['GET','candidate']:
                $url = $url . 'email=' . $parameters['email'];
                $curlOptions['CURLOPT_URL'] = $url;
                break;

        }

        curl_setopt_array($curl, $curlOptions);

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

}

