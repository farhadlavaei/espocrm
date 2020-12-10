<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2020 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: https://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

namespace Espo\Core\Select\Factory;

use Espo\Core\{
    Exceptions\Error,
    Select\Where\Converter,
    Select\Where\ItemConverter,
    Select\Where\DateTimeItemConverter,
};

use Espo\{
    Entities\User,
};

class WhereConverterFactory
{
    protected $injectableFactory;
    protected $metadata;

    public function __construct(InjectableFactory $injectableFactory, Metadata $metadata)
    {
        $this->injectableFactory = $injectableFactory;
        $this->metadata = $metadata;
    }

    public function create(string $entityType, User $user) : Converter
    {
        $itemConverterClassName = $this->getItemConverterClassName($entityType, $name);

        $itemConverter = $this->injectableFactory->createWith($itemConverterClassName, [
            'entityType' => $entityType,
            'user' => $user,
        ]);

        $dateTimeItemConverterClassName = $this->getDateTimeItemConverterClassName($entityType, $name);

        $dateTimeItemConverter = $this->injectableFactory->createWith($dateTimeItemConverterClassName, [
            'entityType' => $entityType,
            'user' => $user,
        ]);

        return $this->injectableFactory->createWith(Converter::class, [
            'entityType' => $entityType,
            'user' => $user,
            'itemConverter' => $itemConverter,
            'dateTimeItemConverter' => $dateTimeItemConverter,
        ]);
    }

    protected function getItemConverterClassName(string $entityType) : string
    {
        $className = $this->metadata->get(['selectDefs', $entityType, 'whereItemConverterClassName']);

        if ($className) {
            return $className;
        }

        return ItemConverter::class;
    }

    protected function getDateTimeItemConverterClassName(string $entityType) : string
    {
        $className = $this->metadata->get(['selectDefs', $entityType, 'whereDateTimeItemConverterClassName']);

        if ($className) {
            return $className;
        }

        return DateTimeItemConverter::class;
    }
}
