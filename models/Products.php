<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Products
 *
 * @author Theophilus
 */
class Products extends BaseModel{
    //put your code here
    /**
     * Built for the json returned result for Added Vendor
     * {type:vendor, id:1} formatted
     * @param type $string
     * @return type
     */
    public static function __getAddress($string, $key=''){
        $stackProduct   = Products::__convert($string, $key);
        return $stackProduct;
    }
    
    /**
     * Built for the json returned result for Added Vendor
     * {type:vendor, id:1} formatted
     * @param type $string
     * @return type
     */
    public static function __getNextAddress($string, $key=''){
        $stackProduct   = Products::__convert($string, $key);
        return $stackProduct;
    }

    
    /**
     * This is for the purpose of the string format
     * {type:vendor, id:1}
     * @param type $string
     */
    public static function __convert($string, $key=''){
        $product        = new Products();
        $strJsonDecode  = json_decode($string);
        $sqlStatement   = "SELECT * FROM vendor "
                                        . "WHERE vendor_id=".$strJsonDecode->id;
        $result         = $product->getReadConnection()->query($sqlStatement);
                          $result->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return empty($key) ? $result->fetch() : $result->fetch()[$key];
    }
}
