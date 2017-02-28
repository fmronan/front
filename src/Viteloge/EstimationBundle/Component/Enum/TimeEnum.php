<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class TimeEnum extends Enum {

        const VIDE = null;

        const IMMEDIATE = 0;

        const ONE_MONTH = 1;

        const TWO_MONTH = 2;

        const SIX_MONTH = 6;

        const NONE = -1;

        public function choices() {
            return array(
                 'estimate.time.immediate'=> self::IMMEDIATE,
                 'estimate.time.one_month'=> self::ONE_MONTH,
                 'estimate.time.two_month'=> self::TWO_MONTH,
                 'estimate.time.six_month'=> self::SIX_MONTH,
                 'estimate.time.none'=> self::NONE
            );
        }

    }

}
