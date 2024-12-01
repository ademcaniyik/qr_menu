<?php
class Auth extends Controller {
    private $userModel;
    private $businessModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('User');
        $this->businessModel = $this->model('Business');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = Security::sanitizePost($_POST);

            // Init data
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'business_name' => trim($_POST['business_name']),
                'business_phone' => trim($_POST['business_phone']),
                'business_address' => trim($_POST['business_address']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'business_name_err' => ''
            ];

            // Validate data
            if (empty($data['name'])) {
                $data['name_err'] = 'Lütfen adınızı girin';
            } elseif ($this->userModel->findUserByUsername($data['name'])) {
                $data['name_err'] = 'Bu kullanıcı adı zaten kullanılıyor';
            }

            if (empty($data['email'])) {
                $data['email_err'] = 'Lütfen email adresinizi girin';
            } elseif (!Security::validateEmail($data['email'])) {
                $data['email_err'] = 'Geçerli bir email adresi girin';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_err'] = 'Email adresi zaten kayıtlı';
            }

            if (empty($data['password'])) {
                $data['password_err'] = 'Lütfen şifre girin';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'Şifre en az 6 karakter olmalıdır';
            }

            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Lütfen şifrenizi tekrar girin';
            } else {
                if ($data['password'] != $data['confirm_password']) {
                    $data['confirm_password_err'] = 'Şifreler eşleşmiyor';
                }
            }

            if (empty($data['business_name'])) {
                $data['business_name_err'] = 'Lütfen işletme adını girin';
            }

            // Make sure errors are empty
            if (empty($data['name_err']) && empty($data['email_err']) && 
                empty($data['password_err']) && empty($data['confirm_password_err']) &&
                empty($data['business_name_err'])) {
                
                // Hash Password
                $data['password'] = Security::hashPassword($data['password']);

                // Register User
                if ($this->userModel->register($data)) {
                    // Get the new user
                    $user = $this->userModel->findUserByEmail($data['email']);
                    
                    // Create business
                    $businessData = [
                        'user_id' => $user->id,
                        'name' => $data['business_name'],
                        'phone' => $data['business_phone'],
                        'address' => $data['business_address'],
                        'description' => '' // Boş açıklama
                    ];
                    
                    if ($this->businessModel->add($businessData)) {
                        flash('register_success', 'Kayıt başarılı, giriş yapabilirsiniz');
                        redirect('auth/login');
                    } else {
                        die('Bir şeyler yanlış gitti');
                    }
                } else {
                    die('Bir şeyler yanlış gitti');
                }
            } else {
                // Load view with errors
                $this->view('auth/register', $data);
            }
        } else {
            // Init data
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'business_name' => '',
                'business_phone' => '',
                'business_address' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => '',
                'business_name_err' => ''
            ];

            // Load view
            $this->view('auth/register', $data);
        }
    }

    public function login() {
        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanitize POST data
            $_POST = Security::sanitizePost($_POST);
            
            // Init data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Lütfen email adresinizi girin';
            }

            // Validate Password
            if(empty($data['password'])) {
                $data['password_err'] = 'Lütfen şifrenizi girin';
            }

            // Check for user/email
            if($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                // User not found
                $data['email_err'] = 'Kullanıcı bulunamadı';
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'Şifre yanlış';

                    $this->view('auth/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('auth/login', $data);
            }

        } else {
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            // Load view
            $this->view('auth/login', $data);
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->username;
        redirect('businesses/index');
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('auth/login');
    }
}
