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
        $this->add([
            'type' => 'text',
            'name' => 'manifest_logo',
            'options' => [
                'label' => 'URL for logo in manifest', // @translate
                'info' => 'Enter the URL of manifest logo', // @translate
            ],
            'attributes' => [
                'required' => false,
            ],
        ]);
        $this->add([
            'type' => 'text',
            'name' => 'manifest_license',
            'options' => [
                'label' => 'URL for manifest license', // @translate
                'info' => 'Enter the URL of manifest license', // @translate
            ],
            'attributes' => [
                'required' => false,
            ],
        ]);
    }
}
