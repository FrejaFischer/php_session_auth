<?php
require_once 'Database.php';
require_once 'Logger.php';

class User extends Database
{
     /**
     * find and authenticate user
     * @param $user The user to authenticate, taken from $_POST
     * @return string|false, users email if authenticate succes, false if error
     */
    public function authenticateUser(array $user): array|false 
    {
        $sql =<<<SQL
            SELECT email FROM `users` WHERE email = ?;
        SQL;

        try{
            $email = htmlspecialchars(trim($user['email']));
            $password = htmlspecialchars(trim($user['pwd']));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([ 
                $email
            ]);

            return false; // Delete

            // TO DO
            // find users password by email

            // Use that password to compare with password from POST
            // If the same, true else false
            
            // then set token info???
            // return true if succes or false if not???



            // $userInfo = $stmt->fetchAll();

            // if(!$userInfo) {
            //     Logger::logText('No user found', 'Function:'.__FUNCTION__ ,'Line number:'.__LINE__);
            //     return false;
            // } else {
            //     return $userInfo;
            // }
            
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

     // make token
    //  $date = new DateTime();
    //  $date->modify("+30 minutes");
    //  $expiration = $date->format("Y-m-d H:i:s");
    //  $token = $expiration;

     /**
     * Validates form from insert user
     * @param $user The employee to insert, taken from $_POST
     * @return array, with error messages inside if error
     *          or empty if there is no errors
     */
    function validate(array $user): array
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
}