<?php
/**
 * Created by PhpStorm.
 * User: ypoon
 * Date: 23/7/2018
 * Time: 1:01 PM
 */

namespace App\FormType\Base;

use App\Entity\Base\Directory\DirectoryGroup;
use App\Entity\Base\Directory\DirectoryMember;
use App\FormType\Component\CompositeCollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectoryGroupsForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add("name", TextType::class)
			->add("shortStr", TextType::class)
            ->add("children", CompositeCollectionType::class, [
                "entry_type" => EntityType::class,
                "entry_options" => [
                    "class" => DirectoryMember::class,
                    "choice_label" => "fullName"
                ]])
			->add("submit", SubmitType::class);
	}

	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults([
			"data_class" => DirectoryGroup::class
		]);
	}

}