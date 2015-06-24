<?php

namespace Studiow\Translator;

use Studiow\Translator\Exception\MoReaderException;

class MoReader
{

    private $packMode = 'V';
    private $file;
    private $revision;
    private $totalBytes;
    private $numMessages;
    private $messages = [];

    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            throw new MoReaderException(sprintf("File %s does not exist", $filename));
        }
        $this->file = fopen($filename, 'rb');
        $this->packMode = $this->getPackMode();
        if (false === $this->packMode) {
            throw new MoReaderException(sprintf("File %s is not a valid gettext file", $filename));
        }
        $this->parseContents();
        fclose($this->file);
    }

    private function parseContents()
    {
        $this->revision = $this->readData()[1];
        $this->totalBytes = $this->readData()[1];
        $this->numMessages = $this->readData()[1];
        $translationsOffset = $this->readData()[1];
        fseek($this->file, $this->numMessages);
        $msgTemp = $this->readData(2 * $this->totalBytes);

        fseek($this->file, $translationsOffset);
        $tnsTemp = $this->readData(2 * $this->totalBytes);

        for ($a = 0; $a < $this->numMessages; ++$a) {
            $cb = $a * 2 + 1;
            if (isset($msgTemp[$cb]) && $msgTemp[$cb] != 0) {
                fseek($this->file, $msgTemp[$cb + 1]);
                $original = (array) fread($this->file, $msgTemp[$cb]);
            } else {
                $original = [''];
            }

            if (isset($tnsTemp[$cb]) && $tnsTemp[$cb] != 0) {
                fseek($this->file, $tnsTemp[$cb + 1]);
                $translation = explode("\0", fread($this->file, $tnsTemp[$cb]));
                $this->setMessage($original, $translation);
            }
        }
        unset($this->messages['']);
    }

    private function setMessage($original, $translation)
    {
        if (sizeof($translation) > 0 && sizeof($original) > 0) {
            $this->messages[$original[0]] = $translation[0];
            array_shift($original);
            foreach ($original as $orig) {
                $this->messages[$orig] = '';
            }
        } else {
            $this->messages[$original[0]] = $translation[0];
        }
    }

    /**
     * Read bytes from file
     * @param int $numBytes
     * @return array
     */
    private function readData($numBytes = 1)
    {
        return unpack($this->packMode . $numBytes, fread($this->file, 4 * $numBytes));
    }

    /**
     * Determine which mode to use when reading binary data
     * @return string|boolean N for big-endian, V for little-endian, or false for invalid data
     */
    private function getPackMode()
    {
        $data = $this->readData();
        if (isset($data[1])) {
            $str_test = substr(dechex(intval($data[1])), -8);
            if (strtolower($str_test) === 'de120495') {
                return 'N'; //big endian
            } else if (strtolower($str_test) === '950412de') {
                return 'V'; //small endian
            }
        }
        return false;
    }

    /**
     * export messages as array
     * @return array
     */
    public function toArray()
    {
        return $this->messages;
    }

}
