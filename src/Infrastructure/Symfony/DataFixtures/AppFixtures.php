<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Base class for the data fixtures
 * This is a just helper to simplify the code a bit on the child classes
 *
 * Fixtures are used in development stage only, and prepopulate the database
 *   with meaningul data
 */
abstract class AppFixtures extends Fixture
{
    private ObjectManager $em;

    public function load(ObjectManager $manager): void
    {
        $this->em = $manager;
        foreach ($this->getEntities() as $entity) {
            $this->em->persist($entity);
            $this->em->flush();
        }
    }

    /**
     * Return entities to be saved
     * @return mixed[]
     */
    abstract public function getEntities(): iterable;
}
