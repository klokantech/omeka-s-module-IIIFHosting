<?php
namespace IIIFHosting\Controller;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Controller\AbstractActionController;

class IngestController extends AbstractActionController
{
    public function ingestAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        if($request->isPost()) {
            $rawBody = $request->getContent();

            if (!$rawBody) {
                return $response;
            }

            $payload = json_decode($rawBody);

            if($payload->status == "done"){
                $base_image_url = $payload->url . "/info.json";

                $ctx = stream_context_create(array(
                    'http' => array(
                        'timeout' => 5
                        )
                    )
                );

                $result = @file_get_contents($base_image_url, False, $ctx);

                if($result !== FALSE){
                    $metadata = $result;
                    $connection = $this->getEvent()->getApplication()->getServiceManager()->get('Omeka\Connection');
                    $connection->exec("UPDATE media SET `data`='$metadata', `renderer`='iiif' WHERE id = $payload->external_id;");
                }
            }
        }

        return $response;
    }
}
