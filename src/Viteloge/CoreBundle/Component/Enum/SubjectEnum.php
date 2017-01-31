<?php

namespace Viteloge\CoreBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class SubjectEnum extends Enum {

        const __default = null;

        const NATIONAL_AD = 1;

        const LOCAL_AD = 2;

        const HIGHLIGHT_AD = 3;

        const PARTNER = 4;

        const BUG = 5;

        const TECHNICAL_ASSIST = 6;

        const MISC_QUESTION = 7;

        public function choices() {
            return array(
                 'contact.subject.nationalad'=> self::NATIONAL_AD,
                 'contact.subject.localad'=> self::LOCAL_AD,
                 'contact.subject.highlightad'=> self::HIGHLIGHT_AD,
                 'contact.subject.partner'=> self::PARTNER,
                'contact.subject.bug'=> self::BUG ,
                'contact.subject.technicalassist' => self::TECHNICAL_ASSIST,
                 'contact.subject.miscquestion'=> self::MISC_QUESTION,
            );
        }

    }

}
