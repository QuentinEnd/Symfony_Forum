<?php
/**
 * Created by PhpStorm.
 * User: quent
 * Date: 06/09/2017
 * Time: 10:10
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Author;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AuthorFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $encoderFactory = $this->container->get("security.encoder_factory");
        $encoder = $encoderFactory->getEncoder(new Author());
        $password = $encoder->encodePassword("123", null);

        $author = new Author();
        $author->setName("Chattam")
            ->setFirstName("Maxime")
            ->setEmail("maxime.ch@gmail.com")
            ->setPassword($password);

        $this->addReference("auteur_1", $author);
        $manager->persist($author);

        $author = new Author();
        $author->setName("Meyer")
            ->setFirstName("Deon")
            ->setEmail("deon.meyer@gmx.com")
            ->setPassword($password);

        $this->addReference("auteur_2", $author);
        $manager->persist($author);

        $author = new Author();
        $author->setName("Trump")
            ->setFirstName("Donald")
            ->setEmail("dodo.trump@whitehouse.com")
            ->setPassword($password);

        $this->addReference("auteur_3", $author);
        $manager->persist($author);

        $author = new Author();
        $author->setName("Mankell")
            ->setFirstName("Henning")
            ->setEmail("menkell.h@gmail.com")
            ->setPassword($password);

        $this->addReference("auteur_4", $author);
        $manager->persist($author);

        $manager->flush();

    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}