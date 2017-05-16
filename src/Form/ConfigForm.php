<?php
namespace IIIFHosting\Form;

use Zend\Form\Form;
use Zend\Validator\Callback;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'customer',
            'options' => [
                'label' => 'Customer email', // @translate
                'info' => 'Enter the registered IIIF Hosting customer`s email.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'type' => 'text',
            'name' => 'secure_payload',
            'options' => [
                'label' => 'Secure payload', // @translate
                'info' => 'Enter the secure payload from IIIF Hosting administration.', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
        $this->add([
            'type' => 'text',
            'name' => 'ingest_api',
            'options' => [
                'label' => 'URL ingest_api', // @translate
                'info' => 'Enter the URL of ingest API', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
    }
}
