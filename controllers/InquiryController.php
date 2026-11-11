<?php

class InquiryController
{
    public function index()
    {
        require_role(['Owner', 'Sales Staff']);

        render('inquiries/index', [
            'pageTitle' => 'Inquiries',
            'inquiries' => Inquiry::allWithItems(),
        ]);
    }

    public function decide()
    {
        $user = require_role(['Owner', 'Sales Staff']);

        $status = ($_POST['action'] == 'approve') ? 'Approved' : 'Rejected';
        Inquiry::decide($_POST['inquiry_id'], $status, $user['user_id']);

        flash('Inquiry ' . strtolower($status) . '.');
        redirect('inquiries');
    }
}
