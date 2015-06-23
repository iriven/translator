<?php

namespace Studiow\Translator\TranslationSet;

use Studiow\Translator\Exception\MoReaderException;

class MoTranslationSet extends ArrayTranslationSet
{

    public function __construct($filename)
    {
        $reader = new \Studiow\Translator\MoReader($filename);
        $this->setLabels($reader->toArray());
    }

}
