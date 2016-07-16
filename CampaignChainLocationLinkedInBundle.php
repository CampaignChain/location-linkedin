<?php

namespace CampaignChain\Location\LinkedInBundle;

use CampaignChain\Location\LinkedInBundle\DependencyInjection\CampaignChainLocationLinkedInExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CampaignChainLocationLinkedInBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new CampaignChainLocationLinkedInExtension();
    }
}
