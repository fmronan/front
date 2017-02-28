<?php

namespace Viteloge\CoreBundle\Component\Enum {

    class TypeEnum extends Enum {

        const VIDE = null;

        const HOUSE = 'Maison';

        const APPARTMENT = 'Appartement';

        const FIELD = 'Terrain';

        const PARKING = 'Stationnement';

        public function choices() {
            return array(
                'ad.type.house' => self::HOUSE,
                'ad.type.appartment' => self::APPARTMENT,
                'ad.type.field' => self::FIELD,
                'ad.type.parking'=> self::PARKING
            );
        }

    }

}
