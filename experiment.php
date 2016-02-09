<?php
namespace rg\modules\publicliterature\actions;

use rg\core\base\annotations\internal;
use rg\core\pow\requirements\RequestDataRequirement;
use rg\core\pow\requirements\Requirement;
use rg\core\pow\requirements\RequirementCollection;
use rg\core\pow\requirements\TrafficExperimentRequirement;
use rg\model\experiments\experiment\selectors\TrafficExperimentIdSelector;
use rg\model\experiments\experiment\TrafficExperimentData;
use rg\model\experiments\experiment\TrafficExperimentService;
use rg\model\literature\entities\ExtendedLink;
use rg\model\literature\entities\Publication;
use rg\model\literature\publication\PublicationModel;
use rg\modules\literature\actions\AbstractPublication;

/**
 * PublicationAbstract.
 *
 * @copyright ResearchGate GmbH
 */
class PublicationAbstract extends AbstractPublication {

    /**
     * @inject
     * @var \rg\modules\literature\classes\PublicationViewProvider
     */
    public $publicationViewProvider;

    /**
     * @inject
     * @var \rg\core\Session
     */
    public $session;

    /**
     * @var ExtendedLink
     */
    public $preferredLink;

    /**
     * @var bool
     */
    public $showFollowPublicationButton = false;

    /**
     * @var bool
     */
    public $alwaysShowAbstractLoggedOut = false;

    /**
     * @throws \rg\injektor\InjectionException
     * @throws \Exception
     * @return \rg\core\pow\requirements\RequirementCollection
     * @internal
     */
    public function collectAdditionalData() {
        if (!$this->publicationViewProvider->getAbstract($this->publication) && !($this->session->isScientistLoggedIn() && $this->isEditingAllowed())) {
            return $this->deactivateWidget();
        }
        $requirements = [
            serviceModelRequirement(
                $this->properties->preferredLink,
                ['publication' => $this->publication],
                PublicationModel::getCall()->getPreferredLink(),
                Requirement::MODE_OPTIONAL
            ),
            new RequestDataRequirement(
                $this->properties->showFollowPublicationButton, null, RequestDataRequirement::MODE_OPTIONAL
            ),
            TrafficExperimentRequirement::builder()
                    ->name(TrafficExperimentData::PUBLICATION_DETAIL_PAGE_WITH_FULLTEXT_SHOW_FULL_ABSTRACT)
                    ->page(Publication::class, $this->publicationUid, TrafficExperimentService::EXT_PUBLICATION_FULLTEXT)
                    ->selector(new TrafficExperimentIdSelector())
                    ->controlVariant(function () {
                    })
                    ->addVariant(TrafficExperimentData::PUBLICATION_DETAIL_PAGE_WITH_FULLTEXT_SHOW_FULL_ABSTRACT_ENABLED, function () {
                        $this->alwaysShowAbstractLoggedOut = true;
                    })
                    ->build()

        ];
        return new RequirementCollection($requirements);


    }

    /**
     * @throws \rg\injektor\InjectionException
     * @throws \Exception
     * @return array
     * @internal
     */
    public function getData() {
        return [
            'publicationUid' => $this->publication->getPublicationUid(),
            'abstract' => $this->publicationViewProvider->getAbstract($this->publication),
            'canEdit' => $this->isEditingAllowed(),
            'isAdmin' => $this->session->isScientistLoggedIn() && $this->isAdminEditingAllowed($this->requestContext->getCurrentAccountId()),
            'isArtifact' => $this->publication->getPublicationType() === Publication::TYPE_ARTIFACT,
            'showFullAbstract' => !$this->session->isAccountLoggedIn() && (!$this->preferredLink || $this->alwaysShowAbstractLoggedOut),

        ];
    }

    /**
     * @return string[]
     * @internal
     */
    public function getTemplateExtensions() {
        return ['generalHelpers'];
    }
}
