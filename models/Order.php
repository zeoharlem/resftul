<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderController
 *
 * @author Theophilus
 */

class Order extends BaseModel{
    //put your code here
    public function initialize(){
        $this->allowEmptyStringValues(array(
            'company','additional_info'));
    }
}
