<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of active medical services.
     */
    public function index(): View
    {
        $services = Service::active()
            ->ordered()
            ->get();

        return view('user.services.index', compact('services'));
    }

    /**
     * Display details of a specific service.
     */
    public function show(Service $service): View
    {
        if (! $service->is_active) {
            abort(404, 'ไม่พบบริการนี้หรือปิดการให้บริการแล้ว');
        }

        $service->load(['therapists' => function ($query) {
            $query->where('is_active', true);
        }]);

        return view('user.services.show', compact('service'));
    }
}
