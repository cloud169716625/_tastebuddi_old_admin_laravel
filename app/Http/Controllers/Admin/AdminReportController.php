<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report;

class AdminReportController extends AdminBaseController
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
        return view('admin.reports.index');
    }

    public function show()
    {
        return view('admin.reports.show');
    }
}
