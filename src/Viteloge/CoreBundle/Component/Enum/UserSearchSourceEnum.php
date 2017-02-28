<?php

namespace Viteloge\CoreBundle\Component\Enum {

    class UserSearchSourceEnum extends Enum {

        const VIDE = '';

        const WEB = 'websearch';

        const CONFIGURE = 'configure';

        const LANDING = 'landing';

        const DIRECT = 'direct';

        public function choices() {
            return array(
                 'usersearch.web'=> self::WEB,
                 'usersearch.configure'=> self::CONFIGURE,
                 'usersearch.landing'=> self::LANDING
            );
        }

    }

}
