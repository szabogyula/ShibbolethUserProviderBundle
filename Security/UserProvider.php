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

use Symfony\Component\Yaml\Yaml;

class UserProvider implements ShibbolethUserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $file = __DIR__.'/../../../../app/config/parameters.yml';
        $parameters = Yaml::parse(file_get_contents($file));

        $admin_role_regexp = $parameters['parameters']['shib_user_provider']['admin_role_regexp'];
        $role_regexp = $parameters['parameters']['shib_user_provider']['role_regexp'];
        $entitlement_serverparameter = $parameters['parameters']['shib_user_provider']['entitlement_serverparameter'];

        $has_org = FALSE;
        $roles = array();
        if (array_key_exists($entitlement_serverparameter,$_SERVER)) {
            $role = "ROLE_USER";
            if (preg_match($admin_role_regexp,$_SERVER[$entitlement_serverparameter])){
                $role = "ROLE_ADMIN";
            }
            $roles[] = $role;
            foreach(explode(';',$_SERVER[$entitlement_serverparameter]) as $e) {
                if (preg_match($role_regexp,$e)) {
                    $roles[] = 'ROLE_' . preg_replace($role_regexp,'',$e);
                    $has_org = TRUE;
                }
            }
        }
        if (! in_array("ROLE_ADMIN",$roles) AND ! $has_org) {
            $roles=array();
        }
        $user = new User($username,null,$roles);
	    
var_dump($user);exit;
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
        else $user->addRole('ROLE_USER');

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
