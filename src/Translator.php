<?php

namespace Studiow\Translator;

class Translator
{

    private $sets = [];
    private $defaultLanguage = 'en';
    private $defaultSet = 'default';

    public function __construct($defaultSet = 'default', $defaultLanguage = 'en')
    {
        $this->setDefaultLanguage($defaultLanguage);
        $this->setDefaultSet($defaultSet);
    }

    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;
        return $this;
    }

    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    public function setDefaultSet($setName)
    {
        $this->defaultSet = $setName;
        return $this;
    }

    public function getDefaultSet()
    {
        return $this->defaultSet;
    }

    public function addTranslationSet(ITranslationSet $translationSet, $setName = null, $language = null)
    {
        if (is_null($setName)) {
            $setName = $this->getDefaultSet();
        }
        if (is_null($language)) {
            $language = $this->getDefaultLanguage();
        }
        $this->sets[$language][$setName] = $translationSet;
    }

    public function getTranslationSet($setName = null, $language = null)
    {
        if (is_null($setName)) {
            $setName = $this->getDefaultSet();
        }
        if (is_null($language)) {
            $language = $this->getDefaultLanguage();
        }

        return isset($this->sets[$language][$setName]) ? $this->sets[$language][$setName] : null;
    }

    public function getLabel($label, $setName = null, $language = null)
    {

        if (is_null($setName)) {
            $setName = $this->getDefaultSet();
        }
        if (is_null($language)) {
            $language = $this->getDefaultLanguage();
        }

        $set = $this->getTranslationSet($setName, $language);

        if (!is_null($set) && $set->hasLabel($label)) {
            return $set->getLabel($label);
        }
    }

}
