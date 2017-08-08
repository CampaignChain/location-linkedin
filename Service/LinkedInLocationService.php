<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace CampaignChain\Location\LinkedInBundle\Service;



use CampaignChain\Channel\LinkedInBundle\REST\LinkedInClient;
use CampaignChain\CoreBundle\Entity\Location;
use CampaignChain\CoreBundle\EntityService\LocationService;
use CampaignChain\CoreBundle\Wizard\ChannelWizard;
use CampaignChain\Location\LinkedInBundle\Entity\Page;
use CampaignChain\Location\LinkedInBundle\Entity\User;
use CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;

/**
 * Handel LinkedIn Location related processing
 *
 * Class LinkedInLocationService
 * @package CampaignChain\Location\LinkedInBundle\Service
 */
class LinkedInLocationService
{
    /**
     * @var ChannelWizard
     */
    private $channelWizard;

    /**
     * @var LocationService
     */
    private $locationService;

    /**
     * @var LinkedInClient
     */
    private $client;

    /**
     * @var AssetsHelper
     */
    private $assetsHelper;

    public function __construct(ChannelWizard $channelWizard, LocationService $locationService, LinkedInClient $client, AssetsHelper $assetsHelper)
    {
        $this->channelWizard = $channelWizard;
        $this->locationService = $locationService;
        $this->client = $client;
        $this->assetsHelper = $assetsHelper;
    }

    /**
     * @return Location[]
     */
    public function getParsedLocationsFromLinkedIn()
    {
        $channel = $this->channelWizard->getChannel();
        $profile = $this->channelWizard->get('profile');

        $locations = [];

        $locationName = $profile->displayName;

        // Get the location module for the user stream.
        $locationModuleUser = $this->locationService
            ->getLocationModule('campaignchain/location-linkedin', 'campaignchain-linkedin-user');

        // Create the location instance for the user stream.
        $locationUser = new Location();
        $locationUser->setIdentifier($profile->identifier);
        $locationUser->setName($locationName);
        $locationUser->setLocationModule($locationModuleUser);
        if(!$profile->photoURL || strlen($profile->photoURL) == 0){
            $locationUser->setImage($this->assetsHelper
                ->getUrl('/bundles/campaignchainchannellinkedin/ghost_person.png'));
        } else {
            $locationUser->setImage($profile->photoURL);
        }
        $locationUser->setChannel($channel);
        $locationModuleUser->addLocation($locationUser);
        $locations[$profile->identifier] = $locationUser;

        $tokens = $this->channelWizard->get('tokens');
        /** @var Token $userToken */
        $userToken = array_values($tokens)[0];

        $connection = $this->client->getConnectionByToken($userToken);
        $companies = $connection->getCompanies();

        //there is only a user page
        if (empty($companies)) {
            return $locations;
        }

        // Get the location module for the page stream.
        $locationModulePage = $this->locationService
            ->getLocationModule('campaignchain/location-linkedin', 'campaignchain-linkedin-page');

        $wizardPages = [];
        foreach ($companies as $company) {

            $newToken = new Token();
            $newToken->setAccessToken($userToken->getAccessToken());
            $newToken->setApplication($userToken->getApplication());
            $newToken->setTokenSecret($userToken->getTokenSecret());
            $tokens[$company['id']] = $newToken;
            $this->channelWizard->set('tokens', $tokens);

            $companyData = $connection->getCompanyProfile($company['id']);

            $locationPage = new Location();
            $locationPage->setChannel($channel);
            $locationPage->setName($companyData['name']);
            $locationPage->setIdentifier($companyData['id']);
            if (isset($companyData['squareLogoUrl'])) {
                $locationPage->setImage($companyData['squareLogoUrl']);
            } else {
                $locationPage->setImage($this->assetsHelper
                    ->getUrl('/bundles/campaignchainchannellinkedin/ghost_person.png'));
            }
            $locationPage->setLocationModule($locationModulePage);
            $locationModulePage->addLocation($locationPage);

            $locations[$companyData['id']] = $locationPage;

            $wizardPages[$companyData['id']] = $companyData;
        }
        $this->channelWizard->set('pagesData', $wizardPages);

        return $locations;
    }

    public function handleUserPageCreation(Location $location)
    {
        // The display name of the Facebook user will be the name of the CampaignChain channel.
        $this->channelWizard->setName($location->getName());
        // Get the OAuth profile data from the Wizard.
        $profile = $this->channelWizard->get('profile');
        // Define the URL of the location
        $location->setUrl($profile->profileURL);

        $user = new User();
        $user->setLocation($location);
        $user->setIdentifier($profile->identifier);
        $user->setDisplayName($profile->displayName);
        $user->setProfileImageUrl($location->getImage());
        $user->setProfileUrl($profile->profileURL);

        // Remember the user object in the Wizard.
        $this->channelWizard->set($user->getIdentifier(), $user);

        $flashBagMsg = $this->channelWizard->get('flashBagMsg');
        $flashBagMsg .= '<li>User stream: <a href="'.$profile->profileURL.'">'.$profile->displayName.'</a></li>';
        $this->channelWizard->set('flashBagMsg', $flashBagMsg);
    }

    public function handleCompanyPageCreation(Location $location)
    {
        $this->channelWizard->setName($location->getName());

        $pagesData = $this->channelWizard->get('pagesData');
        $pageData = $pagesData[$location->getIdentifier()];

        $companyLink = 'https://www.linkedin.com/company/'.$pageData['id'];
        $location->setUrl($companyLink);

        $page = new Page();
        $page->setLocation($location);
        $page->setIdentifier($pageData['id']);
        $page->setDisplayName($pageData['name']);
        $page->setDescription($pageData['description']);
        $page->setLink($companyLink);
        $page->setWebsiteLink($pageData['websiteUrl']);

        // Remember the user object in the Wizard.
        $this->channelWizard->set($page->getIdentifier(), $page);

        $flashBagMsg = $this->channelWizard->get('flashBagMsg');
        $flashBagMsg .= '<li>Page: <a href="'.$page->getLink().'">'.$page->getDisplayName().'</a></li>';
        $this->channelWizard->set('flashBagMsg', $flashBagMsg);
    }
}