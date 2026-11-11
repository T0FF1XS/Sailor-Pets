<?php

class AuthController
{
    public function login()
    {
        if (current_user()) {
            redirect('dashboard');
        }

        render('auth/login', ['pageTitle' => 'Staff Login']);
    }

    public function check()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (LoginAttempt::tooMany($ip)) {
            flash('Too many wrong tries. Please wait 15 minutes and try again.');
            redirect('login');
        }

        $found = User::findByEmail(trim($_POST['email']));

        if ($found && password_verify($_POST['password'], $found['password'])) {
            LoginAttempt::clear($ip);
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'user_id' => $found['user_id'],
                'name' => $found['first_name'] . ' ' . $found['last_name'],
                'role' => $found['role_name'],
            ];
            redirect('dashboard');
        }

        LoginAttempt::add($ip);
        flash('Wrong email or password.');
        redirect('login');
    }

    public function logout()
    {
        session_destroy();
        redirect('home');
    }
}
