<?php

namespace AssetManager\Resolver;

use AssetManager\Asset\FileAsset;
use AssetManager\Asset\HttpAsset;
use AssetManager\Exception;
use AssetManager\Service\MimeResolver;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

/**
 * This resolver allows you to resolve using a 1 on 1 mapping to a file.
 */
class MapResolver implements ResolverInterface, MimeResolverAwareInterface
{
    /**
     * @var array
     */
    protected $map = [];

    /**
     * @var MimeResolver The mime resolver.
     */
    protected $mimeResolver;

    /**
     * Constructor
     *
     * Instantiate and optionally populate map.
     *
     * @param array|Traversable $map
     */
    public function __construct($map = [])
    {
        $this->setMap($map);
    }

    /**
     * Retrieve the map
     *
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set (overwrite) map
     *
     * Maps should be arrays or Traversable objects with name => path pairs
     *
     * @param array|Traversable $map
     * @throws Exception\InvalidArgumentException
     */
    public function setMap($map)
    {
        if (!is_array($map) && !$map instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array or Traversable, received "%s"',
                __METHOD__,
                (is_object($map) ? get_class($map) : gettype($map))
            ));
        }

        if ($map instanceof Traversable) {
            $map = ArrayUtils::iteratorToArray($map);
        }

        $this->map = $map;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($name)
    {
        if (!isset($this->map[$name])) {
            return null;
        }

        $file     = $this->map[$name];
        $mimeType = $this->getMimeResolver()->getMimeType($file);

        if (false === filter_var($file, FILTER_VALIDATE_URL)) {
            $asset = new FileAsset($file);
        } else {
            $asset = new HttpAsset($file);
        }

        $asset->mimetype = $mimeType;

        return $asset;
    }

    /**
     * Get the mime resolver
     *
     * @return MimeResolver
     */
    public function getMimeResolver()
    {
        return $this->mimeResolver;
    }

    /**
     * Set the mime resolver
     *
     * @param MimeResolver $resolver
     */
    public function setMimeResolver(MimeResolver $resolver)
    {
        $this->mimeResolver = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return array_keys($this->map);
    }
}
