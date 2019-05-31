<?php
namespace lrnzfrr\CakeSlimImage\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * CakeSlimImage middleware
 * process image from slim image cropper
 * for ajax or api service
 */
class CakeSlimImageMiddleware
{
    /**
     * Invoke method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Message\ResponseInterface $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return \Psr\Http\Message\ResponseInterface A response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $jsonData = $request->input('json_decode',true);
        if($jsonData && isset($jsonData['slim'])) {
            $request = $this->processRequest($request);
        }
        return $next($request, $response);
    }

    /**
     * processRequest
     *
     * @param  mixed $request
     * Process request
     * @return void
     */
    public function processRequest(ServerRequestInterface $request) {

        $requestData =  $this->getData($request);
  
        if(!$requestData['slim']) {
            return $request;
        }
 
        if($this->isMulti($requestData)) { // is multi image
            foreach($requestData['slim'] as $currentImgData) {
                $requestData['slimImage'][] = $this->processData( $currentImgData);
            }
        } else { // single image
            $requestData['slimImage'] = $this->processData( $requestData['slim']);
        }

        unset($requestData['slim']);
        $requestData = json_encode($requestData,true);
        $request->setInput($requestData); // deprecated change withBody 
        return  $request;
    }

    /**
     * processData
     *  process image data
     * @param  mixed $slimImageData
     *
     * @return imageData
     */
    private function processData($slimImageData) {
        $slimImageData['output']['image'] =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $slimImageData['output']['image']));
        $filename = '/tmp/' . uniqid() .time() .'_'. $slimImageData['output']['name'] ;

        file_put_contents($filename, $slimImageData['output']['image']);
     
        $imageData['tmp_name'] = $filename;
        $imageData['error'] = 0;
        $imageData['name'] = $slimImageData['output']['name'];
        $imageData['type'] = $slimImageData['output']['type'];
        $imageData['size'] = filesize($filename);
        return $imageData;
    }

    private function getData($request) {
        $requestData  = $request->input('json_decode',true);
        if($this->isMulti($requestData)) { // convert to Array
            $i = 0;
            foreach($requestData['slim'] as $currentImage) {
                $requestData['slim'][$i] = json_decode($currentImage['image'], true);
                $i++;
            }
        } else {
            $requestData['slim'] = json_decode($requestData['slim'],true);
        }
        return $requestData;
    }    

    /**
     * isMulti
     *
     * @param  mixed $requestData
     * check if the request is multi or single image
     * @return bool
     */
    private function isMulti($requestData) {
        return (bool) isset($requestData['slim'][0]) && is_array($requestData['slim'][0]) ;
    }
}
