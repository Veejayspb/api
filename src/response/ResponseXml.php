<?php

namespace veejay\api\response;

use SimpleXMLElement;

class ResponseXml extends Response
{
    /**
     * Название корневого элемента.
     * @var string
     */
    public string $rootName = 'response';

    /**
     * Название элемента при перечислении массива.
     * @var string
     */
    public string $itemName = 'item';

    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        header('Content-Type: application/xml; charset=utf-8');
        return parent::run();
    }

    /**
     * {@inheritdoc}
     */
    protected function getBody(): string
    {
        $xml = new SimpleXMLElement("<$this->rootName/>");
        $this->array2xml($this->data, $xml);
        return $xml->asXML();
    }

    /**
     * Сформировать XML-древо на основе переданного массива данных.
     * @param array $data
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function array2xml(array $data, SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            $type = gettype($value);

            $child = $xml->addChild(
                $this->getElementName($key),
                $this->getElementValue($value)
            );

            if (in_array($type, ['array', 'object'])) {
                $this->array2xml((array)$value, $child);
            }
        }
    }

    /**
     * Вернуть название элемента по значению ключа.
     * @param int|string $key
     * @return string
     */
    private function getElementName(int|string $key): string
    {
        return is_string($key) && $key != '' ? $key : $this->itemName;
    }

    /**
     * Вернуть содержимое элемента по значению.
     * @param mixed $value
     * @return mixed
     */
    private function getElementValue(mixed $value): mixed
    {
        $type = gettype($value);
        $allowed = ['integer', 'double', 'string', 'boolean', 'null'];

        return in_array($type, $allowed) ? $value : null;
    }
}
