<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Theme;
use AppBundle\Form\ThemeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 * Class AdminController
 * @package AppBundle\Controller
 */
class AdminController extends Controller
{

    /**
     * @Route("/",
     *     name="admin_home"
     * )
     * @return Response
     */
    public function indexAction()
    {
        return $this->render("admin/index.html.twig");

    }

    /**
     * @Route("/login",
     *     name="admin_login"
     * )
     * @return Response
     */
    public function admin_loginAction()
    {
        // Permet de gérer les erreurs et de garder le nom du user pour ne pas qu'il le retape
        $securityUtils = $this->get("security.authentication_utils");
        $lastUserName = $securityUtils->getLastUsername();
        $error = $securityUtils->getLastAuthenticationError();

        return $this->render("default/generic-login.html.twig", [
            "action" => $this->generateUrl("admin_login_check"),
            "title" => "Login des Administrateurs",
            "userName" => $lastUserName,
            "error" => $error
        ]);
    }

    /**
     * @Route("/themes",
     *     name="admin_themes"
     * )
     * @return Response
     */
    public function themeAction(Request $request)
    {
        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");

        $themeList = $repository->findAll();

        //Génération du formulaire
        $theme = new Theme();
        $form = $this->createForm(ThemeType::class, $theme);

        //Hydratation de l'entité
        $form->handleRequest($request);

        //Traitement de notre formulaire
        if ($form->isSubmitted() and $form->isValid()) {
            //Persistance de l'entité dans la base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($theme);
            $em->flush();

            //Redirection pour éviter de poster 2 fois les données et ainsi
            //ne pas avoir d'erreur SQL
            return $this->redirectToRoute("admin_themes");
        }

        return $this->render("admin/theme.html.twig", [
            "themeList" => $themeList,
            "themeForm" => $form->createView()
        ]);
    }

    /**
     * @Route("/secure",
     *     name="admin_only_god"
     * )
     * @return Response
     */
    public function onlyGodAction()
    {
        return $this->render("admin/god.html.twig");
    }

}