<?php
namespace Controllers;

class Index extends AppController{
    
    public function index_action() {
        $this->go(DEFAULT_MODULE, 'auth');
    }
    
    public function confirm() {
        
    }
}
