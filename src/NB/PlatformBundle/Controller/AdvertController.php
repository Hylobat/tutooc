<?php

namespace NB\PlatformBundle\Controller;

use NB\PlatformBundle\Entity\Advert;
use NB\PlatformBundle\Form\AdvertEditType;
use NB\PlatformBundle\Form\AdvertType;
use NB\PlatformBundle\Entity\AdvertSkill;
use NB\PlatformBundle\Entity\Application;
use NB\PlatformBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdvertController extends Controller
{
    public function homeAction(Request $request)
    {
        return $this->redirectToRoute('nb_platform_home');
    }
    public function indexAction($page, Request $request)
    {

        if ($page < 1){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }
        $nbPerPage = 3;
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('NBPlatformBundle:Advert')
            ->getAdverts($page, $nbPerPage)
        ;

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        if($page > $nbPages){
            return $this->redirectToRoute('nb_platform_home', array('page' => $nbPages));
        }
        return $this->render('NBPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages'     => $nbPages,
            'page'        => $page,
        ));
    }

    public function viewAction(Advert $advert)
    {

        $em = $this->getDoctrine()->getManager();
        /*$repository = $em->getRepository('NBPlatformBundle:Advert');
        $advert = $repository->find($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }*/
        $listApplications = $em
            ->getRepository('NBPlatformBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        $listAdvertSkills = $em
            ->getRepository('NBPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('NBPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills
        ));
    }

    /**
     * @Security("has_role('ROLE_AUTEUR')")
     */
    public function addAction(Request $request)
    {

        $advert = new Advert();

        $form   = $this->createForm(AdvertType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Annonce bien enregistrée.');
            return $this->redirectToRoute('nb_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('NBPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_AUTEUR')")
     */
    public function editAction(Advert $advert, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /*$advert = $em->getRepository('NBPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }*/

        $form   = $this->createForm(AdvertEditType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            //$em->persist($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Annonce bien enregistrée.');
        }

        return $this->render('NBPlatformBundle:Advert:edit.html.twig', array(
            'form' => $form->createView(),
            'advert' => $advert
        ));
    }


    /**
     * @Security("has_role('ROLE_AUTEUR')")
     */
    public function deleteAction(Advert $advert, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /*$advert = $em->getRepository('NBPlatformBundle:Advert')->find($id);


        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }*/

        $form = $this->get('form.factory')->create();


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $em->remove($advert);

            $em->flush();


            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");


            return $this->redirectToRoute('nb_platform_home');

        }



        return $this->render('NBPlatformBundle:Advert:delete.html.twig', array(

            'advert' => $advert,

            'form'   => $form->createView(),

        ));

    }
    public function menuAction($limit)
    {

        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository('NBPlatformBundle:Advert')->findBy(
            array(),                 // Pas de critère
            array('date' => 'desc'), // On trie par date décroissante
            $limit,                  // On sélectionne $limit annonces
            0                 // À partir du premier
        );

        return $this->render('NBPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    public function translationAction($name)
    {
        return $this->render('NBPlatformBundle:Advert:translation.html.twig', array(
            'name' => $name
        ));
    }

}
