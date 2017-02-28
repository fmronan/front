<?php

namespace Viteloge\CoreBundle\Component\Enum {

    class RoomEnum extends Enum {

        const VIDE = null;

        const ONE = 1;

        const TWO = 2;

        const THREE = 3;

        const FOUR = 4;

        const MORE = 5;

        public function choices() {
            return array(
                 'ad.rooms.one'=> self::ONE,
                 'ad.rooms.two'=> self::TWO,
                 'ad.rooms.three'=> self::THREE,
                 'ad.rooms.four'=> self::FOUR,
                 'ad.rooms.more'=> self::MORE
            );
        }

    }

}
