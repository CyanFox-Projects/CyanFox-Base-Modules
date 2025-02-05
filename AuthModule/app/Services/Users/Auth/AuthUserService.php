<?php

namespace Modules\AuthModule\Services\Users\Auth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\AuthModule\Models\User;

class AuthUserService
{
    /**
     * This variable represents the user object.
     *
     * @var User
     */
    private $user;

    /**
     * Constructs a new instance of the class.
     *
     * @param  $user  User user object to be assigned to the $user property.
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Regenerates the remember token for the user.
     *
     * @return string The newly generated remember token.
     */
    public function regenerateRememberToken(): string
    {
        $rememberToken = Str::password();

        $this->user->setRememberToken($rememberToken);
        $this->user->save();

        return $rememberToken;
    }

    /**
     * Retrieves the avatar URL for the user.
     *
     * If the user has a custom_avatar_url set, that URL will be returned.
     * Otherwise, the method will attempt to retrieve the user's avatar image from the storage.
     * If the avatar image exists in the storage, the URL will be returned with a cache-busting query parameter.
     * If no avatar image exists in the storage, the method will return the default avatar URL with a username placeholder.
     *
     * @return string The avatar URL.
     */
    public function getAvatarURL(): string
    {
        if ($this->user->custom_avatar_url) {
            return $this->user->custom_avatar_url;
        }

        $filePath = 'avatars/'.$this->user->id.'.png';
        if (Storage::disk('public')->exists($filePath)) {
            return asset('storage/'.$filePath).'?v='.md5_file(storage_path('app/public/'.$filePath));
        }

        return str_replace('{username}', urlencode($this->user->username), setting('authmodule.profile.default_avatar_url'));
    }

    /**
     * Returns the color scheme for the user.
     *
     * This method checks if the user's theme is in the array of dark themes,
     * and returns 'dark' if it is, otherwise it returns 'light'.
     *
     * @return string The color scheme. Possible values are 'dark' or 'light'.
     */
    public function getColorScheme(): string
    {
        if ($this->user->theme == 'dark') {
            return 'dark';
        } else {
            return 'light';
        }

    }

    /**
     * Retrieves the session manager for the authenticated user.
     *
     * @return AuthUserSessionService The session manager for the authenticated user.
     */
    public function getSessionManager(): AuthUserSessionService
    {
        return new AuthUserSessionService($this->user);
    }

    /**
     * Returns the instance of AuthUserTwoFactorService for managing two-factor authentication.
     *
     * @return AuthUserTwoFactorService The instance of AuthUserTwoFactorService.
     */
    public function getTwoFactorManager(): AuthUserTwoFactorService
    {
        return new AuthUserTwoFactorService($this->user);
    }
}
