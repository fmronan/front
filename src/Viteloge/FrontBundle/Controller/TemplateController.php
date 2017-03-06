<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Viteloge\FrontBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Viteloge\FrontendBundle\Controller\TemplateController as FrontendTemplate;


/**
 * TemplateController.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TemplateController extends FrontendTemplate
{

    /**
     * Renders a template.
     *
     * @param string    $template  The template name
     * @param int|null  $maxAge    Max age for client caching
     * @param int|null  $sharedAge Max age for shared (proxy) caching
     * @param bool|null $private   Whether or not caching should apply for client caches only
     *
     * @return Response A Response instance
     */
    public function templateAction($template, $maxAge = null, $sharedAge = null, $private = null)
    {
        //on va devoit changer le nom du template
        $template = str_replace('Frontend', 'Front', $template);
        return parent::templateAction( $template,$maxAge,$sharedAge,$private);

    }
}
