<?php

/**
 * quick submission controller
 */
class QuickSubmission extends Controller {
    /**
     * index model function when page is accessed
     * 
     * @param  array [ url GET parameters ]
     * 
     * @return void
     */
    public function index($params) {
        $this->_view('', $this->_model('QuickSubmission', $params));
    }
}