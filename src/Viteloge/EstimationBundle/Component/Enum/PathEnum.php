<?php

namespace Viteloge\EstimationBundle\Component\Enum {

    use Viteloge\CoreBundle\Component\Enum\Enum;

    class PathEnum extends Enum {

        const VIDE = null;

        const DRIVEWAY = 'ALL';

        const DRIVE = 'AV';

        const BOULEVARD = 'BD';

        const CROSSROADS = 'CAR';

        const WAY = 'CHE';

        const PAVEMENT = 'CHS';

        const CITY = 'CITE';

        const CORNICE = 'COR';

        const COURSE = 'CRS';

        const DOMAIN = 'DOM';

        const DESCENT = 'DSC';

        const GAP = 'ECA';

        const ESPLANADE = 'ESP';

        const SUBURB = 'FG';

        const GREAT = 'GR';

        const HAMLET = 'HAM';

        const HALLE = 'HLE';

        const IMPASSE = 'IMP';

        const LOCALITY = 'LD';

        const ALLOTMENT = 'LOT';

        const MARKET = 'MAR';

        const MOUNTED = 'MTE';

        const PASSAGE = 'PAS';

        const SPOT = 'PL';

        const LOWLAND = 'PLN';

        const PLATEAU = 'PLT';

        const WALK = 'PRO';

        const PARVIS = 'PRV';

        const NEIGHBORHOOD = 'QUA';

        const PLATFORM = 'QUAI';

        const RESIDENCE = 'RES';

        const ALLEY = 'RLE';

        const BYPASS = 'ROC';

        const TRAFFIC_CIRCLE = 'RPT';

        const ROAD = 'RTE';

        const STREET = 'RUE';

        const SENTE = 'SEN';

        const SQUARE = 'SQ';

        const MEDIAN = 'TPL';

        const TRAVERSE = 'TRA';

        const VILLA = 'VLA';

        const VILLAGE = 'VLGE';

        public function choices() {
            return array(
                 'estimate.path.driveway'=> self::DRIVEWAY,
                 'estimate.path.drive'=> self::DRIVE,
                 'estimate.path.boulevard'=> self::BOULEVARD,
                 'estimate.path.crossroads'=> self::CROSSROADS,
                 'estimate.path.way'=> self::WAY,
                 'estimate.path.pavement'=> self::PAVEMENT,
                 'estimate.path.city'=> self::CITY,
                 'estimate.path.cornice'=> self::CORNICE,
                 'estimate.path.course'=> self::COURSE,
                 'estimate.path.domain'=> self::DOMAIN,
                 'estimate.path.descent'=> self::DESCENT,
                 'estimate.path.gap'=> self::GAP,
                 'estimate.path.esplanade'=> self::ESPLANADE,
                 'estimate.path.suburb'=> self::SUBURB,
                 'estimate.path.great'=> self::GREAT,
                 'estimate.path.hamlet'=> self::HAMLET,
                 'estimate.path.halle'=> self::HALLE,
                 'estimate.path.impasse'=> self::IMPASSE,
                 'estimate.path.locality'=> self::LOCALITY,
                 'estimate.path.allotment'=> self::ALLOTMENT,
                 'estimate.path.market'=> self::MARKET,
                 'estimate.path.mounted'=> self::MOUNTED,
                 'estimate.path.passage'=> self::PASSAGE,
                 'estimate.path.spot'=> self::SPOT,
                 'estimate.path.lowland'=> self::LOWLAND,
                 'estimate.path.plateau'=> self::PLATEAU,
                 'estimate.path.walk'=> self::WALK,
                 'estimate.path.parvis'=> self::PARVIS,
                 'estimate.path.neighborhood'=> self::NEIGHBORHOOD,
                 'estimate.path.platform'=> self::PLATFORM,
                 'estimate.path.residence'=> self::RESIDENCE,
                 'estimate.path.alley'=> self::ALLEY,
                 'estimate.path.bypass'=> self::BYPASS,
                 'estimate.path.trafficcircle'=> self::TRAFFIC_CIRCLE,
                 'estimate.path.road'=> self::ROAD,
                 'estimate.path.street'=> self::STREET,
                 'estimate.path.sente'=> self::SENTE,
                 'estimate.path.square'=> self::SQUARE,
                 'estimate.path.median'=> self::MEDIAN,
                 'estimate.path.traverse'=> self::TRAVERSE,
                 'estimate.path.villa'=> self::VILLA,
                 'estimate.path.village'=> self::VILLAGE
            );
        }

    }

}
