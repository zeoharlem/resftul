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
    
    public function initialize() {
        parent::initialize();
    }
    
    public function index(){
        $getProduct = Products::find(array(
                        'order'=>'RAND()'))->toArray();
        $typeRes    = new Phalcon\Http\Response();
        $this->__setJSON($getProduct);
    }
    
    public function order($param){
        $this->__setJSON($this->__setBasket($param));
    }
    
    public function basket($param){
        $this->__setJSON($this->__setBasket($param));
    }
    
    public function remove($param){
        $this->__setJSON($this->__remove($param));
    }
    
    private function __remove($flow){
        $getItemProduct = $flow;
        if($this->session->has('cart_item') || !empty($_SESSION['cart_item'])){
            foreach ($this->session->get('cart_item') as $keys => $values){
                if($getItemProduct == $keys){
                    unset($_SESSION['cart_item'][$keys]);
                }
                if(empty($_SESSION['cart_item'])){
                    $this->session->remove('cart_item');
                }
            }
        }
        return $this->session->get('cart_item');
    }
    
    private function __setJSON($data){
        $typeRes    = new Phalcon\Http\Response();
        $typeRes->setHeader('Content-Type', 'application/json');
        $typeRes->setJsonContent(array('status'=>'OK', 'data'=>$data));
        $typeRes->sendHeaders(); $typeRes->send();
    }
    
    private function __setBasket($flow, $qty=null){
        $getItemProduct = $flow;
        $product        = Products::find(
                'product_id='.(int)$getItemProduct)->getFirst();
        
        //Check if quantity was sent with the post
        $qty            = !is_null($qty) ? $qty : 1;
        //Set Item Array Values
        $itemTray   = array(
            $getItemProduct => array(
                'name'  => $product->title, 
                'id'    => $getItemProduct, 
                'qty'   => $qty, 'option' => '', 
                'price' => $product->sale_price,

                //product->added_by returns objects
                'vendor_id' => $product->added_by,
                //_____ ______ ______ _____ _____ ____
                'shipping' => 0, 'tax' => 0, 'coupon' => '', 
                'image'     => $product->front_image,
                'subtotal'  => $product->sale_price,
                'address'   => Products::__getAddress($product->added_by, 'address2'),
                'addedby'   => Products::__convert($product->added_by, 'display_name'),
                'location'  => Products::__getAddress($product->added_by, 'address1')
            )
        );
        if($this->session->has('cart_item') || !empty($_SESSION['cart_item'])){
            if(array_key_exists($getItemProduct, $this->session->get('cart_item'))){
                foreach($this->session->get('cart_item') as $keys => $values){
                    if($getItemProduct == $keys){
                        //Calculate the total price and assign to the session var
                        $pTaskCounter   = (int)$this->session->get('cart_item')[$getItemProduct]['qty'] + 1;
                        $_SESSION['cart_item'][$keys]['qty']                                = $pTaskCounter;
                    }
                }
            }
            else{
                //Do not use array_merge() cos it will reassign the key value(index);
                $this->session->set('cart_item',$this->session->get('cart_item') + $itemTray);
            }
        }
        else{
            $this->session->set('cart_item', $itemTray);
        }
        //echo count($this->session->get('cart_item'));
        return $this->session->get('cart_item');
    }
}
