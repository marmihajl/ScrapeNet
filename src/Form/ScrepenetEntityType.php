<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/29/18
 * Time: 7:39 PM
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScrepenetEntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', TextType::class)->add('save', SubmitType::class)->add('redirect', SubmitType::class);
    }
}
