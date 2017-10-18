<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Theme;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Post;

class PostController extends Controller
{

    /**
     * @param $slug
     * @Route("/post/{slug}",
     *          name="post_details"
     * )
     * @return Response
     */
    public function detailsAction($slug)
    {

        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        /**
         * @var $post Post
         */
        $post = $repository->findOneBySlug($slug);

        if (!$post) {
            throw new NotFoundHttpException("post introuvable");
        }

        return $this->render("post/details.html.twig", [
            "post" => $post,
            "answerList" => $post->getAnswers()
        ]);
    }

    /**
     * @Route("/post-par-annee/{year}", name="post_by_year",
     *      requirements={"year":"\d{4}"})
     * @param $year
     * @return Response
     */
    public function postByYearAction($year)
    {
        $postRepository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        return $this->render("default/theme.html.twig", [
            "title" => "Liste des posts par année ({$year})",
            "themeParAnnee" => "Page des posts",
            "postList" => $postRepository->getPostsByYear($year)
        ]);
    }

    /**
     * @Route("/post/new/{slug}",
     *     name="new_post"
     * )
     * @param Request $request
     * @param $slug
     * @var $slug Theme
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addPostAction(Request $request, $slug)
    {
        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");

        $theme = $repository->findOneBySlug($slug);

        //Gestion des nouveaux posts
        $user = $this->getUser();
        $roles = isset($user) ? $user->getRoles() : [];
        $formView = null;

        if (in_array("ROLE_AUTHOR", $roles)) {

            //Création du formulaire pour l'ajout de nouveaux posts
            $post = new Post();
            dump($user);
            //Permet d'obtenir le thème où on se trouve directement dans la liste déroulante
            $post->setTheme($theme);
            $post->setAuthor($user);
            $post->setCreatedAt(new \DateTime());

            //Utilisation de notre service pour la génération d'un fomulaire pour un nouveau post
            $formHandler = $this->get("post.form_handler")
                ->setPost($post);

            //Traitement du formulaire
            if ($formHandler->process()) {

                //Redirection pour éviter de poster 2 fois les données et ainsi
                //ne pas avoir d'erreur SQL
                return $this->redirectToRoute("theme_details", ["slug" => $theme->getSlug()]);
            }

            $formView = $formHandler->getFormView();

        }
        //Fin de la gestion des nouveaux posts
        return $this->render("default/theme.html.twig", [
            "title" => "Nouveau Post",
            "theme" => $theme,
            "postList" => $theme->getPosts(),
            "addPostForm" => $formView
        ]);
    }


    /**
     * @Route("/post/modif/{slug}",
     *     name="post_edit"
     * )
     * @param Request $request
     * @param Post $slug
     * @return Response
     */
    public function editPostAction(Request $request, $slug)
    {
        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        $post = $repository->findOneBySlug($slug);
        $theme = $post->getTheme();

        //Sécurité de l'opération de modification de post
        $user = $this->getUser();
        $roles = isset($user) ? $user->getRoles() : [];
        $userId = isset($user) ? $user->getId() : null;
        if (!in_array("ROLE_AUTHOR", $roles) || $userId != $post->getAuthor()->getId()) {
            throw new AccessDeniedHttpException("Vous n'avez pas les droits pour modifier ce post");
        }

        //Création du formulaire de modification de post
        $form = $this->createForm(PostType::class, $post);

        //Hydratation de l'entité
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute("post_details", ["slug" => $post->getSlug()]);
        }

        return $this->render("default/theme.html.twig", [
            "title" => "Modification Post",
            "theme" => $theme,
            "postList" => $theme->getPosts(),
            "editPostForm" => $form->createView()
        ]);
    }
}