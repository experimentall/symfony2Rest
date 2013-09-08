<?php

namespace Demo\AuthRestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ValidatorInterface;

use Demo\AuthRestBundle\Entity\User;
use Demo\AuthRestBundle\Form\UserType;

class UserController extends Controller
{
    /**
     * Get User entity.
     *
     * @param string $email Unique email id.
     *
     * @return User
     *
     * @throws User not found.
     */
    protected function getEntity($email)
    {
        $entity = $this->getDoctrine()->getRepository('DemoAuthRestBundle:User')
            ->findOneByEmail($email);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity');
        }

        return $entity;
    }

    /**
     * Validate and add or update user form.
     *
     * @param User    $entity     User instance.
     * @param array   $parameters Form submit parameters.
     * @param boolean $encryptPwd Encrypt password on update if modified.
     *
     * @return Object
     */
    protected function processForm(User $entity, $parameters, $encryptPwd=true)
    {
        $form = $this->createForm(new UserType(), $entity);

        $form->bind($parameters);

        if ($form->isValid())
        {
            if ($encryptPwd)
                $this->encryptPassword($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $entity;
        }

        return array(
            'form' => $form,
        );
    }

    /**
     * Encrypt user password.
     *
     * @param User $entity User instance.
     */
    protected function encryptPassword(User $entity)
    {
        $factory  = $this->get('security.encoder_factory');
        $encoder  = $factory->getEncoder($entity);
        $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
        $entity->setPassword($password);
    }

    /**
     * Get User.
     *
     * @param Request $request.
     *
     * @return User
     */
    public function postGetUserAction(Request $request)
    {
        $parameters = json_decode($request->getContent(), true);
        $entity     = $this->getEntity($parameters['email']);

        return $entity;
    }

    /**
     * Add new User.
     *
     * @param Request $request.
     *
     * @return Object
     */
    public function postUserAction(Request $request)
    {
        $parameters = json_decode($request->getContent(), true);
        $entity     = new User();

        return $this->processForm($entity, $parameters);
    }

    /**
     * Update User.
     *
     * @param Request $request.
     *
     * @return Object
     */
    public function putUserAction(Request $request)
    {
        $encrypt = true;

        $parameters = json_decode($request->getContent(), true);
        $entity     = $this->getEntity($parameters['email']);

        if (!(isset($parameters['nom']) && $parameters['nom'] != ''))
        {
            $parameters['nom'] = $entity->getNom();
        }
        if (!(isset($parameters['prenom']) && $parameters['prenom'] != ''))
        {
            $parameters['prenom'] = $entity->getPrenom();
        }
        if (!(isset($parameters['password']) && $parameters['password'] != ''))
        {
            $parameters['password'] = $entity->getPassword();
            $encrypt = false;
        }

        return $this->processForm($entity, $parameters, $encrypt);
    }

    /**
     * Delete User.
     *
     * @param Request $request.
     *
     * @return array
     */
    public function deleteUserAction(Request $request)
    {
        $parameters = json_decode($request->getContent(), true);
        $entity     = $this->getEntity($parameters['email']);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return array('delete' => array('email' => $parameters['email'], 'msg' => 'delete success'));
    }

}