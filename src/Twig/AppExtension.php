<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;

class AppExtension extends \Twig\Extension\AbstractExtension
{
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('formatRole', [$this, 'formatRole']),
        ];
    }

    public function formatRole($symfonyRole): string
    {
        $role = str_replace('ROLE_', '', $symfonyRole);
        $role = str_replace('_', ' ', $role);
        $role = ucfirst(strtolower($role));

        return $role;
    }
}