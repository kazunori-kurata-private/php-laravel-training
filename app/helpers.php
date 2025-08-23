<?php

if (!function_exists('user_can')) {
    /**
     * ユーザーが指定された権限を持っているかチェック
     *
     * @param string $permission
     * @return bool
     */
    function user_can(string $permission): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        switch ($permission) {
            case 'manage_users':
                return $user->canManageUsers();
            
            case 'manage_cats':
                return $user->canManageCats();
            
            case 'manage_blogs':
                return $user->canManageBlogs();
            
            case 'create_blogs':
                return $user->canCreateBlogs();
            
            case 'edit_blogs':
                return $user->canEditBlogs();
            
            case 'is_staff':
                return $user->isStaff();
            
            case 'is_manager':
                return $user->isManager();
            
            case 'is_admin':
                return $user->isAdmin();
            
            default:
                return false;
        }
    }
}

if (!function_exists('user_role')) {
    /**
     * ユーザーのロール名を取得
     *
     * @return string|null
     */
    function user_role(): ?string
    {
        if (!auth()->check()) {
            return null;
        }

        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return 'admin';
        } elseif ($user->isManager()) {
            return 'manager';
        } elseif ($user->isStaff()) {
            return 'staff';
        }
        
        return null;
    }
}

if (!function_exists('can_edit_blog')) {
    /**
     * ユーザーが指定されたブログを編集できるかチェック
     *
     * @param mixed $blog
     * @return bool
     */
    function can_edit_blog($blog): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        
        // 管理者はブログを編集できない
        if ($user->isAdmin()) {
            return false;
        }
        
        // 自分のブログは編集可能
        if ($user->id === $blog->user_id) {
            return true;
        }
        
        return false;
    }
}

if (!function_exists('can_delete_blog')) {
    /**
     * ユーザーが指定されたブログを削除できるかチェック
     *
     * @param mixed $blog
     * @return bool
     */
    function can_delete_blog($blog): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();
        
        // 管理者はブログを削除できない
        if ($user->isAdmin()) {
            return false;
        }
        
        // 自分のブログは削除可能
        if ($user->id === $blog->user_id) {
            return true;
        }
        
        // マネージャーはスタッフのブログを削除可能
        if ($user->isManager() && $blog->user && $blog->user->isStaff()) {
            return true;
        }
        
        return false;
    }
} 