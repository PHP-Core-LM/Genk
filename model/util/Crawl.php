<?php
/**
 * Created by PhpStorm
 * Author: Lê Minh Hổ
 * Date: 4/12/2019
 * Time: 10:31 AM
 */

include_once "simple_html_dom.php";

class Crawl
{
    /**
     * Get sourcecode html from html
     * @param $url
     * @return bool | simple_html_dom
     */
    function getSourceHTML($url)
    {
        $cURL = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0
        );
        curl_setopt_array($cURL, $options);
        set_time_limit(60);
        $result = curl_exec($cURL);
        curl_close($cURL);
        return str_get_html($result);
    }


    /**
     * Get all source code html from list url
     * @param $urls
     * @return array
     */
    function getAllSourceHTML($urls)
    {
        $result = array();
        foreach ($urls as $url)
            $result[] = $this->getSourceHTML($url);
        return $result;
    }
}