<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\isGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\Contact;
use App\Form\Type\ContactFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contact controller.
 *
 * @Route("/contact")
 */
class ContactController extends AbstractController {

  private function getContacts(App\Entity\Child $child) {
//TODO : increase SQL requests !!!
    //$contacts = $child->getContacts();
    $contacts = new \Doctrine\Common\Collections\ArrayCollection();
    foreach ($child->getUsers() as $user) {
      foreach ($user->getContacts() as $_contact) {
        if (!$contacts->contains($_contact)) {
          $contacts->add($_contact);
        }
      }
      foreach ($user->getChildren() as $child) {
        foreach ($child->getContacts() as $_contact) {
          if (!$contacts->contains($_contact)) {
            $contacts->add($_contact);
          }
        }
      }
    }
    return $contacts;
  }

  private function hasContact(App\Entity\User $user, Contact $contact) {
    $answer = false;
//TODO : increase SQL requests !!!
    //foreach ($users as $user) {
    if ($user->hasContact($contact)) {
      $answer = true;
      //}
      foreach ($user->getChildren() as $child) {
        if ($user->hasContact($contact)) {
          $answer = true;
        }
      }
    }
    return $answer;
  }

  /**
   * Lists all Contact entities.
   *
   * @Route("/", name="contact")
   * @Method("GET")
   * @Template()
   */
  public function indexAction() {
    $em = $this->getDoctrine()->getManager();

    $entities = $em->getRepository('App:Contact')->findAll();

    return array(
        'entities' => $entities,
    );
  }
  /**
   * Lists child Contact entities for Presence page.
   *
   * @Route("/presence/child/{childId}", name="show_contacts_presence")
   * @Method("GET")
   * @Template("App:Contact:contactChildPresenceTable.html.twig")
   */
  public function showContactsPresenceAction($childId) {
    
      return $this->showContactsAction($childId);
  }
  /**
   * Lists child Contact entities.
   *
   * @Route("/child/{childId}", name="show_contacts")
   * @Method("GET")
   * @Template("App:Contact:contactChildListTable.html.twig")
   */
  public function showContactsAction($childId) {
    $em = $this->getDoctrine()->getManager();
    $child = $em->getRepository('App:Child')->find($childId);

    return array(
        'entities' => $this->getContacts($child),
        'child' => $child,
    );
  }

  /**
   * Creates a new Contact entity.
   *
   * @Route("/{childId}", name="admin_contact_create")
   * @Method({"POST"})
   * @Template("App:Contact:new.html.twig")
   * isGranted("ROLE_USER_ENABLED")
   */
  public function createAction(Request $request, $childId) {


    $em = $this->getDoctrine()->getManager();
    $contact = new Contact();
    $form = $this->createForm(new ContactFormType(), $contact);
    $form->bind($request);
    $user = $this->getUser();
    $child = $em->getRepository(Child::class)->find($childId);
    $response = new JsonResponse();
    $responseArray = array();
    $responseArray['error'] = '1';

    if ($user->isAdmin() or $user->hasChild($child) or $user->hasNoChild()) {
      if ($form->isValid()) {
        $user->addContact($contact);
        $child->addContact($contact);
        $contact->addChild($child);
        $contact->setUser($user);
        $em->persist($contact);
        $em->flush();
        $responseArray['error'] = '0';
        $_message = $this->get('translator')
                ->trans('cantine.contact.flash.contactCreated', array('%contactName%' => $contact->getDisplayName()));
        $this->get('session')->getFlashBag()->add('notice', $_message);

        $response->setData($responseArray);
        return $response;
      } else {
        $_message = $this->get('translator')
                ->trans('cantine.contact.flash.contactCreationError');
        $responseArray['errorMsg'] = 'ERROR MSG';
      }
    } else {
      $_message = $this->get('translator')
              ->trans('cantine.flash.rightsError');
      $responseArray['errorMsg'] = 'ERROR MSG';
    }
    $this->get('session')->getFlashBag()->add('error', $_message);
    $responseArray['entity'] = $contact;
    $responseArray['form'] = $form->createView();
    $response->setData($responseArray);
    return $response;
  }

