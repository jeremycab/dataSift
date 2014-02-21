<?php

namespace DataSift\TestBundle\Http;

class HttpRequest {
    private $query;
    
    public function __construct($queryString)
    {
        $this->query = $queryString;
        var_dump(explode("\n", $this->query));
    }
}
