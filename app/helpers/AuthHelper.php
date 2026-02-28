<?php
class AuthHelper {
    public static function isLoggedIn(){
        return isset($_SESSION['user_id']);
    }

    public static function getUser(){
        return isset($_SESSION['user_id']) ? $_SESSION : null;
    }

    public static function requireLogin(){
        if(!self::isLoggedIn()){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public static function hasRole($role){
        if(is_array($role)){
            return isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], $role);
        }
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }
    
    public static function requireRole($roles = []){
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        if(!self::isLoggedIn() || !in_array($_SESSION['user_role'], $roles)){
            // Redirect to dashboard or unauthorized page
            header('Location: ' . URLROOT . '/dashboard'); 
            // If strictly unauthorized, maybe show an error, but dashboard is safer fallback
            exit;
        }
    }
}
