<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\AnswerType;
use AppBundle\Entity\Post;
use AppBundle\Entity\Answer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AnswerController extends Controller
{

    /**
     * @Route("/answer/new/{slug}",
     *     name="new_answer"
     * )
     * @param Request $request
     * @param $slug Post
     * @return Response
     */
    public function addAnswerAction(Request $request, $slug)
    {
        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        /**
         * @var $post Post
         */
        $post = $repository->findOneBySlug($slug);

        //Gestion des nouvelles réponses
        $user = $this->getUser();
        $roles = isset($user) ? $user->getRoles() : [];
        $formView = null;

        if (in_array("ROLE_AUTHOR", $roles)) {

            //Création du formulaire pour l'ajout de nouvelles réponses
            $answer = new Answer();
            dump($user);

            $answer->setAuthor($user);
            $answer->setCreatedAt(new \DateTime());
            $answer->setPost($post);

            $form = $this->createForm(AnswerType::class, $answer);
            //Hydratation de l'entité (du formulaire)
            $form->handleRequest($request);

            //Traitement du formulaire
            if ($form->isSubmitted() and $form->isValid()) {

                //Persistance de l'entité dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($answer);
                $em->flush();

                //Redirection pour éviter de poster 2 fois les données et ainsi
                //ne pas avoir d'erreur SQL
                return $this->redirectToRoute("post_details", ["slug" => $post->getSlug()]);
            }
            //Fin de la gestion des nouvelles réponses
            return $this->render("post/details.html.twig", [
                "title" => "Nouvelle Réponse",
                "post" => $post,
                "addAnswerForm" => $form->createView()
            ]);
        }

    }

    /**
     * @Route("\answer\modif\{id}",
     *     name="modif_answer"
     * )
     * @param Request $request
     * @param Answer $answer
     * @return Response
     */
    public function editAnswerAction(Request $request, Answer $answer){

        $post = $answer->getPost();

        //Sécurité de l'opération de modification de post
        $user = $this->getUser();
        $roles = isset($user) ? $user->getRoles() : [];
        $userId = isset($user) ? $user->getId() : null;

        if (!in_array("ROLE_AUTHOR", $roles) || $userId != $answer->getAuthor()->getId()) {
            throw new AccessDeniedHttpException("Vous n'avez pas les droits pour modifier cette réponse");
        }
        //Création du formulaire de modification de post
        $form = $this->createForm(AnswerType::class, $answer);

        //Hydratation de l'entité
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();

            return $this->redirectToRoute("post_details", ["id" => $answer->getId()]);
        }

        return $this->render("post/details.html.twig", [
            "title" => "Modifier sa réponse",
            "post" => $post,
            "editAnswerForm" => $form->createView()
        ]);

    }


}
