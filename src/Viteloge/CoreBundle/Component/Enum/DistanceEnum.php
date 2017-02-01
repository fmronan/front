<?php

namespace Viteloge\CoreBundle\Component\Enum {

    class DistanceEnum extends Enum {

        const __default = null;

        const NONE = 0;

        const FIVE = 5;

        const TEN = 10;

        const TWENTY = 20;

        const THIRTY = 30;

        public function choices() {
            return array(
                 'ad.distance.none' => self::NONE,
                 'ad.distance.five'=> self::FIVE,
                 'ad.distance.ten'=> self::TEN,
                 'ad.distance.twenty'=> self::TWENTY,
                 'ad.distance.thirty'=> self::THIRTY
            );
        }

    }

}
