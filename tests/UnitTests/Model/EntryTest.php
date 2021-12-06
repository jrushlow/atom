<?php

/**
 * Copyright 2020 Jesse Rushlow - Geeshoe Development.
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

namespace RushlowDevelopment\Atom\UnitTests\Model;

use RushlowDevelopment\Atom\Collection\ElementCollection;
use RushlowDevelopment\Atom\Contract\CollectionInterface;
use RushlowDevelopment\Atom\Exception\ModelException;
use RushlowDevelopment\Atom\Model\Entry;
use RushlowDevelopment\Atom\UnitTests\AbstractModelTest;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 *
 * @internal
 */
final class EntryTest extends AbstractModelTest
{
    public array $expected = [];

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->classUnderTest = Entry::class;
        $this->getExpected();
    }

    public function requiredPropertyPerRFCDataProvider(): \Generator
    {
        yield 'Author' => ['author'];
        yield 'Id' => ['id'];
        yield 'Title' => ['title'];
        yield 'Updated' => ['updated'];
    }

    public function optionalPropertyPerRFCDataProvider(): \Generator
    {
        yield 'Category' => ['category'];
        yield 'Content' => ['content'];
        yield 'Contributor' => ['contributor'];
        yield 'Link' => ['link'];
        yield 'Published' => ['published'];
        yield 'Rights' => ['rights'];
        yield 'Source' => ['source'];
        yield 'Summary' => ['summary'];
    }

    /**
     * @return array<array>
     */
    public function getterDataProvider(): array
    {
        $this->getExpected();

        return [
            ['getId', $this->expected['id']],
            ['getTitle', $this->expected['title']],
            ['getUpdated', $this->expected['updated']],
        ];
    }

    /**
     * @dataProvider getterDataProvider
     *
     * @param mixed $expected
     */
    public function testGetters(string $methodName, $expected): void
    {
        $entry = new Entry($this->expected['id'], $this->expected['title'], $this->expected['updated']);

        self::assertEquals($expected, $entry->$methodName());
    }

    /**
     * @return array<array>
     */
    public function exceptionDataProvider(): array
    {
        $this->getExpected();

        return [
            ['getId', 'Id', ['', $this->expected['title'], $this->expected['updated']]],
            ['getTitle', 'Title', [$this->expected['id'], '', $this->expected['updated']]],
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testGetterExceptions(
        string $methodName,
        string $expectedMsg,
        array $constructorParams
    ): void {
        $entry = new Entry(...$constructorParams);

        $this->expectException(ModelException::class);
        $this->expectExceptionMessage("$expectedMsg value is empty or uninitialized");

        $entry->$methodName();
    }

    public function optionalElementGetterSetters(): array
    {
        return [
            ['getAuthor', 'setAuthor', $this->createMock(CollectionInterface::class)],
        ];
    }

    /**
     * @dataProvider optionalElementGetterSetters
     *
     * @param mixed $expected
     */
    public function testOptionalEntryGetterSetters(string $getter, string $setter, $expected): void
    {
        $params = [];
        foreach ($this->expected as $value) {
            $params[] = $value;
        }

        $entry = new Entry(...$params);

        $entry->$setter($expected);

        self::assertSame($expected, $entry->$getter());
    }

    public function testConstructorCreatesCollectionForAuthor(): void
    {
        $entry = new Entry('', '', $this->expected['updated']);

        self::assertInstanceOf(ElementCollection::class, $entry->getAuthor());
    }

    protected function getExpected(): void
    {
        $time = $this->createMock(\DateTime::class);

        $this->expected = [
            'id' => 'test_id',
            'title' => 'test_title',
            'updated' => $time,
        ];
    }
}
