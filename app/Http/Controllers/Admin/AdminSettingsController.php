<?php

namespace App\Http\Controllers\Admin;

class AdminSettingsController extends AdminBaseController
{
    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('admin.settings.index');
    }
}