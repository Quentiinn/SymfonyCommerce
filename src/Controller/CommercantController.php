<?php

namespace App\Controller;

use App\Entity\Commercant;
use App\Entity\TypeCommercant;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class CommercantController extends Controller
{
    /**
     * @Route("/commercant", name="commercant.index")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->redirectToRoute('commercant.show');
    }


    /**
     * @Route("commercant/show",name="commercant.show")
     * @Method({"GET"})
     */
    public function showCommercant(Request $request , Environment $twig , RegistryInterface $doctrine){

//        $detailsProduits=$doctrine->getRepository(Produit::class)->getProduits();

        $commercants=$doctrine->getRepository(Commercant::class)->findAll();
        /*$produit=$doctrine->getRepository(Produit::class)->find(1);
        $typeProduit=$doctrine->getRepository(TypeProduit::class)->find(1);
        $produit->setTypeProduitId($typeProduit);
        $doctrine->getEntityManager()->persist($produit);
        $doctrine->getEntityManager()->flush();    // commit des opérations
        */

        return new Response($twig->render('commercant/showCommercant.html.twig' , ['data' => $commercants]));
    }

    /**
     * @Route("/commercant/add",name="commercant.add")
     * @Method({"GET"})
     */
    public function addCommercant(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $typeCommercant=$doctrine->getRepository(TypeCommercant::class)->findAll();

        return $this->render('commercant/addCommercant.html.twig', ['typeCommercants'=> $typeCommercant]);
    }


    /**
     * @Route("/commercant/validFormAdd",name="commercant.validFormAdd")
     * @Method({"POST"})
     */
    public function validFormAddAction(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        $donnees['nom']=htmlspecialchars($request->request->get('nom'));
        $donnees['prix']=htmlspecialchars($request->request->get('prix'));
        $donnees['date']=htmlspecialchars($request->request->get('date'));
        $donnees['idTypeCommercant']=htmlspecialchars($request->request->get('idTypeCommercant'));

        $erreurs=array();
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='Le nom doit être composé de 2 lettres minimum';
        if(! is_numeric($donnees['idTypeCommercant']))$erreurs['idTypeCommercant']='Veuillez saisir un type de commercant';
        if(! is_numeric($donnees['prix']))$erreurs['prix']='Veuillez saisir une valeur numérique';
        if($donnees['date'] == "")$erreurs['date']='Veuillez saisir une date';


        if(! empty($erreurs))
        {
            $typeCommercants=$doctrine->getRepository(TypeCommercant::class)->findIdAndLibelle();
            return $this->render('commercant/addCommercant.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'typeCommercants'=> $typeCommercants]);
        }
        else
        {
            $date = \DateTime::createFromFormat('d/m/Y', $donnees['date']);
            $idTypeCommercant=$doctrine->getRepository(TypeCommercant::class)->find($donnees['idTypeCommercant']);
            $commerce = new Commercant();
            $commerce->setNoms($donnees['nom']);
            $commerce->setPrixLocation($donnees['prix']);
            $commerce->setDateInstallation($date);
            $commerce->setIdTypeCommercant($idTypeCommercant);
            $em=$doctrine->getManager();
            $em->persist($commerce);
            $em->flush();
            return $this->redirectToRoute('commercant.show');
        }
        return $this->redirectToRoute('commercant.show');
    }

    /**
     * @Route("/commercant/delete",name="commercant.delete")
     * @Method({"DELETE"})
     */
    public function deleteCommercant(Request $request, Environment $twig, RegistryInterface $doctrine)
    {
        //permet de verifier les tokens
        if(!$this->isCsrfTokenValid('delete_valid', $request->get('token'))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
        $id=$request->request->get('commercant_id');
        $commercant=$doctrine->getRepository(Commercant::class)->find($id);
        $em=$doctrine->getManager();
        $em->remove($commercant);
        $em->flush();
        return $this->redirectToRoute('commercant.show');
    }

    /**
     * @Route("/commercant/edit/{id}",name="commercant.edit", requirements={"id" = "\d+"})
     * @Method({"GET"})
     */
    public function editProduit(Request $request, Environment $twig, RegistryInterface $doctrine,$id)
    {
        // A modifier
        $conn = $this->get('database_connection');
        $statement = $conn->executeQuery('SELECT id,id_type_commercant,noms,prix_location,date_installation
    FROM commercants WHERE id= ? LIMIT 1;',[$id]);
        $produit = $statement->fetch();
        $typeCommercants = $conn->fetchAll('SELECT id,noms
    FROM type_commercants ORDER BY id;');
        // fin A modifier


        $produits=$doctrine->getRepository(Commercant::class)->find($id);
        dump($produits);


        return $this->render('commercant/editCommercant.html.twig', ['donnees' => $produits,'typeCommercants'=> $typeCommercants]);
    }

    /**
     * @Route("/commercant/validFormEdit",name="commercant.validFormEdit")
     * @Method({"POST"})
     */
    public function validFormEditCommercant(Request $request)
    {
        $donnees['id']=$request->request->get('id');
        $donnees['noms']=htmlspecialchars($request->request->get('nom'));
        $donnees['prixLocation']=htmlspecialchars($request->request->get('prix'));
        $donnees['dateInstallation']=htmlspecialchars($request->request->get('date'));
        $donnees['idTypeCommercant']=htmlspecialchars($request->request->get('idTypeCommercant'));

        $erreurs=array();
        if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['noms']))) $erreurs['nom']='Le nom doit être composé de 2 lettres minimum';
        if(! is_numeric($donnees['idTypeCommercant']))$erreurs['idTypeCommercant']='Veuillez saisir un type de commercant';
        if(! is_numeric($donnees['prixLocation']))$erreurs['prix']='Veuillez saisir une valeur numérique';
        if($donnees['dateInstallation'] == "")$erreurs['date']='Veuillez saisir une date';

        if(! empty($erreurs))
        {
            // A modifier
            $conn = $this->get('database_connection');
            $typeCommercants = $conn->fetchAll('SELECT id,noms  FROM type_commercants ORDER BY id;');
            // fin A modifier
            return $this->render('commercant/editCommercant.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs,'typeCommercants'=> $typeCommercants]);
        }
        else
        {

            $em = $this->getDoctrine()->getManager();
            $commercant = $em->getRepository(Commercant::class)->find($donnees['id']);

            if (!$commercant) {
                throw $this->createNotFoundException(
                    'No product found for id '.$donnees['id']
                );
            }

            $date = DateTime::createFromFormat("j/m/Y" , $donnees['date']);
            $commercant->setNoms($donnees['nom']);
            $commercant->setDateInstallation($date);
            $commercant->setPrixLocation($donnees['prix']);
            $em->flush();

            return $this->redirectToRoute('commercant.show');
        }

        return $this->redirectToRoute('commercant.show');
    }
}
