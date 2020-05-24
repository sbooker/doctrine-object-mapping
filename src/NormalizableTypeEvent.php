<?php

declare(strict_types=1);

namespace Sbooker\DoctrineObjectMapping;

use Doctrine\Common\EventArgs;

final class NormalizableTypeEvent extends EventArgs
{
    const onPreNormalize = 'onPreNormalize';
    const onPreDenormalize = 'onPreDenormalize';

    /** @var NormalizableType */
    private $type;

    public function __construct(NormalizableType $type)
    {
        $this->type = $type;
    }

    public function getType(): NormalizableType
    {
        return $this->type;
    }
}