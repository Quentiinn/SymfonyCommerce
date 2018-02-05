<?php

namespace App\Controller;

use App\Entity\TypeCommercant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class TypeCommercantController extends Controller
{
    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @Route("/type-commercant", name="typecommercant.index")
     */
    public function index()
    {
        return $this->redirectToRoute('typecommercant.show');
    }


    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_USER')")
     * @Route("type-commercant/show",name="typecommercant.show")
     */
    public function showTypeCommercant(Request $request , Environment $twig , RegistryInterface $doctrine){
        $typeCommercants=$doctrine->getRepository(TypeCommercant::class)->findAll();
        return new Response($twig->render('typeCommercant/showTypeCommercant.html.twig' , ['data' => $typeCommercants]));
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/type-commercant/add",name="typecommercant.add")
     */
    public function addTypeCommercant(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        return $this->render('typeCommercant/addTypeCommercant.html.twig');
    }


    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/type-commercant/validFormAdd",name="typecommercant.validFormAdd")
     * @Method({"POST"})
     */
    public function validFormAddAction(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        if(!$this->isCsrfTokenValid('add_type_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $donnees['nom']=htmlspecialchars($request->request->get('nom'));

        $erreurs=array();
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='Le nom doit être composé de 2 lettres minimum';


        if(! empty($erreurs))
        {

            return $this->render('typecommercant/addTypeCommercant.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
        }
        else
        {
            $type = new TypeCommercant();
            $type->setNoms($donnees['nom']);
            $em=$doctrine->getManager();
            $em->persist($type);
            $em->flush();
            return $this->redirectToRoute('typecommercant.show');
        }
        return $this->redirectToRoute('typecommercant.show');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/type-commercant/delete",name="typecommercant.delete")
     * @Method({"DELETE"})
     */
    public function deleteTypeCommercant(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        //permet de verifier les tokens
        if(!$this->isCsrfTokenValid('delete_valid_type', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $id=$request->request->get('type_commercant_id');
        $type=$doctrine->getRepository(TypeCommercant::class)->find($id);
        $em=$doctrine->getManager();
        $em->remove($type);
        $em->flush();
        return $this->redirectToRoute('typecommercant.show');
    }
}
