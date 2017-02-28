<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class ConditionEnum extends Enum {

        const VIDE = null;

        const NEWER = 'N';

        const GOOD = 'B';

        const WORK = 'T';

        const REPAIR = 'R';

        public function choices() {
            return array(
                 'estimate.condition.newer'=> self::NEWER,
                 'estimate.condition.good'=> self::GOOD,
                 'estimate.condition.work'=> self::WORK,
                 'estimate.condition.repair'=> self::REPAIR
            );
        }

    }

}
