<?php

namespace Viteloge\CoreBundle\Component\Enum {

    class CivilityEnum extends Enum {

        const VIDE = null;

        const MISTER = 'M';

        const MISTRESS = 'Mme';

        const MISS = 'Mlle';

        public function choices() {
            return array(
                 'user.civility.mister'=> self::MISTER,
                 'user.civility.mistress'=> self::MISTRESS,
                 'user.civility.miss'=> self::MISS
            );
        }

    }

}
