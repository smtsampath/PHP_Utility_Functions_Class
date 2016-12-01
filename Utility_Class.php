<?php

/**
 * @author Thushara Sathkumara
 * 
 */

class Utility_Class {

    ############ Find the position of the first occurrence of a substring in a string ############
    public static function strpos_arr($haystack, $needle) {
        if (!is_array($needle))
            $needle = array($needle);
        foreach ($needle as $what) {
            if (($pos = strpos($haystack, $what)) !== false)
                return $pos;
        }
        return false;
    }
    ##############################################################################################
    
    ######################## Calculate the difference between two dates  #########################
    public static function getDateDiffCount($date1, $date2) {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
        $interval = $date1->diff($date2);
        if($interval->days == 0) {
            $diff = 1;
        } else {
            $diff = $interval->days;
        }
        return $diff;
    }
    ##############################################################################################
    
    ########################### Get the list of dates between two dates  #########################
    public static function getListofDatesBetweenTwoDates($date_from, $date_to) {
        // Convert date to a UNIX timestamp
        $date_from = strtotime($date_from);
        $date_to = strtotime($date_to);
        $list = array();
        for ($i = $date_from; $i <= $date_to; $i+=86400) {
            $list[] = date('m/d/Y', $i);
        }
        $date_range = implode(",", $list);
        return $date_range;
    }
    ##############################################################################################
    
    ############################### Find the latitude and longitude ##############################
    public static function getCoordinates($address) {
        //Formatted address
        $formattedAddr = str_replace(' ', '+', $address);
        //Send request and receive json data by address
        $geocodeFromAddr = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&sensor=true_or_false');
        $json = json_decode($geocodeFromAddr);
        if (isset($json->results)) {
            //Get latitude and longitute from json data
            $latitude = $json->results[0]->geometry->location->lat;
            $longitude = $json->results[0]->geometry->location->lng;
            return $latitude . "," . $longitude;
        }
        return false;
    }
    ##############################################################################################
    
    ######################## Find the location data from address and type ########################
    # Types
    const latitude_longitude = 0;
    const neighborhood = 1;
    const city = 2;
    const state = 3;
    const zipcode = 4;
    const country = 5;

    public static function getGeOLocationInfo($address, $type) {

        if (!empty($address)) {
            //Formatted address
            $formattedAddr = str_replace(' ', '+', $address);
            //Send request and receive json data by address
            $url = "http://maps.googleapis.com/maps/api/geocode/json?address=$formattedAddr&sensor=false";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $results = curl_exec($ch);
            curl_close($ch);

            $output1 = json_decode($results);
            //Get latitude and longitute from json data
            if (count($output1->results) > 0) {
                $latitude = $output1->results[0]->geometry->location->lat;
                $longitude = $output1->results[0]->geometry->location->lng;
                if ($type == static::latitude_longitude) {
                    return $latitude . ',' . $longitude;
                }
                if (!empty($output1)) {
                    $addressComponents = $output1->results[0]->address_components;
                    $response = "";
                    for ($x = 0; $x < count($addressComponents); $x++) {
                        $row = $addressComponents[$x];
                        if ($type == static::neighborhood) {
                            if ($row->types[0] == 'neighborhood') {
                                $response = $row->long_name;
                                return $response;
                            }
                        }
                        if ($type == static::city) {
                            if ($row->types[0] == 'locality') {
                                $response = $row->long_name;
                                return $response;
                            }
                        }
                        if ($type == static::state) {
                            if ($row->types[0] == 'administrative_area_level_1') {
                                $response = $row->short_name;
                                return $response;
                            }
                        }
                        if ($type == static::zipcode) {
                            if ($row->types[0] == 'postal_code') {
                                $response = $row->long_name;
                                return $response;
                            }
                        }
                        if ($type == static::country) {
                            if ($row->types[0] == 'country') {
                                $response = $row->short_name;
                                return $response;
                            }
                        }
                    }
                } else {
                    return false;
                }
            } else {
                return "";
            }
        } else {
            return false;
        }
    }
    ##############################################################################################
    
    #################### Find the Timezone Offset From latitude and Longitude ####################
    public static function gettimeZoneOffset($lat, $lng) {
        $timestamp = (int) time();

        $timeZoneFromAddr = file_get_contents('https://maps.googleapis.com/maps/api/timezone/json?location' . $lat . ',' . $lng . '&timestamp=' . $timestamp);
        $json = json_decode($timeZoneFromAddr);
        if (isset($json->rawOffset)) {
            //Get latitude and longitute from json data
            $timeZoneOffset = $json->rawOffset;
            return $timeZoneOffset;
        }
        return false;
    }
    ##############################################################################################
    
    ############################### Generate Random Product SKU ##################################
    public static function SKU_gen($string, $id = null, $length){
        $results = ''; // empty string
        $vowels = array('a', 'e', 'i', 'o', 'u', 'y'); // vowels
        preg_match_all('/[A-Z][a-z]*/', ucfirst($string), $m); // Match every word that begins with a capital letter, added ucfirst() in case there is no uppercase letter
        foreach($m[0] as $substring){
            $substring = str_replace($vowels, '', strtolower($substring)); // String to lower case and remove all vowels
            $results .= preg_replace('/([a-z]{'.$length.'})(.*)/', '$1', $substring); // Extract the first N letters.
        }
        $results .= '-'. str_pad($id, 4, 0, STR_PAD_LEFT); // Add the ID
        return $results;
    }

    ##############################################################################################
    
    ################################## Generate Random Number ####################################
    public static function random_num($length) {
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }
    ##############################################################################################

}
