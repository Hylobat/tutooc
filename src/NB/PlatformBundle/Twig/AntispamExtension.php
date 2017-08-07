<?php

namespace NB\PlatformBundle\Twig;

use NB\PlatformBundle\Antispam\NBAntispam;

class AntispamExtension extends \Twig_Extension
{
    /**
     * @var NBAntispam
     */
    private $nbAntispam;

    public function __construct(NBAntispam $nbAntispam)
    {
        $this->nbAntispam = $nbAntispam;
    }

    public function checkIfArgumentIsSpam($text)
    {
        return $this->nbAntispam->isSpam($text);
    }

    // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
        );
    }

    // La méthode getName() identifie votre extension Twig, elle est obligatoire
    public function getName()
    {
        return 'NBAntispam';
    }
}