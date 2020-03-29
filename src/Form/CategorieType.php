<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
	//constructeur de formulaires : capable de générer tout type de formulaire à partir de champs HTML connus
    {
        $builder
            ->add('libelle') 
			//équivaut à ->add('libelle',TextType::class) car TextType est le type de champs sémantique (différent des types de champs HTML) par défaut pour un attribut <string>
			//exemple de fonctionnement d'un type de champs sémantique : DateType peut afficher trois champs HTML select successifs pour sélectionner le jour, le mois et l'année
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
		//le constructeur de formulaires détermine à partir de la classe, ici la classe Categorie, le type de champ sémantique qu'il doit définir
    }
}