  /**
   * Displays a form to create a new Contact entity.
   *
   * @Route("/new", name="contact_new")
   * @Method("GET")
   * @Template()
   * isGranted("ROLE_USER")
   */
  public function newAction() {
    $contact = new Contact();
    $form = $this->createForm(new ContactFormType(), $contact);

    return array(
        'entity' => $contact,
        'form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a Contact entity.
   *
   * @Route("/{id}", name="contact_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id) {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository(Contact::class)->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find Contact entity.');
    }

    $deleteForm = $this->createDeleteForm($id);

    return array(
        'entity' => $entity,
        'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing Contact entity.
   *
   * @Route("/{id}/edit", name="contact_edit")
   * @Method("GET")
   * @Template()
   * isGranted("ROLE_PARENT")
   */
  public function editAction($id) {
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $contact = $em->getRepository(Contact::class)->find($id);

    if ($this->hasContact($user, $contact) or $user->isAdmin()) {


      if (!$contact) {
        throw $this->createNotFoundException('Unable to find Contact entity.');
      }

      $editForm = $this->createForm(new ContactFormType(), $contact);

      return array(
          'entity' => $contact,
          'edit_form' => $editForm->createView(),
      );
    } else {
      throw new AccessDeniedException();
    }
  }

  /**
   * Updates an existing Contact entity.
   *
   * @Route("/{id}", name="contact_update")
   * @Method("PUT")
   * @Template("App:Contact:edit.html.twig")
   * isGranted("ROLE_USER_ENABLED")
   */
  public function updateAction(Request $request, $id) {
    $em = $this->getDoctrine()->getManager();


    $user = $this->getUser();
    $contact = $em->getRepository(Contact::class)->find($id);

//if ($user->hasChild($contact->getChild()) or $user->isAdmin()) {
    if ($this->hasContact($user, $contact) or $user->isAdmin()) {
      $response = new JsonResponse();
      $responseArray = array();
      $responseArray['error'] = 0;
      if (!$contact) {
        throw $this->createNotFoundException('Unable to find Contact entity.');
      }

//$deleteForm = $this->createDeleteForm($id);
      $editForm = $this->createForm(new ContactFormType(), $contact);
      $editForm->bind($request);

      if ($editForm->isValid()) {
        $em->persist($contact);
        $em->flush();
        $_message = $this->get('translator')
                ->trans('cantine.contact.flash.contactUpdated', array('%contactName%' => $contact->getDisplayName()));
        $this->get('session')->getFlashBag()->add('notice', $_message);

        $response->setData($responseArray);
        return $response;
      } else {
        throw $this->createNotFoundException('Unable to save contact ' + $contact->getDisplayName());
      }
    } else {
      throw new AccessDeniedException();
    }
  }

  /**
   * Deletes a Contact entity.
   *
   * @Route("/{id}", name="contact_delete")
   * @Method("DELETE")
   * isGranted("ROLE_USER_ENABLED")
   */
  public function deleteAction(Request $request, $id) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $contact = $em->getRepository(Contact::class)->find($id);
    if (!$contact) {
      throw $this->createNotFoundException('Unable to find Contact entity.');
    }

    if ($user->hasContact($contact) or $user->isAdmin()) {
      $response = new JsonResponse();
      $responseArray = array();


      $em->remove($contact);
      $em->flush();
      $responseArray['error'] = 0;

      $_message = $this->get('translator')
              ->trans('cantine.contact.flash.contactDeleted', array('%contactName%' => $contact->getDisplayName()));
      $this->get('session')->getFlashBag()->add('notice', $_message);


      $response->setData($responseArray);
      return $response;
    } else {
      $_message = $this->get('translator')
              ->trans('cantine.contact.flash.deleteForbidden', array('%contactName%' => $contact->getDisplayName()));
      $this->get('session')->getFlashBag()->add('notice', $_message);
      throw new AccessDeniedException();
    }
  }

  /**
   * Creates a form to delete a Contact entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id) {
    return $this->createFormBuilder(array('id' => $id))
                    ->add('id', 'hidden')
                    ->getForm()
    ;
  }

  /**
   * Activate a Contact entity for a child.
   *
   * @Route("/{id}/{childID}", name="contact_activate")
   * @Method("UPDATE")
   * isGranted("ROLE_USER_ENABLED")
   */
  public function activateAction(Request $request, $id, $childID) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $contact = $em->getRepository(Contact::class)->find($id);
    $child = $em->getRepository(Child::class)->find($childID);
    if (!$contact or !$child) {
      throw $this->createNotFoundException('Unable to find Contact or Child entity.');
    }

    if ($user->hasChild($child) or $user->isAdmin()) {
      $response = new JsonResponse();
      $responseArray = array();


      $child->addContact($contact);
      $em->flush();
      $responseArray['error'] = 0;

      $_message = $this->get('translator')
              ->trans('cantine.contact.flash.contactActivated', array('%contactName%' => $contact->getDisplayName()));
      $this->get('session')->getFlashBag()->add('notice', $_message);


      $response->setData($responseArray);
      return $response;
    } else {
      throw new AccessDeniedException();
    }
  }

  /**
   * Deactivate a Contact entity for a child.
   *
   * @Route("/{id}/{childID}", name="contact_deactivate")
   * @Method("DELETE")
   * isGranted("ROLE_USER_ENABLED")
   */ public function deactivateAction(Request $request, $id, $childID) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $contact = $em->getRepository(Contact::class)->find($id);
    $child = $em->getRepository(Child::class)->find($childID);
    if (!$contact or !$child) {
      throw $this->createNotFoundException('Unable to find Contact or Child entity.');
    }

    if ($user->hasChild($child) or $user->isAdmin()) {
      $response = new JsonResponse();
      $responseArray = array();


      $child->removeContact($contact);
      $em->flush();
      $responseArray['error'] = 0;

      $_message = $this->get('translator')
              ->trans('cantine.contact.flash.contactDeactivated', array('%contactName%' => $contact->getDisplayName()));
      $this->get('session')->getFlashBag()->add('notice', $_message);


      $response->setData($responseArray);
      return $response;
    } else {
      throw new AccessDeniedException();
    }
  }

}

