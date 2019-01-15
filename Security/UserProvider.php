<?php


namespace Niif\ShibbolethUserProviderBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    protected $default_role;
    protected $entitlement_prefix;
    protected $admin_role_regexp;
    protected $user_role_regexp;
    protected $guest_role_regexp;
    protected $generate_custom_roles;
    protected $custom_role_prefix;
    protected $custom_additional_role;
    protected $entitlement_serverparameter;

    public function __construct(
        $default_role,
        $entitlement_prefix,
        $admin_role_regexp,
        $user_role_regexp,
        $guest_role_regexp,
        $generate_custom_roles,
        $custom_role_prefix,
        $custom_additional_role,
        $entitlement_serverparameter
    ) {
        $this->default_role = $default_role;
        $this->entitlement_prefix = $entitlement_prefix;
        $this->admin_role_regexp = $admin_role_regexp;
        $this->user_role_regexp = $user_role_regexp;
        $this->guest_role_regexp = $guest_role_regexp;
        $this->generate_custom_roles = $generate_custom_roles;
        $this->custom_role_prefix = $custom_role_prefix;
        $this->custom_additional_role = $custom_additional_role;
        $this->entitlement_serverparameter = $entitlement_serverparameter;
    }
    public function loadUserByUsername($username)
    {
        $roles = array();
        if ($this->default_role) {
            $roles[] = $this->default_role;
        }
        if (array_key_exists($this->entitlement_serverparameter, $_SERVER)) {
            foreach (explode(';', $_SERVER[$this->entitlement_serverparameter]) as $e) {
                if (preg_match('/^'.$this->entitlement_prefix.'/', $e)) {
                    $entitlement_value = preg_replace('/^'.$this->entitlement_prefix.'/', '', $e);
                    if (preg_match($this->admin_role_regexp, $entitlement_value)) {
                        $roles[] = 'ROLE_ADMIN';
                    } elseif (preg_match($this->user_role_regexp, $entitlement_value)) {
                        $roles[] = 'ROLE_USER';
                    } elseif (preg_match($this->guest_role_regexp, $entitlement_value)) {
                        $roles[] = 'ROLE_GUEST';
                    } elseif ($this->generate_custom_roles) {
                        $roles[] = 'ROLE_'.preg_replace('/^'.$this->custom_role_prefix.'/', '', $entitlement_value);
                        if ($this->custom_additional_role) {
                            $roles[] = $this->custom_additional_role;
                        }
                    }
                }
            }
        }
        $roles = array_unique($roles);
        $user = new User($username, null, $roles);

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
