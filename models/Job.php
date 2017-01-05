<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Multiple\Frontend\Models;

/**
 * Description of Job
 *
 * @author Theophilus Alamu <theophilus.alamu at gmail.com>
 */
class Job extends BaseModel{
    public function initialize(){
        $this->setSource('jobs');
    }
}
