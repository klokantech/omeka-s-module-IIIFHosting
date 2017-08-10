<?php
namespace IIIFHosting;

use IIIFHosting\Form\ConfigForm;
use Omeka\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\PhpRenderer;
use Omeka\Entity\Media;
use Omeka\Mvc\Controller\Plugin\Messenger;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
        $acl = $this->getServiceLocator()->get('Omeka\Acl');
        $acl->allow(
            null,
            'IIIFHosting\Controller\Ingest'
        );
        $acl->allow(
            null,
            'IIIFHosting\Controller\Manifest'
        );

        $acl->allow(
            null,
            'Omeka\Api\Adapter\MediaAdapter',
            ['update']
        );

        $acl->allow(
            null,
            'Omeka\Entity\Media',
            ['update']
        );
    }

    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $settings = $serviceLocator->get('Omeka\Settings');
        $settings->delete('iiifhosting_customer');
        $settings->delete('iiifhosting_secure_payload');
        $settings->delete('iiifhosting_ingest_api');
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $iiifhosting_ingest_api = $settings->get('iiifhosting_ingest_api');

        if ($iiifhosting_ingest_api == ''){
            $iiifhosting_ingest_api = 'https://admin.iiifhosting.com/api/v1/ingest/';
        }

        $form = new ConfigForm;
        $form->init();
        $form->setData([
            'customer' => $settings->get('iiifhosting_customer'),
            'secure_payload' => $settings->get('iiifhosting_secure_payload'),
            'ingest_api' => $iiifhosting_ingest_api,
        ]);
        return $renderer->formCollection($form, false);
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $form = new ConfigForm;
        $form->init();
        $form->setData($controller->params()->fromPost());
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }
        $formData = $form->getData();

        $iiifhosting_customer = $formData['customer'];
        $iiifhosting_secure_payload = $formData['secure_payload'];
        $iiifhosting_ingest_api = $formData['ingest_api'];

        $settings->set('iiifhosting_customer', $iiifhosting_customer);
        $settings->set('iiifhosting_secure_payload', $iiifhosting_secure_payload);
        $settings->set('iiifhosting_ingest_api', $iiifhosting_ingest_api);

        if ($iiifhosting_customer and $iiifhosting_secure_payload and $iiifhosting_ingest_api){
            $data = array(
                "email" => $iiifhosting_customer,
                "secure_payload" => $iiifhosting_secure_payload,
                "webhook_url" => "http://$_SERVER[HTTP_HOST]/iiif_ingest"
            );
            $postdata = json_encode($data);

            $ctx = stream_context_create(array(
                'http' => array(
                    'method'  => 'POST',
                    'timeout' => 5,
                    'header'  => 'Content-type: application/json\r\n',
                    'content' => $postdata
                )
            ));

            $result = @file_get_contents("https://admin.iiifhosting.com/api/v1/configure_webhook/", False, $ctx);

            if($result === FALSE){
                $messenger = new Messenger;
                $messenger->addError("Error in communication with IIIF Hosting server.");
            }
        }

        return true;
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        $sharedEventManager->attach(
            'Omeka\Api\Adapter\ItemAdapter',
            'api.create.post',
            [$this, 'afterSaveItem']
        );
        $sharedEventManager->attach(
            'Omeka\Api\Adapter\ItemAdapter',
            'api.update.post',
            [$this, 'afterSaveItem']
        );
    }

    public function afterSaveItem(Event $event)
    {
        $item = $event->getParam('response')->getContent();

        foreach ($item->getMedia() as $media) {
            if(strpos($media->getMediaType(), 'image/') !== False and $media->getIngester() == 'upload' and $media->getRenderer() != 'iiif'){
                $this->callIiifhostingIngestApi($media);
            }
        }
    }

    public function callIiifhostingIngestApi(Media $media)
    {
        $settings = $this->getServiceLocator()->get('Omeka\Settings');

        $iiifhosting_customer = $settings->get('iiifhosting_customer');
        $iiifhosting_secure_payload = $settings->get('iiifhosting_secure_payload');
        $iiifhosting_ingest_api = $settings->get('iiifhosting_ingest_api');

        if ($iiifhosting_customer and $iiifhosting_secure_payload and $iiifhosting_ingest_api){
            $data = array(
                "email" => $iiifhosting_customer,
                "secure_payload" => $iiifhosting_secure_payload,
                "files" => array(array(
                    "id" => $media->getId(),
                    "name" => $media->getSource(),
                    "url" => "http://$_SERVER[HTTP_HOST]/files/original/".$media->getStorageId().".".$media->getExtension(),
                    "size" => 1
                ))
            );
            $postdata = json_encode($data);

            $ctx = stream_context_create(array(
                'http' => array(
                    'method'  => 'POST',
                    'timeout' => 5,
                    'header'  => 'Content-type: application/json\r\n',
                    'content' => $postdata
                )
            ));

            $result = @file_get_contents($iiifhosting_ingest_api, False, $ctx);

            if($result === FALSE){
                $messenger = new Messenger;
                $messenger->addError("Error in communication with IIIF Hosting server.");
            }
        }
    }
}

