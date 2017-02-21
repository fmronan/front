<?php

namespace Viteloge\FrontendBundle\Services {
use Symfony\Component\HttpFoundation\JsonResponse;
use Viteloge\CoreBundle\Entity\Ad;

    class PhoneService {


        public function searchPhoneNumber($tel){
            if(isset($tel) && !empty($tel)){
                $clef = "b28b9b89b6aea1dc6287a6d446e001a8";
                $tel = preg_replace("([^0-9]+)","",$tel);
                $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
                $xml.=
                "<createLink><clef>".$clef."</clef><numero>".$tel."</numero><pays>FR</pays></createLink>";
                // Lien vers l’API :
                $url = "http://mer.viva-multimedia.com/xmlRequest.php?xml=".urlencode($xml);
                $ch = curl_init ($url) ;
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
                $res = curl_exec ($ch) ;
                curl_close ($ch);
                $array = array();
                //recupération du numéro et du code
                if (preg_match("{<numero>(.*)</numero>.*<code>(.*)</code>}", $res, $regs))
                {
                    $tel = ((strlen($regs[1]) == "10") ? wordwrap($regs[1], 2, '.', 1) : $regs[1]);
                    $array[] = $tel ;
                    $array[] = $regs[2] ;
                }
                $num = implode('¤',$array);
                $num = rtrim($num,'¤');
               }else{
                $num ='Pas de Numéro';
               }
            return $num;
        }

        public function getPhoneNumber($tel,Ad $ad){
            $num = $this->searchPhoneNumber($tel);
            $cout = '1,34€/appel.0,34€/mn';
            $response = new JsonResponse();

            return $response->setData(array('phone' => $num, 'cout' => $cout, 'id' => $ad->getId()));

        }


    }

}
