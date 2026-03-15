<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('auth/login', ['title' => 'Buyer Login', 'role' => 'buyer']);
    }

    public function sellerLogin(): string
    {
        return view('auth/login', ['title' => 'Seller Login', 'role' => 'seller']);
    }

    public function register(): string
    {
        return view('auth/register', ['title' => 'Buyer Registration']);
    }

    public function registerPost()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'full_name' => 'required|min_length[3]',
            'address' => 'required|min_length[5]',
            'mobile_number' => 'required|min_length[10]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $model = new UserModel();
        $model->insert([
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'full_name' => $this->request->getPost('full_name'),
            'address' => $this->request->getPost('address'),
            'mobile_number' => $this->request->getPost('mobile_number'),
            'role' => 'buyer',
        ]);

        return redirect()->to('/login')->with('success', 'Registration successful. You may now log in.');
    }

    public function loginPost()
    {
        $email = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');
        $role = (string) $this->request->getPost('role');

        $model = new UserModel();
        $user = $model->where('email', $email)->where('role', $role)->first();

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
        }

        unset($user['password_hash']);
        $this->session->set('user', $user);

        if ($role === 'seller') {
            return redirect()->to('/seller/storefront')->with('success', 'Welcome back, seller.');
        }

        return redirect()->to('/storefront')->with('success', 'Welcome back.');
    }

    public function logout()
    {
        $this->session->remove('user');
        return redirect()->to('/')->with('success', 'You have been logged out.');
    }
}
