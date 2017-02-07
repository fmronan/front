<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class UsageEnum extends Enum {

        const __default = null;

        const INHABITED = 'H';

        const RENT = 'L';

        const BLANK = 'V';

        public function choices() {
            return array(
                 'estimate.usage.inhabited'=> self::INHABITED,
                 'estimate.usage.rent'=> self::RENT,
                 'estimate.usage.empty'=> self::BLANK
            );
        }

    }

}
