<?php

class Scale 
{
    private  $vendor_model_list=array("TEST"=>array("TEST1","TEST2"),
                                      "MERA"=>array("V100","V200")
                                            ); 
    private  $scale_type_list=array("TCP","WEB");
    
    private        $vendor="";
    private        $model="";
    private        $scale_type="";
    private        $scale_ip="";
    private        $scale_port="";
    private        $socket_connection_timeout="5";
    private        $curl_connecttion_timeout="5";

    public function __construct()
    {}
    
      
    public function initialize($vendor,$model,$type,$ip,$port)
    {
        $error_description="Scale->initialize error: ";
        if (in_array($model,$this->vendor_model_list[$vendor])) {
        $this->vendor=$vendor;
        $this->model=$model;
        }
        else {
            throw new Exception($error_description."Undefined scale vendor/model: ".$vendor."-".$model);
        }
        if (in_array($type,$this->scale_type_list)) {
            $this->scale_type=$type;
        }
        else {
            throw new Exception($error_description.$type." is wrong scale type");
        }
        
        if (preg_match("/^[0-9]{1,3}(\.[0-9]{1,3}){3}\$/",trim($ip))) {
            $this->scale_ip=trim($ip);
        }
        else {
            throw new Exception($error_description.$ip." is not valid IP address");
        }
        if ($port > 0 and $port < 65536) {
            $this->scale_port=$port;
        }
        else {
            throw new Exception($error_description.$port." is not valid IP port (range 1-65535)");
        }
        
    }
    
    public function initializeFromDB($connection_string,$id)
    {
        $error_description="Scale->initializeFromDB error: ";
        throw new Exception($error_description."Method is not implemented yet");
    }

      
    public function getWeight()
    {
        $error_description_no_init="Scale->getWeight error: Scale is not initialized";
        $error_description_socket="Scale->getWeight error: Error while opening socket";
        $error_description_curl="Scale->getWeight error: Curl error :";
        $web_connection_url="";
        
        switch($this->vendor) {
            case "MERA":
                switch($this->model) {
                    case "V100" : if ($this->scale_type=="TCP"){
                                        $fp=fsockopen($this->scale_ip,$this->scale_port, $socket_error_no, $socket_error_str, $this->socket_connection_timeout);
                                        if (!$fp) { 
                                            throw new Exception($error_description_socket." ".$socket_error_str."(".$socket_error_no.")");
                                        }
                                        else {
                                            fputs($fp,PHP_EOL);
                                            $result = fgets($fp,21);
                                            fclose($fp);
                                            $result = $result." (data received from the socket)";
                                            return $result;
                                        }
                                         
                                  }
                                  if ($this->scale_type=="WEB"){
                                        $web_connection_url="http://".$this->scale_ip.":".$this->scale_port."/weight.html";
                                        $ch = curl_init($web_connection_url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_HEADER, false);
                                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curl_connecttion_timeout); 
                                        $contents = curl_exec( $ch );
                                        $curl_error = curl_errno( $ch );
                                        $curl_error_msg  = curl_error( $ch );
                                        curl_close( $ch );
                                        if ($contents===false) {
                                              throw new Exception($error_description_curl." ".$curl_error." ".$curl_error_msg);
                                        }
                                        else {
                                              $contents=$contents." (data received from the web)";
                                              return $contents;
                                        }
                                                                 
                                  }
                                 
                    case "V200" : return "V200";
                    
                    default: throw new Exception($error_description_no_init);
                                  
                }
            case "TEST":
                switch($this->model) {
                    case "TEST1": return "1.000";
                        
                    case "TEST2": return "2.000";
                    
                    default: throw new Exception($error_description_no_init);
                    
                }
            default : throw new Exception($error_description_no_init);
            
           return "-1";
        }
        
    }
    
    public function getScaleDescription()
    {
        $description = "Scale vendor   : ".$this->vendor.PHP_EOL.
                       "Scale model    : ".$this->model.PHP_EOL.
                       "Scale type     : ".$this->scale_type.PHP_EOL.
                       "Scale IP_ADDR  : ".$this->scale_ip.PHP_EOL.
                       "Scale IP_PORT  : ".$this->scale_port.PHP_EOL.
                       "Socket timeout : ".$this->socket_connection_timeout.PHP_EOL.
                       "cURL timeout   : ".$this->curl_connecttion_timeout.PHP_EOL;
        return nl2br($description);
    }
    
    public function setSocketConnectionTimeout($timeout)
    {
        $this->socket_connection_timeout=$timeout;
    }
    
    public function setCurlConnectionTimeout($timeout)
    {
        $this->curl_connection_timeout=$timeout;
    }
    
            
    function __destruct()
    {}
}


