<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue;

use Rhumsaa\Uuid\Uuid;

class Message
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var array
     */
    private $internal = array();

    /**
     * @param string $uuid
     * @param string $type
     * @param mixed $data
     */
    private function __construct($uuid, $type, $data, $identifier = null)
    {
        $this->uuid = $uuid;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Message
     */
    public function setInternal($key, $value)
    {
        $this->internal[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getInternal($key, $default = null)
    {
        if (!isset($this->internal[$key])) {
            return $default;
        }
        return $this->internal[$key];
    }

    /**
     * @return array
     */
    public function getInternals()
    {
        return $this->internal;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getInternal('identifier');
    }

    /**
     * @param string $identifier
     * @return Message
     */
    public function setIdentifier($identifier)
    {
        $this->setInternal('identifier', $identifier);
        return $this;
    }

    /**
     * @param string $type
     * @param mixed $data
     * @return Message
     */
    public static function create($type, $data = null)
    {
        return new self(Uuid::uuid4()->toString(), $type, $data);
    }

    /**
     * @param array $arr
     * @return Message
     */
    public static function fromArray(array $arr)
    {
        return new self($arr['uuid'], $arr['type'], $arr['data']);
    }
}
