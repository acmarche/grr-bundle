<?php

namespace Grr\GrrBundle\EventSubscriber\Form;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;
use Grr\Core\Factory\DurationFactory;
use Grr\Core\Model\DurationModel;
use Grr\GrrBundle\Entity\Entry;
use Grr\GrrBundle\Entry\Form\Type\DurationTimeTypeField;
use Grr\GrrBundle\Entry\Validator\Duration as DurationConstraint;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddDurationFieldSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly DurationFactory $durationFactory
    ) {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'OnPreSetData',
            FormEvents::SUBMIT => 'OnSubmit',
        ];
    }

    /**
     * Verifie si nouveau objet
     * Remplis les champs jours, heures, minutes
     * donne le bon label au submit.
     */
    public function OnPreSetData(FormEvent $formEvent): void
    {
        /**
         * @var Entry
         */
        $entry = $formEvent->getData();
        $form = $formEvent->getForm();
        $room = $entry->getRoom();
        $type = null !== $room ? $room->getTypeAffichageReser() : 0;

        if (0 === $type) {
            $durationModel = $this->durationFactory->createByEntry($entry);
            $form->add(
                'duration',
                DurationTimeTypeField::class,
                [
                    'label' => false,
                    'data' => $durationModel,
                    'constraints' => [
                        new DurationConstraint(),
                    ],
                ]
            );
        }

        if (1 === $type) {
            $form->add(
                'endTime',
                DateTimeType::class,
                [
                    'label' => 'entry.form.endTime.label',
                    'help' => 'entry.form.endTime.help',
                ]
            );
        }
    }

    /**
     * Modifie la date de fin de réservation suivant les données de la Duration.
     *
     * @throws Exception
     */
    public function OnSubmit(FormEvent $formEvent): void
    {
        $form = $formEvent->getForm();

        /**
         * @var DurationModel
         */
        $duration = $form->getData()->getDuration();

        if ($duration) {
            /**
             * @var Entry
             */
            $entry = $formEvent->getData();

            $endTime = Carbon::instance($entry->getStartTime());

            $unit = $duration->getUnit();
            $time = $duration->getTime();

            switch ($unit) {
                case DurationModel::UNIT_TIME_WEEKS:
                    $endTime->addWeeks($time);
                    break;
                case DurationModel::UNIT_TIME_DAYS:
                    $endTime->addDays($time);
                    break;
                case DurationModel::UNIT_TIME_HOURS:
                    $minutes = $time * CarbonInterface::MINUTES_PER_HOUR;
                    $endTime->addMinutes($minutes);
                    break;
                case DurationModel::UNIT_TIME_MINUTES:
                    $endTime->addMinutes($time);
                    break;
                default:
                    throw new Exception('Unexpected value');
            }

            $entry->setEndTime($endTime);
        }
    }
}
