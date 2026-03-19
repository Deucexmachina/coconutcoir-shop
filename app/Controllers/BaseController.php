<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->helpers = ['url', 'form'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    protected function currentUser(): ?array
    {
        return $this->session->get('user');
    }

    protected function requireBuyer()
    {
        $user = $this->currentUser();
        if ($user === null || $user['role'] !== 'buyer') {
            $target = current_url();
            $suffix = $target !== '' ? '?redirect_to=' . rawurlencode($target) : '';
            return redirect()->to('/login' . $suffix)->with('error', 'Please log in as buyer first.');
        }

        return null;
    }

    protected function requireSeller()
    {
        $user = $this->currentUser();
        if ($user === null || $user['role'] !== 'seller') {
            return redirect()->to('/seller/login')->with('error', 'Please log in as seller first.');
        }

        return null;
    }
}
