<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SLATemplateExport implements FromView, WithEvents
{
    use RegistersEventListeners;
    private $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    public function view(): View
    {
        return view('pages.admin.request-slas.exports.template');
    }
}
