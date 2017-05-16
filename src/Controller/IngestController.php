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

            //~ var_dump( $request->getContent());
            //~ exit;

            $rawBody = $request->getContent();

            if (!$rawBody) {
                return $response;
            }

            $data = json_decode($rawBody);

            $base_image_url = $data->url . "/info.json";

            $ctx = stream_context_create(array(
                'http' => array(
                    'timeout' => 5
                    )
                )
            );

            $result = @file_get_contents($base_image_url, False, $ctx);

            if($result !== FALSE){
                $metadata = $result;
                $media = $this->api()->read('media', $data->external_id)->getContent();
                //~ echo($medias->getServiceLocator()->get('Omeka\Connection'));
                //~ exit;
                //~ $r = $this->api()->update('media', $data->external_id, ['o:data' => 'renderer'], ['o:data' => 'renderer'], ['isPartial' => true]);
                //~ $r = $this->api()->delete('media', $data->external_id);
                //~ $connection = $this->getServiceLocator()->get('Omeka\Connection');
                $connection = $media->getServiceLocator()->get('Omeka\Connection');
                $connection->exec("UPDATE media SET `data`='$metadata', `renderer`='iiif' WHERE id = $data->external_id;");
            }
        }

        return $response;
    }
}
