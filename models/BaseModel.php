<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseModel
 *
 * @author web
 * Note: Please methods starting with __postRaw has no advantage
 * It's just used to monitor methods written by the theophilus for brevity
 * most times the __ are mostly private assessors
 */

//namespace Multiple\Frontend\Models;

class BaseModel extends \Phalcon\Mvc\Model{
    const MODEL_REPLACE = 'REPLACE', MODEL_INSERT = 'INSERT';
    /**
     * @param array $array
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post Associative arrays
     */
    public function __postRawArray(array $array, $table, $others=''){
        $sqlState = "INSERT INTO $table SET ";
        foreach($array as $key => $value){
            $_stmtArrayQuery[] = $key."='".$value."'";
        }
        $sqlState .= implode(", ", $_stmtArrayQuery);
        $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
        $return = $this->getReadConnection()->execute($sqlQueryNow);
        return $return;
    }
    
    /**
     * @param array $arrays
     * @param type $table
     * @param type $others
     * @return array
     * @covers className::__postIntKeyRaw($arrays, $table, $others);
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     */
    public function __postIntKeyRaw(array $arrays, $table, $others=''){
        foreach($arrays as $keys => $values){
            $sqlState = "INSERT INTO $table SET ";
            foreach($values as $index => $element){
                $_stmtArrayQuery[] = $index."='".$element."'";
            }
            $sqlState .= implode(", ", $_stmtArrayQuery);
            $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
            $result = $this->getReadConnection()->execute($sqlQueryNow);
            $_stmtArrayQuery = array();
            //var_dump($sqlQueryNow);
        }
        return $result;
    }
    
    /**
     * @param array $arrays
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post a multi-dimensional array
     */
    public function __postMultiRaw(array $arrays, $table, $others=''){
        foreach($arrays as $keys => $values){
            for($x = 0; $x < count($values); $x++){
                @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = NULL;
            $sqlQuery = 'INSERT INTO '.$table.' SET ';
            foreach($element as $textNotify){
                $sqlArrayQuery[] = $textNotify;
            }
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    /**
     * @param array $arrays
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post a mixture of multi-dimensional array and 
     * Associative array values
     */
    public function __postRawSQLTask(array $arrays, $table, 
            $others = '', $action = self::MODEL_INSERT){
        foreach($arrays as $keys => $values){
            if(isset($values) && is_array($values)){
                for($x = 0; $x < count($values); $x++){
                    @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
                }
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = NULL;
            $sqlQuery = $action.' INTO '.$table.' SET ';
            foreach($element as $textNotify){
                $sqlArrayQuery[] = $textNotify;
            }
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    /**
     * @param array $arrays
     * @param string $table
     * @param string $others
     * @return array
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Post a mixture of multi-dimensional array and 
     * Associative array values
     */
    public function __postDuplicateRawSQL(array $arrays, $table){
        foreach($arrays as $keys => $values){
            if(isset($values) && is_array($values)){
                for($x = 0; $x < count($values); $x++){
                    @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
                }
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = NULL;
            $sqlQuery = 'INSERT INTO '.$table.' SET ';
            foreach($element as $textNotify){
                $_otherInfo[] = $textNotify;
                $sqlArrayQuery[] = $textNotify;
            }
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfo);
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    /**
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Use Raw Replace Structured Query Language
     * Associative Array replace Post
     * @param array $arrays
     * @param type $table
     * @return type
     */
    public function __replaceRawSQL(array $arrays, $table){
        foreach($arrays as $keys => $values){
            $sqlState = "INSERT INTO $table SET ";
            
            //Raw Replace Structured Query Language
            foreach($values as $index => $element){
                $_otherInfo[] = $index."='".strtolower($element)."'";
                $_stmtArrayQuery[] = $index."='".strtolower($element)."'";
            }
            $sqlState .= implode(", ", $_stmtArrayQuery);
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfo);
            $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
            $result = $this->getReadConnection()->execute($sqlQueryNow);
            $_stmtArrayQuery = array();
        }
        return $result;
    }
    
    /**
     * @author Theophilus Alamu <zeoharlem@yahoo.co.uk>
     * Replace Multi Dimensional Array
     * @param array $arrays
     * @param type $table
     * @return type
     */
    public function __replaceMultiRaw(array $arrays, $table){
        foreach($arrays as $keys => $values){
            for($x = 0; $x < count($values); $x++){
                @$stmtArrayQuery[$x][] = $keys . '="' .$values[$x].'"';
            }
        }
        foreach($stmtArrayQuery as $index => $element){
            $sqlArrayQuery = array();
            $sqlQuery = 'INSERT INTO '.$table.' SET ';
            foreach($element as $key => $textNotify){
                $_otherInfos[$key] = $textNotify;
                $sqlArrayQuery[] = $textNotify;
            }
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfos);
            $sqlQuery .= implode(", ", $sqlArrayQuery) . $others;
            $result = $this->getReadConnection()->execute($sqlQuery);
            //var_dump($sqlQuery);
        }
        return $result;
    }
    
    //Excel Purpose for uploading
    public function __excelPostArray(array $arrays, $table, $others=''){
        foreach($arrays[0] as $element){
            $columns[] = $element;
        }
        //Remove the first key
        unset($arrays[0]);
        
        //Iterate throught the adjusted array
        foreach($arrays as $keys => $values){
            $sqlState = "INSERT INTO $table SET ";
            foreach($values as $index => $element){
                $_stmtArrayQuery[] = $columns[$index]."='".$element."'";
                $_otherInfo[] = $columns[$index]."='".strtolower($element)."'";
            }
            $sqlState .= implode(", ", $_stmtArrayQuery);
            $others = ' ON DUPLICATE KEY UPDATE '.implode(", ", $_otherInfo);
            $sqlQueryNow = !empty($others) ? $sqlState.$others : $sqlState;
            $result = $this->getReadConnection()->execute($sqlQueryNow);
            $_stmtArrayQuery = array();
            //var_dump($sqlQueryNow);
        }
        return $result;
    }
}
