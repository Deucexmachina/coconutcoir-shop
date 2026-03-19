<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login(): string
    {
        return view('auth/login', [
            'title' => 'Login',
            'role' => 'buyer',
            'redirect_to' => (string) $this->request->getGet('redirect_to'),
        ]);
    }

    public function sellerLogin(): string
    {
        return view('auth/login', [
            'title' => 'Login',
            'role' => 'seller',
            'redirect_to' => '',
        ]);
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

        if ($role === 'buyer') {
            $guestCart = $this->session->get('guest_cart');
            if (is_array($guestCart) && $guestCart !== []) {
                $cartModel = new CartItemModel();
                $productModel = new ProductModel();

                foreach ($guestCart as $productId => $qty) {
                    $pid = (int) $productId;
                    $qty = max(1, (int) $qty);
                    if ($pid <= 0) {
                        continue;
                    }

                    $product = $productModel->find($pid);
                    if (! $product || (int) $product['is_active'] !== 1) {
                        continue;
                    }

                    $existing = $cartModel
                        ->where('user_id', $user['id'])
                        ->where('product_id', $pid)
                        ->first();
                    $existingQty = $existing ? (int) $existing['quantity'] : 0;
                    $newQty = min((int) $product['stock'], $existingQty + $qty);
                    if ($newQty < 1) {
                        continue;
                    }

                    if ($existing) {
                        $cartModel->update($existing['id'], ['quantity' => $newQty]);
                    } else {
                        $cartModel->insert([
                            'user_id' => $user['id'],
                            'product_id' => $pid,
                            'quantity' => $newQty,
                        ]);
                    }
                }

                $this->session->remove('guest_cart');
            }
        }

        if ($role === 'seller') {
            return redirect()->to('/seller/storefront')->with('success', 'Welcome back, seller.');
        }

        $redirectTo = (string) $this->request->getPost('redirect_to');
        if ($redirectTo === '') {
            $redirectTo = '/storefront';
        }
        return redirect()->to($redirectTo)->with('success', 'Welcome back.');
    }

    public function logout()
    {
        $this->session->remove('user');
        return redirect()->to('/')->with('success', 'You have been logged out.');
    }
}
