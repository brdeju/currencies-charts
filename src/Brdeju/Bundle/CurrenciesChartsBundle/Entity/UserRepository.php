<?php

namespace Brdeju\Bundle\CurrenciesChartsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserProviderInterface {

    public function isAvailableUsernameAndEmail($username, $email, $id = null) {
        $qb = $this->createQueryBuilder('u')
                    ->where('u.username = :username OR u.email = :email')
                    ->setParameter('username', $username)
                    ->setParameter('email', $email);

        if( $id != null ) {
            $qb->andWhere('u.id != :id')->setParameter(':id', $id);
        }
        
        $query = $qb->getQuery();
        $user = $query->getResult();
        if( count( $user ) > 0 ) {
            return false;
        } else {
            return true;
        }
    }
    
    public function loadUserByUsername($username) {
        $qb = $this->createQueryBuilder('u')
                    ->where('u.username = :username OR u.email = :email')
                    ->setParameter('username', $username)
                    ->setParameter('email', $username)
                    ->getQuery();
        
        try {
            $user = $qb->getSingleResult();
            return $user;
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find user "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }
     }
     
     public function refreshUser(UserInterface $user) {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->loadUserByUsername($user->getUsername());  
    }
    
    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }
    
}
