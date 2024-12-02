<?php 

namespace App\Filters;

use Codeigniter\HTTP\RequestInterface;
use Codeigniter\HTTP\ResponseInterface;
use Codeigniter\Filters\FilterInterface;

class Sessionfilter implements FilterInterface{
    public function before(RequestInterface $request, $arguments=null){
        $session = session();
        if(!$session->has('email')){
            return redirect()->to(base_url("/"));
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null){

    }
}

?>