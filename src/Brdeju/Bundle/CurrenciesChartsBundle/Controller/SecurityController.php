<?php

namespace Brdeju\Bundle\CurrenciesChartsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Brdeju\Bundle\CurrenciesChartsBundle\Entity as Model;
use Brdeju\Bundle\CurrenciesChartsBundle\Form\UserType;

class SecurityController extends Controller {

    /**
     * @Route("/login", name="login_path")
     * @Template()
     */
    public function loginAction(Request $request) {
        $authentication_utils = $this->get('security.authentication_utils');
        $error = $authentication_utils->getLastAuthenticationError();
        $last_username = $authentication_utils->getLastUsername();
                
        if( !is_null( $error ) ) {
            $request->getSession()->getFlashBag()
                        ->set('error-notice', $error->getMessageKey()); 
        }

        return array(
            'last_username' => $last_username,
            'error' => $error
        );
    }

    /**
     * @Route("/login_check", name="check_path")
     */
    public function loginCheckAction() {
        
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction() {
        
    }

    /**
     * @Route("/register", name="register")
     * @Template("CurrenciesChartsBundle:Security:register.html.twig")
     */
    public function registerAction(Request $request) {
        $flashbag = $request->getSession()->getFlashBag();

        $data = new Model\User();
        $form = $this->createForm(new UserType(), $data, array(
            'action' => $this->generateUrl('register'),
            'method' => 'POST'
        ));
        $form->handleRequest($request);
        
        if($form->isValid()) {
            $available_username_and_email = $this->getDoctrine()->getManager()
                                                ->getRepository('CurrenciesChartsBundle:User')
                                                ->isAvailableUsernameAndEmail($data->getUsername(),$data->getEmail());

            if( $available_username_and_email ) {
                $em = $this->getDoctrine()->getEntityManager(); 
                $newUser = $form->getData();
                
                $hash = $this->get('security.encoder_factory')
                            ->getEncoder($newUser)
                            ->encodePassword($newUser->getPassword(), $newUser->getSalt());
                $newUser->setPassword($hash);
                
                $em->persist($newUser);
                $em->flush();

                $flashbag->set('success-notice', 'security.register.messages.created');
                return $this->redirect($this->generateUrl('login_path'));
            } else {
                $flashbag->set('error-notice', 'security.register.messages.unavailable');
            }
        }
        
        return array( 'form' => $form->createView() );
    }

}
