<?php

namespace IIIFHosting\View\Helper;

use Omeka\Api\Representation\AbstractResourceEntityRepresentation;
use Omeka\Api\Representation\ItemRepresentation;
use Omeka\Api\Representation\MediaRepresentation;
use Zend\View\Helper\AbstractHelper;
use Exception;

class Manifest extends AbstractHelper
{
    public function __invoke(AbstractResourceEntityRepresentation $item)
    {
        $CONTEXT_PRESENTATION = "http://iiif.io/api/presentation/2/context.json";
        $CONTEXT_IMAGE = "http://iiif.io/api/image/2/context.json";
        $PROFILE_IMAGE = "http://iiif.io/api/image/2/level2.json";
        $BASE_URI = "http://$_SERVER[HTTP_HOST]" . $item->siteUrl();
        $MANIFEST_URI     = $BASE_URI ."/manifest.json";
        $SEQUENCE_URI     = $BASE_URI . "/sequence.json";
        $CANVAS_BASE_URI  = $BASE_URI . "/canvas/";

        $canvases = array("canvases" => array());
        $counter = 0;

        foreach($item->media() as $media) {
            if (strpos($media->mediaType(), 'image/') !== 0) {
                continue;
            }

            $metadata = $media->mediaData();

            if (!isset($metadata) or !array_key_exists('@context', $metadata)) {
                continue;
            }

            $base_image_url = $metadata['@id'];
            $image_width   = 0;
            $image_height  = 0;
            $image_url = $base_image_url . "/full/full/0/native.jpg";

            if (isset($metadata)) {
                $image_width = $metadata['width'];
                $image_height = $metadata['height'];
            }

            $images = array(
                  "@type"       => "oa:Annotation",
                  "motivation"  => "sc:painting",
                  "resource"    => array(
                        "@id"     => $image_url,
                        "@type"   => "dctypes:Image",
                        "service" => array(
                            "@context"  => $CONTEXT_IMAGE,
                            "profile"   => $PROFILE_IMAGE,
                            "@id"       => $base_image_url
                        )
                  ),
                  "on"  => $CANVAS_BASE_URI . "$counter.json"
            );

            $canvas = array(
                    "@id"   => $CANVAS_BASE_URI . "$counter.json",
                    "@type" => "sc:Canvas",
                    "label" => $item->displayTitle() . " - image $counter",
                    "width" => (int)$image_width,
                    "height" => (int)$image_height,
                    "images"  => array(),
            );

            $images["resource"]["width"] = (int)$image_width;
            $images["resource"]["height"] = (int)$image_height;

            array_push( $canvas['images'], $images );
            array_push( $canvases['canvases'], $canvas );

            $counter++;
        }

        $sequences = array(
                 "sequences" => array(
                    "@id"     => $SEQUENCE_URI,
                    "@type"   => "sc:Sequence",
                    "label"   => $item->displayTitle() . " - sequence 1"
                 )
        );

        $sequences = $sequences['sequences'] + $canvases;

        $metadata = [];
        foreach ($item->values() as $term => $value) {
            $metadata[] = (object) [
                'label' => $value['alternate_label'] ?: $value['property']->label(),
                'value' => count($value['values']) > 1
                    ? array_map('strval', $value['values'])
                    : (string) reset($value['values']),
            ];
        }

        $manifest = array(
                "@context"  => $CONTEXT_PRESENTATION,
                "@id"       => $MANIFEST_URI,
                "@type"     => "sc:Manifest",
                "label"     => $item->displayTitle(),
                "metadata"  => $metadata,
                "logo"      => $this->view->setting('iiif_manifest_logo'),
                "licence"   => $this->view->setting('iiif_manifest_licence'),
                "sequences" => array($sequences)
        );

        return $manifest;
    }
}
