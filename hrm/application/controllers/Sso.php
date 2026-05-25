<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sso extends CI_Controller
{
    private static $allowed_goto = [
        'dashboard/Userhome',
        'employee',
        'staff_attendance',
        'leavemanage',
        'outstationduty',
        'salary_sheet',
        'voucher',
    ];

    public function login()
    {
        $token = $this->input->get('token', TRUE);
        $goto  = $this->input->get('goto',  TRUE);

        if (empty($goto) || !in_array($goto, self::$allowed_goto, true)) {
            $goto = 'dashboard/Userhome';
        }

        if (empty($token)) {
            redirect(SERVER . '/dashboard/login');
            return;
        }

        // Validate token — parameterized query prevents SQL injection
        $query = $this->db->query(
            "SELECT * FROM sso_tokens WHERE token = ? AND used = 0 AND expires_at > NOW() LIMIT 1",
            [$token]
        );

        if ($query->num_rows() !== 1) {
            redirect(SERVER . '/dashboard/login');
            return;
        }

        $row    = $query->row();
        $userid = $row->userid;

        // Consume immediately — one-time use, prevents replay attacks
        $this->db->where('token', $token)->update('sso_tokens', ['used' => 1]);

        // Find the matching HRM user (SkyERP userid == HRM user_name)
        $user_q = $this->db->where('user_name', $userid)
                           ->where('user_status', 1)
                           ->get('users');

        if ($user_q->num_rows() !== 1) {
            // No HRM account with that username — fall back to login
            redirect(SERVER . '/dashboard/login');
            return;
        }

        $user = $user_q->row();

        // Load company info (same as normal login flow)
        $this->load->model('Authenticate_model');
        $this->Authenticate_model->LoadCompanyInfo();

        // Build CI session — identical fields to Authenticate_model::Login()
        $this->session->set_userdata([
            'created_by'   => $user->user_id,
            'user_ref_id'  => $user->ref_id,
            'company_id'   => $user->company_id,
            'branch_id'    => $user->branch_id,
            'user_name'    => $user->user_name,
            'user_role'    => $user->user_role,
            'display_name' => $user->display_name,
            'validate'     => true,
        ]);

        redirect(SERVER . '/' . $goto);
    }
}
