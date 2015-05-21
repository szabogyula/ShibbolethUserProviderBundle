<?php 
namespace Niif\ShibbolethUserProviderBundle\Security;

use KULeuven\ShibbolethBundle\Security\ShibbolethUserProviderInterface;
use KULeuven\ShibbolethBundle\Security\ShibbolethUserToken;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\User;    
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserProvider implements ShibbolethUserProviderInterface
{
    protected $entitlement_prefix;
    protected $admin_role_regexp;
    protected $user_role_regexp;
    protected $guest_role_regexp;
    protected $generate_custom_roles;
    protected $entitlement_serverparameter;

    public function __construct(
        $entitlement_prefix,
        $admin_role_regexp,
        $user_role_regexp,
        $guest_role_regexp,
        $generate_custom_roles,
        $entitlement_serverparameter)
    {
        $this->entitlement_prefix = $entitlement_prefix;
        $this->admin_role_regexp = $admin_role_regexp;
        $this->user_role_regexp = $user_role_regexp;
        $this->guest_role_regexp = $guest_role_regexp;
        $this->generate_custom_roles = $generate_custom_roles;
        $this->entitlement_serverparameter = $entitlement_serverparameter;
    }
    public function loadUserByUsername($username)
    {
        $roles = array();
        if (array_key_exists($this->entitlement_serverparameter,$_SERVER)) {
            foreach(explode(';',$_SERVER[$this->entitlement_serverparameter]) as $e) {
                if (preg_match("/^".$this->entitlement_prefix."/", $e)) {
                    $entitlement_value = preg_replace("/".$this->entitlement_prefix."/", "", $e);
                    if (preg_match($this->admin_role_regexp,$entitlement_value)){
                        $roles[] = "ROLE_ADMIN";
                    }
                    elseif (preg_match($this->user_role_regexp,$entitlement_value)){
                        $roles[] = "ROLE_USER";
                    }
                    elseif (preg_match($this->guest_role_regexp,$entitlement_value)){
                        $roles[] = "ROLE_GUEST";
                    }
                    elseif ($this->generate_custom_roles) {
                        $roles[] = 'ROLE_' . $entitlement_value;
                    }
                }
            }
        }
        $user = new User($username,null,$roles);	    
        return $user;            
    }

    public function createUser(ShibbolethUserToken $token){
        // Create user object using shibboleth attributes stored in the token. 
        // 

        $user = new User();
        $user->setUid($token->getUsername());
        //$user->setSurname($token->getSurname());
        //$user->setGivenName($token->getGivenName());
        //$user->setMail($token->getMail());
        // If you like, you can also add default roles to the user based on shibboleth attributes. E.g.:
        // if ($token->isStudent()) $user->addRole('ROLE_STUDENT');
        // elseif ($token->isStaff()) $user->addRole('ROLE_STAFF');
        // else $user->addRole('ROLE_USER');

        //$user->save();
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
