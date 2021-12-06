<?php

/*
 * Copyright 2020 Jesse Rushlow - Rushlow Development.
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

namespace RushlowDevelopment\Atom\UnitTests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RushlowDevelopment\Atom\AtomBuilder;
use RushlowDevelopment\Atom\Contract\EntryInterface;
use RushlowDevelopment\Atom\Contract\FeedInterface;
use RushlowDevelopment\Atom\Contract\GeneratorInterface;
use RushlowDevelopment\Atom\Model\Atom;
use RushlowDevelopment\Atom\Model\Entry;
use RushlowDevelopment\Atom\Model\Feed;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 *
 * @internal
 */
class AtomBuilderTest extends TestCase
{
    public ?MockObject $mockGenerator;
    public ?MockObject $mockAtom;
    public ?MockObject $mockFeedEntry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockGenerator = $this->createMock(GeneratorInterface::class);
        $this->mockAtom = $this->createMock(Atom::class);
        $this->mockFeedEntry = $this->createMock(FeedInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->mockGenerator = null;
        $this->mockAtom = null;
        $this->mockFeedEntry = null;
    }

    public function testConstructorCreatesAtomInstanceIfOneNotProvided(): void
    {
        $builder = new AtomBuilder();

        $this->assertInstanceOf(Atom::class, $builder->getAtom());
    }

    public function testConstructorSetAtomInstanceProvidedAsParam(): void
    {
        $mockAtom = $this->createMock(Atom::class);

        $builder = new AtomBuilder($mockAtom);

        $this->assertSame($mockAtom, $builder->getAtom());
    }

    public function testCreateFeedCallsAtomSetFeedElementWithProvidedElementParams(): void
    {
        $mockAtom = $this->createMock(Atom::class);
        $mockAtom->expects($this->once())
            ->method('setFeedElement')
            ->with(self::isInstanceOf(Feed::class))
        ;

        $mockTime = $this->createMock(\DateTime::class);

        $builder = new AtomBuilder($mockAtom);
        $builder->createFeed('testId', 'testTitle', $mockTime);
    }

    public function testAddEntryCallsAtomAddEntryElementWithEntryModel(): void
    {
        $mockAtom = $this->createMock(Atom::class);
        $mockAtom->expects($this->once())
            ->method('addEntryElement')
            ->with(self::isInstanceOf(Entry::class))
        ;

        $mockTime = $this->createMock(\DateTime::class);

        $builder = new AtomBuilder($mockAtom);
        $builder->addEntry('testId', 'testTitle', $mockTime);
    }

    public function testPublishInitializesGenerator(): void
    {
        $this->mockGenerator->expects($this->once())
            ->method('initialize');

        $this->mockAtom->expects($this->once())
            ->method('getFeedElement')
            ->willReturn($this->mockFeedEntry);

        $atom = new AtomBuilder($this->mockAtom, $this->mockGenerator);
        $atom->publish();
    }

    public function testPublishAddsEntryElementsToGenerator(): void
    {
        $entryA = $this->createMock(EntryInterface::class);
        $entryB = $this->createMock(EntryInterface::class);

        $this->mockAtom->expects($this->once())
            ->method('getEntryElements')
            ->willReturn([$entryA, $entryB]);

        $this->mockGenerator->expects($this->exactly(2))
            ->method('addEntry')
            ->withConsecutive([$entryA], [$entryB]);

        $atom = new AtomBuilder($this->mockAtom, $this->mockGenerator);
        $atom->publish();
    }

    public function testPublishCallsGeneratorGenerateMethodToReturnString(): void
    {
        $this->mockGenerator->expects($this->once())
            ->method('generate');

        $atom = new AtomBuilder($this->mockAtom, $this->mockGenerator);
        $atom->publish();
    }
}
