<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="comment_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
       $postId = $request->get('post_id');
       $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($postId);
       $comments = $post->getComments();
       $comment = new Comment();
       $form = $this->createForm('AppBundle\Form\CommentType', $comment);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           $this->newAction($comment, $post);
       }
       return $this->render('comment/index.html.twig', array(
           'comments' => $comments,
           'post_id' => $postId,
           'form' => $form->createView(),
       ));

       return json_encode(array(
            'comments' => $comments,
            'post_id' => $postId,
        )); //for json
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/new", name="comment_new")
     * @Method({"GET", "POST"})
     */
     public function newAction($comment,  $post)
    {
        $em = $this->getDoctrine()->getManager();
        $comment->setPost($post);
        $em->persist($comment);
        $em->flush();


        return $this->redirectToRoute('comment_index', array('post_id' => $post->getId()));
        return json_encode($post->getId()); //for json
    }



    /**
     * Finds and displays a Comment entity.
     *
     * @Route("/{id}", name="comment_show")
     * @Method("GET")
     */
    public function showAction(Comment $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);

        return $this->render('comment/show.html.twig', array(
            'comment' => $comment,
            'delete_form' => $deleteForm->createView(),
        ));
        return json_encode(array(
            'comment' => $comment,
            'post_id' => $postId
        )); //for json
    }

    /**
     * Displays a form to edit an existing Comment entity.
     *
     * @Route("/{id}/edit", name="comment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Comment $comment)
    {
        $deleteForm = $this->createDeleteForm($comment);
        $editForm = $this->createForm('AppBundle\Form\CommentType', $comment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comment_edit', array('id' => $comment->getId()));
            return json_encode($comment->getId()); //for json
        }

        return $this->render('comment/edit.html.twig', array(
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
         return json_encode($comment); //for json
    }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/{id}", name="comment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Comment $comment)
    {
        $form = $this->createDeleteForm($comment);
        $form->handleRequest($request);
        $postId = $comment->getPost()->getId();
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('comment_index', array('post_id'=>$postId));

    }

    /**
     * Creates a form to delete a Comment entity.
     *
     * @param Comment $comment The Comment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comment $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
        return json_encode($comment->getId()); //for json
    }
}
