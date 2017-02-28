<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class ApplicantEnum extends Enum {

        const VIDE = null;

        const OWNER = 'P';

        const TENANT = 'L';

        const PURCHASER = 'A';

        const AGENT = 'I';

        public function choices() {
            return array(
                 'estimate.applicant.owner'=> self::OWNER,
                 'estimate.applicant.tenant'=> self::TENANT,
                 'estimate.applicant.purchaser'=> self::PURCHASER,
                 'estimate.applicant.agent'=> self::AGENT
            );
        }

    }

}
