<?php

declare(strict_types=1);

namespace SuperVMar\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Stub required by DoctrineFixturesBundle auto-configuration.
 * The actual test fixtures live in tests/Fixtures/.
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // No application fixtures — all fixtures are in tests/Fixtures/
    }
}
