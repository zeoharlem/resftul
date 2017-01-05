<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseController
 *
 * @author Theophilus Alamu <theophilus.alamu at gmail.com>
 */
//namespace Multiple\Frontend\Controllers;

use Phalcon\Mvc\Controller;
class BaseController extends Controller{
    //put your code here
    public function initialize(){
        //var_dump($this->mailer); exit;
        \Phalcon\Tag::prependTitle('Sales Application');
        \Phalcon\Tag::setTitleSeparator(' | ');
        $this->assets->collection('headers')
                ->addCss('assets/css/bootstrap.min.css')
                ->addCss('assets/css/main.css')
                ->addCss('assets/css/red.css')
                ->addCss('assets/css/owl.carousel.css')
                ->addCss('assets/css/owl.transitions.css')
                ->addCss('assets/css/owl.theme.css')
                ->addCss('assets/css/animate.min.css')
                ->addCss('assets/css/font-awesome.min.css')
                ->addCss('http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800', true);
        
        $this->view->setVar('url', $this->__locateString());
        $this->view->setVar('category', \Multiple\Frontend\Models\Category::find()->toArray());
        //$this->view->setVar('taskAgents', json_decode($this->component->helper->getAgents()));
        $this->view->setVar('taskAgents', \Multiple\Frontend\Models\Admin::find());
        //$agents = json_decode($this->component->helper->getAgents());
        //var_dump($agents->data.fleet_image); exit;
        //Create | add the javascript script link
        $this->assets->collection('jsHeaders')
                ->addJs('assets/vendor/modernizr/modernizr.js')
                ->addJs('assets/vendor/breakpoints/breakpoints.js');
        
     
        //Create | add the javascript script link
        $this->assets->collection('footers')
                ->addJs('assets/js/jquery-1.10.2.min.js')
                ->addJs('assets/js/jquery-migrate-1.2.1.js')
                ->addJs('assets/js/bootstrap.min.js')
                ->addJs('assets/js/bootstrap-hover-dropdown.min.js')
                ->addJs('assets/js/owl.carousel.min.js')
                ->addJs('assets/js/bootbox.js')
                ->addJs('assets/js/css_browser_selector.min.js')
                ->addJs('assets/js/echo.min.js')
                ->addJs('assets/js/jquery.easing-1.3.min.js')
                ->addJs('assets/js/bootstrap-slider.min.js')
                ->addJs('assets/js/jquery.raty.min.js')
                ->addJs('assets/js/jquery.prettyPhoto.min.js')
                ->addJs('assets/js/jquery.customSelect.min.js')
                ->addJs('assets/js/wow.min.js')
                ->addJs('assets/js/scripts.js')
                ->addJs('assets/js/app.js');
    }
    
    public function __dataTableJsCss(){
        $this->assets->collection('headers')->addCss(
                'assets/js/datatables/datatables.css');
        $this->assets->collection('footers')->addJs(
                'assets/js/datatables/jquery.dataTables.js');
    }
    
    protected function __crossOriginPolicy(){
        $response   = new Phalcon\Http\Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $response->setHeader('Access-Control-Allow-Headers', 'Authorization');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST');
        $response->sendHeaders();
    }


    protected function __dataTableFlow($builder){
        if($this->request->isAjax()){
            $dataTables = new \DataTables\DataTable();
            return $dataTables->fromBuilder($builder)->sendResponse();
        }
    }
    
    protected function __dataTable($builder){
        if($this->request->isGet() || $this->request->isPost()){
            $dataTables = new \DataTables\DataTable();
            return $dataTables->fromBuilder($builder)->sendResponse();
        }
    }
    
    protected function __dataTableArray($builder){
        if($this->request->isAjax()){
            $dataTables = new \DataTables\DataTable();
            return $dataTables->fromArray($builder)->sendResponse();
        }
    }
    
    //This method should be used for associative array
    /**
     * @param type $array
     * @return type array;
     */
    public function __buildRequest($array = array()){
        if($this->request->isPost()){
            $_POST   = $this->request->getPost() + $array;
            return $getPost     = $this->request->getPost();
        }
        else{
            $_GET   = $this->request->getQuery() + $array;
            return $getQuery    = $this->request->getQuery();
        }
    }
    
    //Get Array Conditions to replace post or get Query
    //Note that the index 0 returned is array and 1 is strings
    //Use like this $getWhatever = $this->__getArrayCon($array);
    protected function __getArrayCon(array $array){
        $strings = '';
        $results = array();
        foreach($array as $key => $value){
            $results[$key] = $value;
            $strings .= $key.' = :'.$key.': AND ';
        }
        return array(
            $results, substr($strings,0,-4)
        );
    }
    
    //Remove empty getPost() | getQuery() request
    protected function __getPostRemoveEmpty(){
        if($this->request->isPost()){
            foreach($this->request->getPost() as $key => $value){
                if(empty($value) || is_null($value)){
                    unset($_POST[$key]);
                }
            }
        }
        else{
            foreach($this->request->getQuery() as $key => $value){
                if(empty($value) || is_null($value)){
                    unset($_GET[$key]);
                }
            }
        }
    }
    
    //This method create a binding value based
    //Empty post remooved from the getPost() returned
    protected function __bindAfterRemoveEmpty($type='post'){
        $results = array();
        switch ($type) {
            case 'post':
                foreach($this->request->getPost() as $key => $value){
                    $results[$key] = $value;
                }
                return $results;
                break;
                
            case 'get':
                foreach($this->request->getQuery() as $key => $value){
                    if($key !== '_url'){
                        $results[$key] = $value;
                    }
                }
                return $results;
                break;
        }
    }
    
    //This method creates queries of values for binding
    protected function __conditionsRemoveEmpty($type='post'){
        $strings = '';
        switch ($type) {
            case 'post':
                foreach($this->request->getPost() as $key => $value){
                    $strings .= $key.' = :'.$key.': AND ';
                }
                return substr($strings,0,-4);
                break;
                
            case 'get':
                foreach($this->request->getQuery() as $key => $value){
                    if($key !== '_url'){
                        $strings .= $key.' = :'.$key.': AND ';
                    }
                }
                return substr($strings,0,-4);
                break;
        }
    }
    
    /**
     * @return String
    //if session has cart_item
    */
    public function __getSubTotal(){
        $total  = array();
        if($this->session->has('cart_item') || isset($_SESSION['cart_item'])){
            foreach($this->session->get('cart_item') as $keys=>$values){
                $total[]    =           $values['qty'] * $values['price'];
            }
        }
        return array_sum($total);
    }
    
    //This method is for this query construction
    private function __locateString(){
        return $this->session->has('strLocation') ? 
                '?strLocation='.$this->session->get('strLocation'):'';
    }
}
