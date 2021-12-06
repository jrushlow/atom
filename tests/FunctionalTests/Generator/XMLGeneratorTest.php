<?php

declare(strict_types=1);

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

namespace RushlowDevelopment\Atom\FunctionalTests\Generator;

use PHPUnit\Framework\TestCase;
use RushlowDevelopment\Atom\Generator\XMLGenerator;
use RushlowDevelopment\Atom\Model\Entry;
use RushlowDevelopment\Atom\Model\Feed;

/**
 * @author Jesse Rushlow <jr@rushlow.dev>
 *
 * @internal
 */
class XMLGeneratorTest extends TestCase
{
    public const TEST_TIME = '2019-12-31T18:30:02+00:00';

    public ?\DateTimeImmutable $time;
    public ?Feed $feed;

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->time = new \DateTimeImmutable(self::TEST_TIME);
        $this->feed = new Feed('http://geeshoe.com/', 'Functional Title', $this->time);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->time = null;
        $this->feed = null;
    }

    /**
     * @return array<array> [['xml', bool]]
     */
    public function xmlResultDataProvider(): array
    {
        $notPretty = <<<'EOT'
            <?xml version="1.0" encoding="UTF-8"?>
            <feed xmlns="http://www.w3.org/2005/Atom" id="feed"><id>http://geeshoe.com/</id>
            EOT;

        $notPretty .= '<title>Functional Title</title><updated>2019-12-31T18:30:02+00:00</updated></feed>'."\n";

        $pretty = <<<'EOT'
            <?xml version="1.0" encoding="UTF-8"?>
            <feed xmlns="http://www.w3.org/2005/Atom" id="feed">
              <id>http://geeshoe.com/</id>
              <title>Functional Title</title>
              <updated>2019-12-31T18:30:02+00:00</updated>
            </feed>

            EOT;

        return [
            'Not pretty XML' => [false, $notPretty],
            'Pretty XML' => [true, $pretty],
        ];
    }

    /**
     * @dataProvider xmlResultDataProvider
     */
    public function testGetXMLResultReturnsExpectedAtomXMLString(bool $pretty, string $expected): void
    {
        $generator = new XMLGenerator(null, null, null, $pretty);
        $generator->initialize($this->feed);

        $result = $generator->generate();

        $this->assertSame($expected, $result);
    }

    public function testAddEntryAppendsFeedElement(): void
    {
        $generator = new XMLGenerator(null, null, null, false);
        $generator->initialize($this->feed);

        $entry = new Entry('https://geeshoe.com', 'Entry Title 1', $this->time);
        $generator->addEntry($entry);

        $result = $generator->generate();

        //@TODO Refactor with a more elegant solution
        $xml = $this->xmlResultDataProvider();
        $entryXML = '<entry><id>https://geeshoe.com</id><title>Entry Title 1</title>'
            .'<updated>2019-12-31T18:30:02+00:00</updated></entry>';

        $haystack = $xml['Not pretty XML'][1];
        $position = strrpos($haystack, '</feed>');

        self::assertIsInt($position);
        $entryXML .= substr($haystack, $position);

        $expected = substr_replace(
            $haystack,
            $entryXML,
            $position
        );

        $this->assertSame($expected, $result);
    }
}
