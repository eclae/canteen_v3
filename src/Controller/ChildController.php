<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Child;
use App\Form\Type\ChildType;
use App\Form\Type\Child4ParentType;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Child controller.
 *
 */
class ChildController extends AbstractController {

  /**
   * Lists all Child entities.
   *
   * @Route("/child/index/{id}", defaults={"id"="0"}, requirements={"id"="\d+"}, name="admin_child")
   * @Method("GET")
   * @Template()
   * @IsGranted("ROLE_PARENT")
   */
  public function indexAction($id) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $child = $em->getRepository(Child::class)->find($id);

    if ($this->isGranted('ROLE_ADMIN')) {
      $children = $em->getRepository(Child::class)->findBy(array(), array('lastName' => 'ASC'));
    } else {
      $children = $user->getChildren();
    }
    if ($child == NULL) {
      $child = $children[0];
    }

    return array(
        'entities' => $children,
        'showEntity' => $child
    );
  }

    /**
   * Change the classroom of children.
   *
   * @Route("/admin/children/grade/show/{page}", requirements={"page"="\d"}, defaults={"page"="1"}, name="admin_children_grade_show")
   * @Method("GET")
   * @Template()
   */
  public function showChildrenGradeAction($page) {

    $em = $this->getDoctrine()->getManager();
    $children = $em->getRepository(Child::class)->findBy(array(), array('lastName' => 'ASC'));;
    $grades = $em->getRepository(Grade::class)->findBy(array(), array('id' => 'ASC'));   
    $highest_grade = end($grades);

    $forms_children = array();
    $forms_childgrades = array();
    foreach ($children as $child) {
        $child_id = $child->getId();
        $forms_children[$child_id] = $this->createShowDeleteForm($child_id, $page)->createView();
        if ($child->getGrade()->getID() < $highest_grade->getID()) {
            $child_grade_id = $child->getGrade()->getID() + 1; 
            $forms_childgrades[$child_id] = $this->createChangeChildGradeForm($child_id, $child_grade_id, $page)->createView();
        }
    }
    
    return array(
        'children' => $children,
        'grades' => $grades,
        'forms_children' => $forms_children,
        'forms_childgrades' => $forms_childgrades,
        'page' => $page,
        'highest_grade' => $highest_grade,
    );
  }
  
  /**
   * Creates a new Child entity.
   *
   * @Route("/admin/child", name="admin_child_create")
   * @Method("POST")
   * @Template("Child::class:new.html.twig")
   */
  public function createAction(Request $request) {
    $entity = new Child();
    $form = $this->createForm(ChildType::class, $entity);
    $form->bind($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($entity);
      $em->flush();

      return $this->redirect($this->generateUrl('admin_child'));
    }

    return array(
        'entity' => $entity,
        'form' => $form->createView(),
    );
  }

  /**
   * Displays a form to create a new Child entity.
   *
   * @Route("/admin/child/new", name="admin_child_new")
   * @Method("GET")
   * @Template()
   */
  public function newAction() {
    $entity = new Child();
    $form = $this->createForm(ChildType::class, $entity);

    return array(
        'entity' => $entity,
        'form' => $form->createView(),
    );
  }

  /**
   * Finds and displays a Child entity.
   *
   * @Route("/admin/child/{id}", name="admin_child_show")
   * @Method("GET")
   * @Template()
   */
  public function showAction($id) {
    $em = $this->getDoctrine()->getManager();

    $entity = $em->getRepository(Child::class)->find($id);

    if (!$entity) {
      throw $this->createNotFoundException('Unable to find Child entity.');
    }

    $deleteForm = $this->createDeleteForm($id);

    return array(
        'entity' => $entity,
        'delete_form' => $deleteForm->createView(),
    );
  }

  /**
   * Displays a form to edit an existing Child entity.
   *
   * @Route("/child/{id}/edit/{tabId}", requirements={"id"="\d+", "tabId"="\d+"}, defaults={"tabId"="2"}, name="admin_child_edit")
   * @Method("GET")
   * @Template()
   * @IsGranted("ROLE_PARENT")
   */
  public function editAction($id, $tabId) {

    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();
    $response = array();

    $child = $em->getRepository(Child::class)->find($id);
    $response['entity'] = $child;
    if (!$child) {
      throw $this->createNotFoundException('Unable to find Child entity.');
    }

    if ($this->isGranted('ROLE_ADMIN')) {
      $editForm = $this->createForm(ChildType::class, $child);
      $deleteForm = $this->createDeleteForm($id);
      return array(
          'edit_form' => $editForm->createView(),
          'delete_form' => $deleteForm->createView(),
          'entity' => $child,
          'tabId' => $tabId,
      );
    } elseif ($user->hasChild($child)) {
      $editForm = $this->createForm(Child4ParentType::class, $child);
      return array(
          'edit_form' => $editForm->createView(),
          'entity' => $child,
          'tabId' => $tabId,
      );
    } else {
      throw new AccessDeniedException();
    }
  }

  /**
   * Edits an existing Child entity.
   *
   * @Route("/child/{id}", name="admin_child_update")
   * @Method({"PUT","GET"})
   * @Template()
   * @IsGranted("ROLE_PARENT")
   */
  public function updateAction(Request $request, $id) {
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();

    $child = $em->getRepository(Child::class)->find($id);

    if (!$child) {
      throw $this->createNotFoundException('Unable to find Child entity.');
    }


    if ($this->isGranted('ROLE_ADMIN')) {
//$deleteForm = $this->createDeleteForm($id);
      $editForm = $this->createForm(ChildType::class, $child);
    } elseif ($user->hasChild($child)) {
      $editForm = $this->createForm(Child4ParentType::class, $child);
    } else {
      throw new AccessDeniedException();
    }

    $editForm->bind($request);

    if ($editForm->isValid()) {
      $em->persist($child);
      $em->flush();
      $this->get('session')->getFlashBag()->add('notice', 'cantine.child.flash.profileUpdated');
    } else {
      $this->get('session')->getFlashBag()->add('error', 'cantine.child.flash.profileUpdateError');
    }
    return $this->redirect($this->generateUrl('admin_child_edit', array('id' => $id)));
  }

  /**
   * Deletes a Child entity.
   *
   * @Route("/admin/child/{id}", name="admin_child_delete")
   * @Method("DELETE")
   */
  public function deleteAction(Request $request, $id) {
    $form = $this->createDeleteForm($id);
    $form->bind($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository(Child::class)->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Child entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('admin_child'));
  }
  /**
   * Deletes a Child entity.
   *
   * @Route("/admin/child_show/{id}/{page}", requirements={"id"="\d+", "page"="\d+"}, name="admin_child_show_delete")
   * @Method("DELETE")
   */
  public function deleteShowAction(Request $request, $id, $page) {
    $form = $this->createShowDeleteForm($id, $page);
    $form->bind($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository(Child::class)->find($id);

      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Child entity.');
      }

      $em->remove($entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('admin_children_grade_show', array('page' => $page)));
  }
   /**
   * change a Child'grade entity.
   *
   * @Route("/admin/change/child/grade/{id}/{grade}/{page}", requirements={"id"="\d+", "grade"="\d+", "page"="\d+"}, name="admin_change_child_grade")
   * @Method("PUT")
   */
  public function updatechildGradeAction(Request $request, $id, $grade, $page) {
    $form = $this->createChangeChildGradeForm($id, $grade, $page);
    $form->bind($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $entity = $em->getRepository(Child::class)->find($id);
      $grade_entity = $em->getRepository(Grade::class)->find($grade);

      if (!$entity or !$grade_entity) {
        throw $this->createNotFoundException('Unable to find Child entity or Grade entity does not exist.');
      }

      $entity->setGrade($grade_entity);
      $em->flush();
    }

    return $this->redirect($this->generateUrl('admin_children_grade_show', array('page' => $page)));
  }
  /**
   * Creates a form to delete a Child entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Symfony\Component\Form\Form The form
   */
  private function createDeleteForm($id) {
    return $this->createFormBuilder(array('id' => $id))
                    ->add('id', HiddenType::class)
                    ->getForm()
    ;
  }
   /**
   * Creates a form to delete a Child entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Symfony\Component\Form\Form The form
   */
  private function createShowDeleteForm($id, $page) {
    return $this->createFormBuilder()
                    ->setAction($this->generateUrl('admin_child_show_delete', array('id' => $id, 'page' => $page)))
                    ->setMethod('DELETE')
                    ->add($this->get('translator')->trans('cantine.form.child.delete'), 'submit', array('attr' => array('class' => 'btn btn-danger')))
                    ->getForm()
    ;
  }
   /**
   * Creates a form to change the Child's grade entity by id.
   *
   * @param mixed $id The entity id
   *
   * @return Symfony\Component\Form\Form The form
   */
  private function createChangeChildGradeForm($id, $grade, $page) {
    return $this->createFormBuilder()
                    ->setAction($this->generateUrl('admin_change_child_grade', array('id' => $id, 'grade' => $grade, 'page' => $page)))
                    ->setMethod('PUT')
                    ->add($this->get('translator')->trans('cantine.form.child.grades.increment'), 'submit', array('attr' => array('class' => 'btn btn-warning')))
                    ->getForm()
    ;
  }
}

