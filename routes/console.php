<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Result;
use App\Models\PracticeResult;
use App\Services\AiSuggestionParser;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ai:backfill-parsed', function () {
    /** @var AiSuggestionParser $parser */
    $parser = app(AiSuggestionParser::class);
    $version = $parser->version();

    $examCount = 0;
    Result::query()
        ->whereNotNull('ai_suggestion')
        ->where(function ($q) {
            $q->whereNull('ai_parsed_json')->orWhereNull('ai_parser_version');
        })
        ->orderBy('id')
        ->chunkById(200, function ($rows) use ($parser, $version, &$examCount) {
            foreach ($rows as $row) {
                $row->forceFill([
                    'ai_parsed_json' => $parser->parse($row->ai_suggestion),
                    'ai_parser_version' => $version,
                ])->save();
                $examCount++;
            }
        }, 'id');

    $practiceCount = 0;
    PracticeResult::query()
        ->whereNotNull('ai_suggestion')
        ->where(function ($q) {
            $q->whereNull('ai_parsed_json')->orWhereNull('ai_parser_version');
        })
        ->orderBy('id')
        ->chunkById(200, function ($rows) use ($parser, $version, &$practiceCount) {
            foreach ($rows as $row) {
                $row->forceFill([
                    'ai_parsed_json' => $parser->parse($row->ai_suggestion),
                    'ai_parser_version' => $version,
                ])->save();
                $practiceCount++;
            }
        }, 'id');

    $this->info("Backfill selesai. Exam: {$examCount}, Practice: {$practiceCount}");
})->purpose('Backfill ai_parsed_json untuk hasil AI lama');
