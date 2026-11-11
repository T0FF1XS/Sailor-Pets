<?php

class DashboardController
{
    public function index()
    {
        require_role(['Owner', 'Sales Staff', 'Inventory Staff']);

        render('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'alerts' => Alert::unread(),
            'low' => Alert::lowStock(),
            'pendingCount' => Inquiry::pendingCount(),
            'pendingList' => Inquiry::pendingList(),
            'today' => Sale::today(),
        ]);
    }

    public function markRead()
    {
        require_role(['Owner', 'Sales Staff', 'Inventory Staff']);
        Alert::markRead($_POST['read_alert']);
        redirect('dashboard');
    }
}
