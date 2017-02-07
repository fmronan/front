<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class TypeEnum extends Enum {

        const __default = null;

        const HOUSE = 'M';

        const APPARTMENT = 'A';

        public function choices() {
            return array(
                 'estimate.type.house'=> self::HOUSE,
                 'estimate.type.appartment'=> self::APPARTMENT
            );
        }

    }

}
