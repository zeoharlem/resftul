<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Register
 *
 * @author Theophilus Alamu <theophilus.alamu at gmail.com>
 */
namespace Multiple\Frontend\Models;

use Phalcon\Validation\Validator;

class Register extends BaseModel{
    //put your code here
    public function beforeValidationOnCreate(){
        $this->role     = "user";
        $this->codename = $this->getDI()->get('component')->helper->makeRandomInts(11);
    }
    
    public function validation(){
        $security   = new Phalcon\Security();
        $validation = new \Phalcon\Validation();
        $validation->add('email', new Validator\Email(array(
            'model'     => $this,
            'message'   => 'Please enter correct email address'
        )));
        $this->password = $security->hash($this->password);
        return $this->validate($validation);
    }
}
