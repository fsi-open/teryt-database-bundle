<?php

/**
 * (c) FSi sp. z o.o <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\TerytDatabaseBundle;

use FSi\Bundle\TerytDatabaseBundle\DependencyInjection\FSITerytDbExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Norbert Orzechowicz <norbert@fsi.pl>
 */
class FSiTerytDbBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new FSITerytDbExtension();
        }

        return $this->extension;
    }
}
