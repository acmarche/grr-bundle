<?php

namespace Grr\GrrBundle\Form\Search;

use Grr\GrrBundle\Entity\EntryType;
use Grr\GrrBundle\EventSubscriber\AddRoomFieldSubscriber;
use Grr\GrrBundle\Form\Type\AreaSelectType;
use Grr\GrrBundle\Repository\RoomRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchEntryType extends AbstractType
{
    /**
     * @var RoomRepository
     */
    private $roomRepository;

    public function __construct(RoomRepository $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                SearchType::class,
                [
                    'required' => false,
                    'label' => false,
                    'attr' => ['placeholder' => 'search.form.word.placeholder', 'class' => 'my-1 mr-sm-2'],
                ]
            )
            ->add(
                'entry_type',
                EntityType::class,
                [
                    'class' => EntryType::class,
                    'required' => false,
                    'label' => false,
                    'help' => null,
                    'placeholder' => 'typeEntry.index.title',
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'area',
                AreaSelectType::class,
                [
                    'required' => false,
                    'label' => false,
                    'placeholder' => 'area.form.select.placeholder',
                ]
            )
            ->addEventSubscriber(new AddRoomFieldSubscriber());
    }
}
