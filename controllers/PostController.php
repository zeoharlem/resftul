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
    private $_track;
    
    public function initialize() {
        parent::initialize();
        $this->_track = $this->component->helper->makeRandomInts(10);
    }
    
    /**
     * List the products avaiable
     */
    public function index(){
        $getProduct = Products::find(array(
                        'order'=>'RAND()'))->toArray();
        $typeRes    = new Phalcon\Http\Response();
        $this->__setJSON($getProduct);
    }
    
    /**
     * Place order for products here
     * @param type $param
     */
    public function order($param){
        var_dump($this->request->getPost());
        //$this->__setJSON($this->__setBasket($param));
    }
    
    /**
     * set up the cart basket
     * @param type $param
     */
    public function basket($param){
        $this->__setJSON($this->__setBasket($param));
        //var_dump($this->component->helper->makeRandomInts(10));
    }
    
    /**
     * remove a key or basket off shore
     * @param type $param
     */
    public function remove($param){
        $this->__setJSON($this->__remove($param));
    }
    
    /**
     * Clear all the baskets
     */
    public function clear(){
        $this->session->remove('cart_item');
        $this->__setJSON($this->session->get('cart_item'));
    }
    
    public function create(){
        
    }
    
    /**
     * set up a product removal by id
     * @param type $flow
     * @return type
     */
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
    
    /**
     * set up json data type
     * @param type $data
     */
    private function __setJSON($data){
        $typeRes    = new Phalcon\Http\Response();
        $typeRes->setHeader('Content-Type', 'application/json');
        $typeRes->setJsonContent(array('status'=>'OK', 'data'=>$data));
        $typeRes->sendHeaders(); $typeRes->send();
    }
    
    /**
     * create a cart basket method
     * @param type $flow
     * @param type $qty
     * @return type
     */
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
        return $this->session->get('cart_item');
    }
    
    /**
     * #start order request from this region #
     */
    
    /**
     * Place the order here
     * This should be an ajax call
     */
    public function startAction(){
        $dbError    = '';
        $tracker    = TRUE;
        $response   = new \Phalcon\Http\Response();
        $track_id   = $this->request->getPost('trans_id');
        
        if($this->request->isPost() && $this->request->isAjax()){
            if($this->session->has('cart_item') && !empty($_SESSION['cart_item'])){
                try {
                    $transMangager  = new Manager();
                    $transaction    = $transMangager->get();
                    $order          = new Order();
                    $order->setTransaction($transaction);
                    $default        = date('Y-m-d');
                    $this->__buildRequest(array(
                        'trans_id'  => $track_id, 'vendor_id' => 1));
                    
                    //Setup the order insert which is the post field
                    if($order->save($this->request->getPost()) == FALSE){
                        $tracker    = FALSE;
                        $transaction->rollback("Order(s) cannot be placed");
                        $dbError    = $order->getMessages();
                    }
                    
                    //Setup the sales insert which is session.get(cart_item);
                    $sales          = new Sales();
                    $sales->setTransaction($transaction);
                    $this->__taskSessionAdd($track_id);
                    $vendorSales    = json_encode($this->session->get('cart_item'));
                    $hourLater      = strtotime(date('Y-m-d H:i:s')) + 60 * 60; 
                    $startSales     = array(
                        'trans_id'      => $track_id,
                        'date_of_order' => date('Y-m-d H:i:s'),
                        'item_sold'     => json_encode($this->session->get('cart_item')),
                        'vendor_id'     => $this->session->get('auth')['codename'],
                        'delivery_time' => date('Y-m-d H:i:s', $hourLater),
                        'status'        => 'pending',
                        'agent'         => 20396,
                    );
                    
                    if($sales->save($startSales) == FALSE){
                        $tracker    = FALSE;
                        $transaction->rollback("Sale(s) cannot be performed");
                        $dbError    = $transaction->getMessages();
                    }
                    $transaction->commit();
                } catch (Failed $exc) {
                    $tracker    = false;
                    $this->flash->error('error: '. $exc->getMessage());
                    $response->setJsonContent(array(
                        'status'    => $tracker,
                        'message'   => $exc->getMessage(),
                        'dbaseerr'  => $order->getMessages()));
                    $exc->getTraceAsString();
                }
            }
        }
        if($tracker){
            $tasking        = array(
                'team_id'   => 9896,
                'agent_id'  => 20396,
                //'agent_id'  => $track_id['fleet_id'],
            );
            $customer       = $this->request->getPost();
            $customerRes    = $this->__createTask($tasking, $customer);
            $trans_id       = array('trans_id' => $customer['trans_id']);
            $customerJob    = $this->jobFlow($trans_id + $customerRes['data']);
            
            $response->setJsonContent(array(
                'status'    => $tracker,
                'data'      => $this->request->getPost(),
                'tookan'    => $customerRes,
                'job'       => $customerJob
            ));
        }
        $response->setHeader('Content-Type', 'application/json');
        $response->send();        exit();
    }
    
    
    /**
     * Mind you this method must be used with a post request
     * @return type
     */
    public function jobFlow($jobTrack){
        $response   = new \Phalcon\Http\Response();
        $job    = new \Multiple\Frontend\Models\Job();
        return $job->create($jobTrack);
    }
    
    /**
     * using the API url v2
     * $customer variable must be array
     * array(email,lastname,phonenumber,address);
     * @param type $team_id
     * @param array $customer
     * @return type
     */
    public function __createTask($task_id, array $customer){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.tookanapp.com/v2/create_task");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
            \"api_key\": \"".self::ACESS_TOKEN."\",
            \"order_id\": \"".$customer['trans_id']."\",
            \"team_id\": \"".$task_id['team_id']."\",
            \"auto_assignment\": \"0\",
            \"job_description\": \"Delivery\",
            \"customer_email\": \"".$customer['email']."\",
            \"customer_username\": \"".$customer['lastname']." ".$customer['firstname']."\",
            \"customer_phone\": \"".$customer['phonenumber']."\",
            \"customer_address\": \"".$customer['address']."\",
            \"latitude\": \"\",
            \"longitude\": \"\",
            \"job_delivery_datetime\": \"".date('m/d/Y H:i:s', strtotime(('+1 hour')))." \",
            \"has_pickup\": \"0\",
            \"has_delivery\": \"1\",
            \"layout_type\": \"0\",
            \"tracking_link\": 1,
            \"timezone\": \"+1\",
            \"custom_field_template\": \"\",
            \"meta_data\": [
              {
                \"label\": \"\",
                \"data\": \"\"
              }
            ],
            \"fleet_id\": \"".$task_id['agent_id']."\",
            \"ref_images\": [
              \"http://tookanapp.com/wp-content/uploads/2015/11/logo_dark.png\",
              \"http://tookanapp.com/wp-content/uploads/2015/11/logo_dark.png\"
            ],
            \"notify\": 1,
            \"tags\": \"\",
            \"geofence\": 0
        }");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));
        $response   = curl_exec($ch);
        $returns    = json_decode($response, TRUE);
        curl_close($ch); return $returns;
    }
    
    /**
     * 
     * @param type $url
     * @param string $token
     * @param string $jsonString
     * @return string
     */
    private function __curlRequestTask($url, $jsonString){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_POST, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-Type: application/json"
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    /**
     * @return string
     * Get the addresses of the shops
     */
    private function __getStringAddress(){
        return join('; ', array_keys($this->__getShopsTask('address')));
    }
    
    /**
     * @return json
     */
    private function __getAvailableFleets(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.tookanapp.com/view_team");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{
            \"access_token\": \"e824c4f685bca92ed63ffd522a855f52\"
        }");
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        curl_close($ch);
        return $response;
    }
    
    //comma separated shops owner's name
    private function __stringifyJobOwner(){
        $stringify  = array_keys($this->__getShopsTask('addedby'));
        return join(',', $stringify);
    }
    
    //comma separated shops owner's name
    private function __stringifyJobOwnerAddress(){
        $stringify  = array_keys($this->__getShopsTask('address2'));
        return join(',', $stringify);
    }
    
    /**
     * @param type $tracker
     * force trans_id into the session array
     */
    private function __taskSessionAdd($tracker){
        if($this->session->has('cart_item') || isset($_SESSION['cart_item'])){
            foreach($this->session->get('cart_item') as $keys=>$values){
                $_SESSION['cart_item'][$keys]['trans_id']   = $tracker;
            }
        }
    }
    
    /**
     * @param string $key
     * @return array
     * Group array with the same value together
     */
    private function __getShopsTask($key=''){
        $return = array();
        foreach($this->session->get('cart_item') as $keys=>$values){
            $return[$values[$key]][]   = $values;
        }
        return $return;
    }
    
}
