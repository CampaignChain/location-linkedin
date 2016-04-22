<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CampaignChain\Location\LinkedInBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CampaignChain\CoreBundle\Util\ParserUtil;

/**
 * @ORM\Entity
 */
class User extends LocationBase
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profileUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profileImageUrl;


    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set profileUrl
     *
     * @param string $profileUrl
     * @return User
     */
    public function setProfileUrl($profileUrl)
    {
        $this->profileUrl = ParserUtil::sanitizeUrl($profileUrl);

        return $this;
    }

    /**
     * Get profileUrl
     *
     * @return string
     */
    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    /**
     * Set profileImageUrl
     *
     * @param string $profileImageUrl
     * @return User
     */
    public function setProfileImageUrl($profileImageUrl)
    {
        $this->profileImageUrl = $profileImageUrl;

        return $this;
    }

    /**
     * Get profileImageUrl
     *
     * @return string
     */
    public function getProfileImageUrl()
    {
        return $this->profileImageUrl;
    }
}