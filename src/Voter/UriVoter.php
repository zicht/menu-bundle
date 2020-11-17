<?php
/**
 * @author Rik van der Kemp <rik@zicht.nl>
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class UriVoter.
 *
 * Simple UriVoter, checks the master request for the current PathInfo and matches against current item.
 */
class UriVoter implements VoterInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * UriVoter constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public function matchItem(ItemInterface $item): ?bool
    {
        return ($this->requestStack->getMasterRequest()->getPathInfo() === $item->getUri() ? true : null);
    }
}
