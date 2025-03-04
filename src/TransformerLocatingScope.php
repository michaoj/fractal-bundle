<?php declare(strict_types=1);

namespace Somnambulist\Bundles\FractalBundle;

use InvalidArgumentException;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use League\Fractal\TransformerAbstract;
use Somnambulist\Bundles\FractalBundle\DependencyInjection\SomnambulistFractalExtension;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function is_callable;

/**
 * Class TransformerLocatingScope
 *
 * @package    Somnambulist\Bundles\FractalBundle
 * @subpackage Somnambulist\Bundles\FractalBundle\TransformerLocatingScope
 */
class TransformerLocatingScope extends Scope
{
    private ServiceLocator $transformers;

    public function __construct(ServiceLocator $transformers, Manager $manager, ResourceInterface $resource, $scopeIdentifier = null)
    {
        parent::__construct($manager, $resource, $scopeIdentifier);

        $this->transformers = $transformers;
    }

    /**
     * @param callable|TransformerAbstract $transformer
     * @param mixed                        $data
     *
     * @return array
     * @internal
     */
    protected function fireTransformer($transformer, $data)
    {
        if (is_string($transformer) && $this->transformers->has($transformer)) {
            $transformer = $this->transformers->get($transformer);
        }
        if (!is_callable($transformer) && !$transformer instanceof TransformerAbstract) {
            throw new InvalidArgumentException(
                sprintf(
                    'Transformer "%s" is not a callable or instance of %s; did you optionally tag as a service using "%s"',
                    $transformer,
                    TransformerAbstract::class,
                    SomnambulistFractalExtension::TRANSFORMER_TAG_NAME,
                )
            );
        }

        return parent::fireTransformer($transformer, $data);
    }
}
