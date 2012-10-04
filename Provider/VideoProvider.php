<?php

/*
 * This file and its content is copyright of Beeldspraak Website Creators BV - (c) Beeldspraak 2012. All rights reserved.
 * Any redistribution or reproduction of part or all of the contents in any form is prohibited.
 * You may not, except with our express written permission, distribute or commercially exploit the content.
 *
 * @author     Roel Sint <roel@beeldspraak.com>
 * @copyright  Copyright 2012, Beeldspraak Website Creators BV
 * @link       http://beeldspraak.com
 */

namespace Sonata\MediaBundle\Provider;

use Sonata\MediaBundle\Provider\FileProvider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Entity\BaseMedia as Media;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\Form;
use Gaufrette\Filesystem;

/**
 * Video
 *
 * @author Roel Sint <roel@beeldspraak.com>
 */
class VideoProvider extends FileProvider
{
    protected $allowedExtensions;

    protected $allowedMimeTypes;

    /**
     * @param string                                           $name
     * @param \Gaufrette\Filesystem                            $filesystem
     * @param \Sonata\MediaBundle\CDN\CDNInterface             $cdn
     * @param \Sonata\MediaBundle\Generator\GeneratorInterface $pathGenerator
     * @param \Sonata\MediaBundle\Thumbnail\ThumbnailInterface $thumbnail
     * @param array                                            $allowedExtensions
     * @param array                                            $allowedMimeTypes
     */
    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array())
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail);

        $this->allowedExtensions = $allowedExtensions;
        $this->allowedMimeTypes  = $allowedMimeTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperProperties(MediaInterface $media, $format, $options = array())
    {   
        if ($format == 'reference') {
            $params = array(
                'width' => false
            );
        } else {
            $settings = $this->getFormat($format);
            $params = array (
                'width' => $settings['width']
            );
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($format == 'reference') {
            $path = $this->getReferenceImage($media);
        } else {
            $path = sprintf('media_bundle/images/files/%s/video.png', $format);
        }

        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     *
     * @return string
     */
    protected function generateReferenceName(MediaInterface $media)
    {
        $extension = $media->getBinaryContent()->guessExtension() ? $media->getBinaryContent()->guessExtension() : pathinfo($media->getBinaryContent()->getClientOriginalName(), PATHINFO_EXTENSION);

        return sha1($media->getName() . rand(11111, 99999)) . '.' . $extension;
    }

}
