<?php

namespace Viteloge\InseeBundle\Controller {

    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Viteloge\InseeBundle\Entity\InseeArea;

    /**
     * @Route("/insee-area")
     */
    class AreaController extends Controller {

        /**
         * @Route(
         *      "/show/{id}.{_format}",
         *      requirements={
         *          "id"="\d+",
         *          "_format"="html|json"
         *      },
         *      defaults={
         *          "_format"="json"
         *      },
         *      name="viteloge_insee_area_show"
         * )
         * @ParamConverter("inseeArea", class="VitelogeInseeBundle:InseeArea", options={"id" = "id"})
         * @Cache(expires="tomorrow", public=true)
         * @Method({"GET"})
         * @Route(options={"expose"=true})
         */
        public function showAction(Request $request, $_format, InseeArea $inseeArea) {

                $inseeArea->getInseeCity()->setInseeState(null); // we do not need insee city
                $inseeArea->getInseeCity()->setInseeDepartment(null); // we do not need insee city

            return $this->render(
                'VitelogeInseeBundle:Area:show.'.$_format.'.twig',
                array(
                    'inseeArea' => $inseeArea
                )
            );
        }

    }

}
