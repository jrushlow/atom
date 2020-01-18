<?php

/**
 * Copyright 2020 Jesse Rushlow - Geeshoe Development
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace Geeshoe\Atom\Contract;

/**
 * Interface GeneratorInterface
 *
 * @package Geeshoe\Atom\Contract
 * @author  Jesse Rushlow <jr@geeshoe.com>
 */
interface GeneratorInterface
{
    /**
     * Create Atom 1.0 Feed element
     *
     * @param FeedRequiredInterface $feed
     */
    public function initialize(FeedRequiredInterface $feed): void;

    /**
     * Add Atom 1.0 Entry element to the Feed element
     *
     * @param EntryRequiredInterface $entry
     */
    public function addEntry(EntryRequiredInterface $entry): void;

    /**
     * Get the Atom 1.0 document
     *
     * @return string
     */
    public function generate(): string;
}
