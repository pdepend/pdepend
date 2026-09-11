<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2026 Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright 2008-2026 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace PDepend;

use PDepend\Source\Tokenizer\Token;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Test case for the worker wire format.
 *
 * @copyright 2008-2017 Manuel Pichler. All rights reserved.
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
#[CoversClass(WorkerProtocol::class)]
#[Group('unittest')]
class WorkerProtocolTest extends TestCase
{
    public function testUnframeReturnsThePayload(): void
    {
        $buffer = WorkerProtocol::frame('payload');

        static::assertSame('payload', WorkerProtocol::unframe($buffer));
        static::assertSame('', $buffer);
    }

    public function testUnframeWaitsForTheRestOfThePayload(): void
    {
        $frame = WorkerProtocol::frame('payload');
        $buffer = substr($frame, 0, -3);

        static::assertNull(WorkerProtocol::unframe($buffer));

        $buffer .= substr($frame, -3);

        static::assertSame('payload', WorkerProtocol::unframe($buffer));
    }

    public function testUnframeHandlesBinaryPayloadsAndBackToBackFrames(): void
    {
        $payload = random_bytes(512) . "\n\n" . random_bytes(512);
        $buffer = WorkerProtocol::frame($payload) . WorkerProtocol::frame('second');

        static::assertSame($payload, WorkerProtocol::unframe($buffer));
        static::assertSame('second', WorkerProtocol::unframe($buffer));
        static::assertNull(WorkerProtocol::unframe($buffer));
    }

    public function testUnframeRejectsACorruptStream(): void
    {
        $buffer = 'PHP Warning: something went wrong';

        $this->expectException(RuntimeException::class);

        WorkerProtocol::unframe($buffer);
    }

    public function testPackedTokensSurviveTheRoundTrip(): void
    {
        $tokens = [$this->token('<?php'), $this->token('class'), $this->token('Aä€')];

        $blob = WorkerProtocol::packTokens(['node' => $tokens]);
        [$offset, $length] = WorkerProtocol::indexTokens($blob)['node'];

        static::assertEquals($tokens, WorkerProtocol::unpackTokens($blob, $offset, $length));
    }

    public function testNestedStreamsAreWrittenOnce(): void
    {
        $unit = [$this->token('<?php'), $this->token('class'), $this->token('{'), $this->token('}')];
        $inner = [$unit[1], $unit[2]];

        $blob = WorkerProtocol::packTokens(['unit' => $unit, 'inner' => $inner]);
        $index = WorkerProtocol::indexTokens($blob);

        // The inner stream points into the stream already written for the unit.
        $innerStart = $index['inner'][0];
        static::assertGreaterThan($index['unit'][0], $innerStart);
        static::assertLessThan($index['unit'][0] + $index['unit'][1], $innerStart);

        static::assertEquals($inner, WorkerProtocol::unpackTokens($blob, ...$index['inner']));
        static::assertEquals($unit, WorkerProtocol::unpackTokens($blob, ...$index['unit']));
    }

    public function testStreamsThatAreNoSliceKeepTheirOwnCopy(): void
    {
        $unit = [$this->token('<?php'), $this->token('class')];
        $unrelated = [$this->token('function')];

        $blob = WorkerProtocol::packTokens(['unit' => $unit, 'unrelated' => $unrelated]);
        $index = WorkerProtocol::indexTokens($blob);

        static::assertEquals($unrelated, WorkerProtocol::unpackTokens($blob, ...$index['unrelated']));
        static::assertEquals($unit, WorkerProtocol::unpackTokens($blob, ...$index['unit']));
    }

    public function testTokensOfSeveralNodesStayApart(): void
    {
        $first = [$this->token('a'), $this->token('b')];
        $second = [$this->token('c')];

        $blob = WorkerProtocol::packTokens(['first' => $first, 'second' => $second]);
        $index = WorkerProtocol::indexTokens($blob);

        static::assertEquals($first, WorkerProtocol::unpackTokens($blob, ...$index['first']));
        static::assertEquals($second, WorkerProtocol::unpackTokens($blob, ...$index['second']));
    }

    private function token(string $image): Token
    {
        return new Token(42, $image, 1, 2, 3, 4);
    }
}
