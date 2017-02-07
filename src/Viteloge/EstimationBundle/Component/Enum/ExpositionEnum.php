<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class ExpositionEnum extends Enum {

        const __default = null;

        const NORTH = 'N';

        const SOUTH = 'S';

        const WEST = 'O';

        const EAST = 'E';

        public function choices() {
            return array(
                 'estimate.exposition.north'=> self::NORTH,
                 'estimate.exposition.south'=> self::SOUTH,
                  'estimate.exposition.west'=> self::WEST,
                  'estimate.exposition.east'=> self::EAST
            );
        }

    }

}
