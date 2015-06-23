<?php

namespace Studiow\Translator\TranslationSet;

use Studiow\Translator\ITranslationSet;

class ArrayTranslationSet implements ITranslationSet
{

    private $items = [];

    public function getLabel($label)
    {
        if ($this->hasLabel($label)) {
            return $this->items[$label];
        }
        return false;
    }

    public function hasLabel($label)
    {
        return array_key_exists($label, $this->items);
    }

    public function setLabel($label, $translation)
    {
        $this->items[$label] = $translation;
        return $this;
    }

    public function setLabels(array $labels)
    {
        foreach ($labels as $label => $translation) {
            $this->setLabel($label, $translation);
        }
        return $this;
    }

}
