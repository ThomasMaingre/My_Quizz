<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Component\Security\Core\Security;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_admin', [$this, 'isAdmin']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('shuffle', [$this, 'shuffleArray']),
        ];
    }

    public function shuffleArray($array)
    {
        shuffle($array);
        return $array;
    }

    public function isAdmin(): bool
    {
        $user = $this->security->getUser();

        // Adjust this logic based on your User entity and property that represents the admin status
        return $user && $user->getStatus() == 2;
    }
}
