<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Category
 *
 * @author Theophilus Alamu <theophilus.alamu at gmail.com>
 */
namespace Multiple\Frontend\Models;

class Category extends BaseModel{
    //put your code here
    
    public function initialize() {
        $this->belongsTo(
                "category_id", 
                "Multiple\\Frontend\\Models\\Products", 
                "category",
                array('reusable' => true, 'alias' => 'categoryPro'));
    }
    
    //A fix for the namespacing attributes
    public function getCategory(){
        return $this->getRelated("Multiple\Frontend\Models\Category");
    }
}
