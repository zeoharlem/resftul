<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PostController
 *
 * @author Theophilus
 */
class PostController extends BaseController{
    
    public function index(){
        $getProduct = Products::find(array(
                        'order'=>'RAND()'))->toArray();
        $typeRes    = new Phalcon\Http\Response();
        if($getProduct == true){
            $typeRes->setHeader('Content-Type', 'application/json');
            $typeRes->setJsonContent(array('status'=>'OK','data'=>$getProduct));
            $typeRes->sendHeaders(); $typeRes->send();
        }
    }
    
    public function orderNow(){
        
    }
    
    protected function setBasket(){
        
    }
}
