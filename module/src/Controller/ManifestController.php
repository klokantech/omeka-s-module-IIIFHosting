<?php
namespace IIIFHosting\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Omeka\Mvc\Exception\NotFoundException;


class ManifestController extends AbstractActionController
{
    public function manifestAction()
    {
        $id = $this->params('id');

        if (empty($id)) {
            throw new NotFoundException;
        }

        $item = $this->api()->read('items', $id)->getContent();

        if (empty($item)) {
            throw new NotFoundException;
        }

        $iiifManifest = $this->viewHelpers()->get('Manifest');
        $manifest = $iiifManifest($item);

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json', true);
        $response->getHeaders()->addHeaderLine('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));

        return $response;
    }
}
