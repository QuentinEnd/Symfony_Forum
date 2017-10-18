<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;
use AppBundle\Form\PostType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        //On récupère les .... dans la base de données les données de la table thème grâce à l'entité Theme
        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");

        //On récupère les .... dans la base de données les données de la table post grâce à l'entité Post
        $postRepository = $this->getDoctrine()
            ->getRepository("AppBundle:Post");

        //On applique la méthode créée dans le ThemeRepository grâce à QueryBuilder
        $list = $repository->getAllThemes()->getArrayResult();

        //On applique la méthode créée dans le PostRepository grâce à QueryBuilder
        $postListByYear = $postRepository->getPostsGroupedByYear();

        return $this->render('default/index.html.twig',
            [
                "themeList" => $list,
                "postList" => $postListByYear
            ]);
    }

    /**
     * @param $slug
     * @Route("/theme/{slug}",
     *       name="theme_details"
     * )
     * @return Response
     */
    public function themeAction($slug, Request $request)
    {

        $repository = $this->getDoctrine()
            ->getRepository("AppBundle:Theme");

        $theme = $repository->findOneBySlug($slug);

        $allThemes = $repository->getAllThemes()->getArrayResult();

        //Si thème est null, je lève une exception
        if (!$theme) {
            throw new NotFoundHttpException("Thème introuvable");
        }

        return $this->render('default/theme.html.twig', [
            "theme" => $theme,
            "postList" => $theme->getPosts(),
            "all" => $allThemes
        ]);
    }

    /**
     * @Route("/inscription",
     *     name="author_registration"
     * )
     * @param Request $request
     * @return Response
     */
    public function registrationAction(Request $request)
    {
        #Instanciation du nouvel objet Author
        $author = new Author();

        #Création du formulaire lié à l'entité Author
        $form = $this->createForm(
            AuthorType::class,
            $author
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //Encodage du mot de passe

            //Récupération de l'encoder qui est lié à une entité particulière
            $encoderFactory = $this->get("security.encoder_factory");
            $encoder = $encoderFactory->getEncoder($author);
            $author->setPassword($encoder->encodePassword($author->getPlainPassword(), null));
            $author->setPlainPassword(null);

            //Enregistrement dans la base de données du nouvel Author
            $em->persist($author);
            $em->flush();
        }

        return $this->render("default/author-registration.html.twig", [
            "registrationForm" => $form->createView()
        ]);

    }

    /**
     * @Route("/author-login",
     *     name="author_login"
     * )
     * @return Response
     */
    public function authorLoginAction()
    {

        $securityUtils = $this->get("security.authentication_utils");

        return $this->render(":default:generic-login.html.twig",
            [
                "title" => "Identification des rédacteurs",
                "action" => $this->generateUrl("author_login_check"),
                "userName" => $securityUtils->getLastUsername(),
                "error" => $securityUtils->getLastAuthenticationError()
            ]);
    }

    /**
     * @Route("/test-service")
     * @return Response
     */
    public function testServiceAction()
    {
        $helloService = $this->get("service.hello");
        $helloService->setName("Bob");

        $newHelloService = $this->get("service.hello");

        $message = $newHelloService->sayHello(). ' '. $helloService->sayHello();

        return $this->render("default/test-service.html.twig", ["message" => $message]);

    }

}
