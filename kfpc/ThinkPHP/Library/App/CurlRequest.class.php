<?php
namespace App;
class CurlRequest{
	public $url         = '';  
    public $method      = 'GET';  
    public $post_data   = null;  
    public $headers     = null;  
    public $options     = null;  
    /** 
     *  
     * @param string $url 
     * @param string $method 
     * @param string $post_data 
     * @param string $headers 
     * @param array $options 
     * @return void 
     */  
    public function __construct($url, $method = 'GET', $post_data = null, $headers = null, $options = null) {  
        $this->url = $url;  
        $this->method = strtoupper( $method );  
        $this->post_data = $post_data;  
        $this->headers = $headers;  
        $this->options = $options;  
    }  
    /** 
     * @return void 
     */  
    public function __destruct() {  
        unset ( $this->url, $this->method, $this->post_data, $this->headers, $this->options );  
    }  
}  

?>