<?php

namespace App\Services\Csp\Policies;

use Spatie\Csp\Policies\Policy;
use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;

class CustomPolicies extends Policy
{
    public function configure()
    {
        $this->setDefaultPolicies();
        $this->addGoogleFontPolicies();
        $this->addDataImgPolicies();
        $this->addJSResourcesPolicies();
    }

    private function setDefaultPolicies()
    {
        return $this->addDirective(Directive::BASE, 'self')
            ->addDirective(Directive::CONNECT, 'self')
            ->addDirective(Directive::DEFAULT, 'self')
            // ->addDirective(Directive::FORM_ACTION, 'self')
            ->addDirective(Directive::IMG, 'self')
            // ->addDirective(Directive::MEDIA, 'self')
            // ->addDirective(Directive::OBJECT, 'self')
            ->addDirective(Directive::FONT, 'self')
            ->addDirective(Directive::SCRIPT, 'self')
            ->addDirective(Directive::STYLE, 'self')
            ->addNonceForDirective(Directive::SCRIPT)
            ->addNonceForDirective(Directive::STYLE);
    }

    private function addGoogleFontPolicies()
    {
        $this->addDirective(Directive::FONT, [
                'fonts.gstatic.com',
                'fonts.googleapis.com',
                'fontawesome.com',
                'cdnjs.cloudflare.com',
                'data:'
            ])
            ->addDirective(Directive::STYLE, [
                'fonts.googleapis.com',
                'fontawesome.com',
                'cdnjs.cloudflare.com',
                'cdn.jsdelivr.net'
            ]);
    }

    private function addDataImgPolicies()
    {
        $this->addDirective(Directive::IMG, [
                'data:',
                'blob:'
            ]);
    }

    private function addJSResourcesPolicies()
    {
        $this->addDirective(Directive::SCRIPT, [
            'code.jquery.com',
            'cdn.datatables.net',
            'cdn.jsdelivr.net',
            'ajax.googleapis.com',
            'maxcdn.bootstrapcdn.com',
            'cdnjs.cloudflare.com',
        ]);
    }
}
