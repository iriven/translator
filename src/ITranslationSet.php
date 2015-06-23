<?php

namespace Studiow\Translator;

interface ITranslationSet
{

    public function hasLabel($label);

    public function getLabel($label);

    public function setLabel($label, $translation);
}
