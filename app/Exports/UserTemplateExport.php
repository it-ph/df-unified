<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class UserTemplateExport implements FromView, WithEvents
{
    use RegistersEventListeners;
    private $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
    }

    public function view(): View
    {
        $user = in_array('admin', $this->roles) ? 'admin' : 'user';

        return view('pages.admin.users.exports.template', [
            'user' => $user,
        ]);
    }
}
