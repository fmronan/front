<?php

namespace Viteloge\CoreBundle\Component\Enum {

    /**
     * Parent class for all SPL types.
     * @see SplType http://php.net/manual/en/class.spltype.php
     */
    abstract class SplType {

        /**
         * Default value
         */
        const VIDE = null;

        /**
         * Internal enum value
         */
        public $__default;

        /**
         * Creates a new value of some type
         * @param mixed $initial_value Type and default value depends on the extension class.
         * @return void
         * @throws \UnexpectedValueException if incompatible type is given.
         */
        public function __construct($initial_value=null) {
            if ($initial_value === null) {
                $initial_value = static::VIDE;
            }
            $class = new \ReflectionClass($this);
            if(!in_array($initial_value, $class->getConstants())) {
                throw new \UnexpectedValueException('Value not a const in enum '.$class->getShortName());
            }
            $this->__default = $initial_value;
        }

        /**
         * Stringify object
         * @return string
         */
        final public function __toString() {
            return (string)$this->__default;
        }

        /**
         * Export object
         * @return SplType
         */
        final public static function __set_state($properties) {
            return new static($properties['VIDE']);
        }

        /**
         * Dumping object (php > 5.6.0)
         * @return array
         */
        final public function __debugInfo() {
            return array( 'VIDE' => $this->__default);
        }

    }
}
