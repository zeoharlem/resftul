<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author Theophilus Alamu <theophilus.alamu at gmail.com>
 */
namespace Multiple\Frontend\Models;

class Users extends BaseModel{
    //put your code here
    const ErrorTypePO   = 'PresenceOf';
    const ErrorTypeICA  = 'InvalidCreateAttempt';
    const ErrorTypeIUA  = 'InvalidUpdateAttempt';
    
    public function getMessages($filter=NULL) {
        $messages   = array();
        foreach(parent::getMessages() as $message){
            switch ($message->getType()) {
                case self::ErrorTypeICA:
                    $messages[] = 'The record cannot be created because it already exists';
                    break;
                case self::ErrorTypeIUA:
                    $messages[] = 'The record cannot be updated because it doesn\'t exist';
                    break;
                case self::ErrorTypePO:
                    $messages[] = 'The field ' . $message->getField() . ' is mandatory';
                    break;
            }
        }
    }
}
