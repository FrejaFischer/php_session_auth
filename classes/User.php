<?php
require_once 'Database.php';
require_once 'Logger.php';

class User extends Database
{

    /**
     * Uses random_int as core logic and generates a random string
     * random_int is a pseudorandom number generator
     *
     * @param int $length
     * @return string
     */
    function getRandomStringRandomInt($length = 50)
    {
        $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pieces = [];
        $max = mb_strlen($stringSpace, '8bit') - 1;
        for ($i = 0; $i < $length; ++ $i) {
            $pieces[] = $stringSpace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    /**
     * Find users password by Email
     * @param array $user - the user to select from
     * @return array with users password or false if error 
     */
    public function getPasswordByEmail(string $userEmail): array|false
    {
        $sql =<<<SQL
            SELECT password FROM `users` WHERE email = ?;
        SQL;

        try{
            $email = htmlspecialchars(trim($userEmail));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([ 
                $email
            ]);
            
            $userInfo = $stmt->fetchAll();

            if(!$userInfo) {
                Logger::logText('No user found', 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
                return false;
            } else {
                return $userInfo[0];
            }
        } catch(PDOException $e){
            Logger::logText('Error inserting user', $e, 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
            return false;
        }

    }
     /**
     * find and authenticate user
     * @param $user The user to authenticate, taken from $_POST
     * @return string|false, users email if authenticate succes, false if error
     */
    public function authenticateUser(array $user): bool
    {
        $password = htmlspecialchars(trim($user['pwd']));
        $dbPassword = $this->getPasswordByEmail($user['email']);

        if(!$dbPassword) {
            return false;
        } else {
            $dbPassword = $dbPassword['password'];
            if(!password_verify($password, $dbPassword)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * set token on user
     * @param array $user to set token on
     * @return bool true if succes, false if error 
     */
    public function setTokens(array $user): bool
    {
        $sql =<<<SQL
            UPDATE `users` SET `token_expires_at`=?,`token`=? WHERE email = ?;
        SQL;

        try{
            // make token
            $date = new DateTime();
            $date->modify("+30 minutes");
            $expireDate = $date->format("Y-m-d H:i:s");
            $token = $this->getRandomStringRandomInt();

            $email = htmlspecialchars(trim($user['email']));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([ 
                $expireDate,
                $token,
                $email
            ]);

            // Store Token in Session
            if($stmt->rowCount() === 1) {
                $_SESSION['token']=$token;
                Logger::logText($_SESSION['token']);
            }

            return $stmt->rowCount() === 1;
            
        } catch(PDOException $e){
            Logger::logText('Error inserting user', $e, 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
            return false;
        }
    }

    /**
     * Checks if token from session is the same as in db,
     * And if the token is expired
     * @param string $token to check
     * @return bool true if valid token, false if not or error
     */
    public function checkToken(string $token): bool
    {

        $sql =<<<SQL
            SELECT token, token_expires_at FROM `users` WHERE token = ?;
        SQL;

        try{
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([ 
                $token
            ]);
            
            $tokenInfo = $stmt->fetchAll();

            if(!$tokenInfo) {
                Logger::logText('No token found', 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
                return false;
            } else {
                $date = new DateTime();
                $formattedDate = $date->format("Y-m-d H:i:s");

                // Check if token is expired
                if($tokenInfo[0]['token_expires_at'] < $formattedDate) {
                    Logger::logText('expired token');
                    return false;
                } else {
                    Logger::logText('not expired');
                    return true;
                }
            }
        } catch(PDOException $e){
            Logger::logText('Error inserting user', $e, 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
            return false;
        }
    }

    /**
     * insert user
     * @param $user The user to insert, taken from $_POST
     * @return boolean, true if success, false if error
     */
    public function insert(array $user): bool
    {
        $sql =<<<SQL
            INSERT INTO `users`(`name`, `email`, `password`) 
            VALUES (?, ?, ?);
        SQL;
        
        try{
            $name = htmlspecialchars(trim($user['name']));
            $email = htmlspecialchars(trim($user['email']));

            // hash
            $password = htmlspecialchars(trim($user['pwd']));
            $password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $name, 
                $email, 
                $password
            ]);
            
            return $stmt->rowCount() === 1;
            
            
        } catch(PDOException $e){
            Logger::logText('Error inserting user', $e, 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
            return false;
        }
    }

     /**
     * Validates form from login
     * @param $user The users form inputs to validate
     * @return array, with error messages inside if error
     *          or empty if there is no errors
     */
    function validateLogin(array $user): array
    {

        $email =trim($user['email']) ?? '';
        $password =trim($user['pwd']) ?? '';
        
        
        $validationErrors = [];
    
        if($email === '') {
            $validationErrors[] = 'Email is mandatory';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $validationErrors[] = 'Invalid email';
        }
    
        if($password === ''){
            $validationErrors[] = 'Password is mandatory';
        }
    
        return $validationErrors;
    }

     /**
     * Validates form from insert user
     * @param $user The users form inputs from $_POST
     * @return array, with error messages inside if error
     *          or empty if there is no errors
     */
    function validateSignup(array $user): array
    {

        $name = trim($user['name']) ?? '';
        $email =trim($user['email']) ?? '';
        $password =trim($user['pwd']) ?? '';
        $repeatPassword =trim($user['repeatPwd']) ?? '';
        
        
        $validationErrors = [];
    
        if($name === '') {
            $validationErrors[] = 'Name is mandatory';
        }
       
        if($email === '') {
            $validationErrors[] = 'Email is mandatory';
        } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $validationErrors[] = 'Invalid email';
        }
    
        if($password === ''){
            $validationErrors[] = 'Password is mandatory';
        } 
        
        if($repeatPassword === ''){
            $validationErrors[] = 'Repeat password';
        }elseif($password !== $repeatPassword) {
            $validationErrors[] = 'Passwords must match';
        }
    
        return $validationErrors;
    }

    /**
     * Logs the user out - deletes Session
     */
    public function logout(): void
    {
        if(isset($COOKIE[session_name()])) {
            setcookie(session_name(),'',time()-86400,'/');
        }
        session_destroy();

        header('Location: ' . BASE_URL . '/index.php');
    }

    /**
     * Check if user is logged in with valid token
     * @param string $token - users token
     * @return bool true if logged in, false if not
     */
    public function isLoggedIn(string $token): bool
    {
        // Check if Logged in (token is present)
        if($token === ''){
            return false;
        } else {
            if($this->checkToken($token)) {
                return true;
            } else {
                return false;
            }
        }
    }
}