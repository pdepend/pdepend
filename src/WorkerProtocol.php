<?php

/**
 * This file is part of PDepend.
 *
 * PHP Version 8
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
use RuntimeException;

/**
 * Messages are length prefixed rather than newline delimited.
 */
final class WorkerProtocol
{
    private const MAGIC = "PDW\x01";
    private const FRAME_HEADER_LENGTH = 8;
    private const SECTION_BODY = 'B';
    private const SECTION_ALIAS = 'A';
    private const BODY_HEADER_LENGTH = 8;
    private const ALIAS_HEADER_LENGTH = 12;
    private const TOKEN_HEADER_LENGTH = 24;

    /**
     * Wraps a payload in a frame.
     */
    public static function frame(string $payload): string
    {
        return self::MAGIC . pack('N', strlen($payload)) . $payload;
    }

    /**
     * Removes and returns the next complete frame from the given buffer, or
     * returns null while the buffer does not hold a full frame yet.
     *
     * @throws RuntimeException
     */
    public static function unframe(string &$buffer): ?string
    {
        if (strlen($buffer) < self::FRAME_HEADER_LENGTH) {
            return null;
        }

        if (!str_starts_with($buffer, self::MAGIC)) {
            throw new RuntimeException(sprintf(
                'Corrupt worker stream, expected a frame header but got "%s".',
                substr($buffer, 0, 32),
            ));
        }

        /** @var array{1: int} */
        $header = unpack('N', $buffer, strlen(self::MAGIC));
        $length = $header[1];

        if (strlen($buffer) < self::FRAME_HEADER_LENGTH + $length) {
            return null;
        }

        $payload = substr($buffer, self::FRAME_HEADER_LENGTH, $length);
        $buffer = substr($buffer, self::FRAME_HEADER_LENGTH + $length);

        return $payload;
    }

    /**
     * @param array<string, array<int, Token>> $tokensById
     */
    public static function packTokens(array $tokensById): string
    {
        // Longest first, so a stream is always written before the nested streams that turn out to be slices of it.
        uasort($tokensById, static fn(array $a, array $b): int => count($b) <=> count($a));

        $blob = '';

        /** @var array<int, array{int, int}> */
        $written = [];

        foreach ($tokensById as $id => $tokens) {
            $id = (string) $id;
            $slice = self::locateSlice($tokens, $written);

            if ($slice !== null) {
                $blob .= self::SECTION_ALIAS . pack('N3', strlen($id), $slice[0], $slice[1]) . $id;

                continue;
            }

            $body = '';
            $bodyOffsets = [];
            foreach ($tokens as $token) {
                $packed = pack(
                    'N6',
                    $token->type,
                    $token->startLine,
                    $token->endLine,
                    $token->startColumn,
                    $token->endColumn,
                    strlen($token->image),
                ) . $token->image;

                $bodyOffsets[spl_object_id($token)] = [strlen($body), strlen($packed)];
                $body .= $packed;
            }

            $header = self::SECTION_BODY . pack('N2', strlen($id), strlen($body)) . $id;
            $bodyStart = strlen($blob) + strlen($header);
            foreach ($bodyOffsets as $objectId => [$relative, $packedLength]) {
                $written[$objectId] = [$bodyStart + $relative, $packedLength];
            }

            $blob .= $header . $body;
        }

        return $blob;
    }

    /**
     * @return array<string, array{int, int}>
     * @throws RuntimeException
     */
    public static function indexTokens(string $blob): array
    {
        $index = [];
        $offset = 0;
        $length = strlen($blob);

        while ($offset < $length) {
            $kind = $blob[$offset];
            $offset++;

            if ($kind === self::SECTION_BODY) {
                /** @var array{1: int, 2: int} */
                $header = unpack('N2', $blob, $offset);
                $offset += self::BODY_HEADER_LENGTH;

                $id = substr($blob, $offset, $header[1]);
                $offset += $header[1];

                $index[$id] = [$offset, $header[2]];
                $offset += $header[2];

                continue;
            }

            if ($kind !== self::SECTION_ALIAS) {
                throw new RuntimeException(sprintf('Unknown token section "%s".', $kind));
            }

            /** @var array{1: int, 2: int, 3: int} */
            $header = unpack('N3', $blob, $offset);
            $offset += self::ALIAS_HEADER_LENGTH;

            $index[substr($blob, $offset, $header[1])] = [$header[2], $header[3]];
            $offset += $header[1];
        }

        return $index;
    }

    /**
     * Returns the offset and length of an already written stream that holds
     * exactly these tokens, or null when they need a copy of their own.
     *
     * @param array<int, Token> $tokens
     * @param array<int, array{int, int}> $written
     * @return array{int, int}|null
     */
    private static function locateSlice(array $tokens, array $written): ?array
    {
        if ($tokens === []) {
            return null;
        }

        $start = null;
        $expected = 0;

        foreach ($tokens as $token) {
            $entry = $written[spl_object_id($token)] ?? null;

            if ($entry === null || ($start !== null && $entry[0] !== $expected)) {
                return null;
            }

            $start ??= $entry[0];
            $expected = $entry[0] + $entry[1];
        }

        return [$start, $expected - $start];
    }

    /**
     * @return list<Token>
     */
    public static function unpackTokens(string $blob, int $offset, int $length): array
    {
        $tokens = [];
        $end = $offset + $length;

        while ($offset < $end) {
            /** @var array{1: int, 2: int, 3: int, 4: int, 5: int, 6: int} */
            $header = unpack('N6', $blob, $offset);
            $offset += self::TOKEN_HEADER_LENGTH;

            $tokens[] = new Token(
                $header[1],
                substr($blob, $offset, $header[6]),
                $header[2],
                $header[3],
                $header[4],
                $header[5],
            );

            $offset += $header[6];
        }

        return $tokens;
    }
}
