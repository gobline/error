<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Error\Provider\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Mendo\Error\ErrorCatcher;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class ErrorCatcherServiceProvider implements ServiceProviderInterface
{
    private $reference;

    public function __construct($reference = 'error.catcher')
    {
        $this->reference = $reference;
    }

    public function register(Container $container)
    {
        $container[$this->reference.'.logger'] = 'logger';
        $container[$this->reference.'.logLevel.default'] = 'error';
        $container[$this->reference.'.logLevel'] = [
            500 => 'error',
            404 => 'info',
            403 => 'info',
            401 => 'info',
        ];

        $container[$this->reference] = function ($c) {
            $errorCatcher = new ErrorCatcher();
            $errorCatcher->setDefaultLogLevel($c[$this->reference.'.logLevel.default']);

            foreach ($c[$this->reference.'.logLevel'] as $code => $level) {
                $errorCatcher->setLogLevel($code, $level);
            }

            if (!empty($c[$c[$this->reference.'.logger']])) {
                $errorCatcher->setLogger($c[$c[$this->reference.'.logger']]);
            }

            return $errorCatcher;
        };
    }
}
